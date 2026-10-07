<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttachment;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventAttachmentController extends Controller
{
    // @desc Show a PDF, image or video in the browser, or download a DOCX
    // @route GET /event-documents/{attachment}
    public function show(Request $request, EventAttachment $attachment): StreamedResponse
    {
        // Same rule as the event itself: below its minimum profile, visitors log in first (then come back here), accounts are refused
        if (! $attachment->event->isVisibleTo($request->user())) {
            $request->user() ? abort(403, __('home/index.unauthorized')) : throw new AuthenticationException();
        }

        $disk = Storage::disk('local');
        $path = $attachment->storage_path;

        abort_unless($disk->exists($path), 404);

        return str_ends_with(strtolower($attachment->document_name), '.docx')
            ? $disk->download($path, $attachment->document_name)
            : $disk->response($path, $attachment->document_name);
    }

    // @desc Upload one or more attachments to an event: documents (PDF / DOCX, in a language) or media (PNG / JPG / MP4)
    // @route POST /manage-schedule/{event}/attachments
    public function store(Request $request, Event $event): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Media have no language (shown in both); the accepted file types follow the type chosen
        $isMedia = $request->input('type') === 'media';
        $extensions = EventAttachment::EXTENSIONS[$isMedia ? 'media' : 'document'];

        // Named error bag so validation errors reopen the Attachments and Images modal
        $validated = $request->validateWithBag('uploadEventFiles', [
            'type' => ['required', Rule::in(EventAttachment::TYPES)],
            'locale' => $isMedia ? ['nullable'] : ['required', Rule::in(EventAttachment::LOCALES)],
            'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:' . implode(',', $extensions), 'max:20480'], // 20 MB each
        ]);

        $directory = $event->documentDirectory();

        foreach ($validated['files'] as $file) {
            $name = $this->safeName($file);

            // Files keep their uploaded name; the same name replaces the event's existing file (and its type and language)
            $file->storeAs($directory, $name, 'local');

            $event->attachments()->updateOrCreate(
                ['document_name' => $name],
                ['type' => $validated['type'], 'locale' => $isMedia ? null : $validated['locale']],
            );
        }

        return back()
            ->with('status', trans_choice('dashboard/manage-study-schedule/index.attachments_uploaded', count($validated['files']), ['count' => count($validated['files'])]))
            ->with('files_event', $event->id);
    }

    // @desc Delete one attachment of an event and its file
    // @route DELETE /manage-schedule/attachments/{attachment}
    public function destroy(Request $request, EventAttachment $attachment): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Events of the same category starting the same day share a folder: the file stays when another one uses it
        $event = $attachment->event;
        $event->deleteDocumentFiles([$attachment->document_name]);

        $name = $attachment->document_name;
        $attachment->delete();

        return back()
            ->with('status', __('dashboard/manage-study-schedule/index.attachment_deleted', ['name' => $name]))
            ->with('files_event', $event->id);
    }

    // @desc Upload the event's images (desktop, mobile, square); only the slots sent are replaced
    // @route POST /manage-schedule/{event}/images
    public function storeImages(Request $request, Event $event): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        $rules = [];
        foreach (Event::IMAGE_TYPES as $type) {
            $rules[$type] = ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'];
        }

        // At least one image: the message shows under the first slot
        [$first, $others] = [Event::IMAGE_TYPES[0], array_slice(Event::IMAGE_TYPES, 1)];
        array_unshift($rules[$first], 'required_without_all:' . implode(',', $others));

        $request->validateWithBag('uploadEventImages', $rules, [
            $first . '.required_without_all' => __('dashboard/manage-study-schedule/index.images_none_chosen'),
        ]);

        $images = $event->images ?? [];

        foreach (Event::IMAGE_TYPES as $type) {
            if (!$request->hasFile($type)) {
                continue;
            }

            // Remove the file being replaced to keep storage clean
            if (!empty($images[$type])) {
                $event->deleteImageFiles([$images[$type]]);
            }

            // Event id and timestamp in the name: events of the same day share the folder, and browsers don't show a cached copy of the old image
            $file = $request->file($type);
            $filename = $type . '-' . $event->id . '-' . now()->timestamp . '.' . $file->extension();

            $file->storeAs($event->imageDirectory(), $filename, 'public');

            $images[$type] = $filename;
        }

        $event->update(['images' => $images]);

        return back()
            ->with('status', __('dashboard/manage-study-schedule/index.images_saved'))
            ->with('files_event', $event->id);
    }

    // @desc Remove one of the event's images (desktop, mobile or square) and its file
    // @route DELETE /manage-schedule/{event}/images/{type}
    public function destroyImage(Request $request, Event $event, string $type): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        abort_unless(in_array($type, Event::IMAGE_TYPES, true), 404);

        $images = $event->images ?? [];

        if (!empty($images[$type])) {
            $event->deleteImageFiles([$images[$type]]);
            unset($images[$type]);
            $event->update(['images' => $images ?: null]);
        }

        return back()
            ->with('status', __('dashboard/manage-study-schedule/index.image_deleted'))
            ->with('files_event', $event->id);
    }

    /**
     * Uploaded file name, lower case and safe for any file system:
     * "Fall Conference Schedule.PDF" becomes "fall_conference_schedule.pdf" (shown as "Fall conference schedule").
     */
    private function safeName(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = strtolower(Str::ascii(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))); // "Horaire d'été" -> "horaire d'ete"

        $name = preg_replace('/\s+/', '_', $name);          // Spaces become underscores
        $name = preg_replace('/[^a-z0-9._-]/', '', $name);  // Drop anything else (brackets, quotes...)
        $name = preg_replace('/_{2,}/', '_', $name);        // "a _ b" -> "a_b", not "a___b"
        $name = trim($name, '_-.');

        if ($name === '') {
            $name = 'attachment-' . now()->timestamp;
        }

        return "{$name}.{$extension}";
    }
}
