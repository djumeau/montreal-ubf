<?php

namespace App\Http\Controllers;

use App\Models\BibleStudy;
use App\Models\StudyAttachment;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudyAttachmentController extends Controller
{
    // @desc Show a PDF in the browser or download a DOCX, counting the view (Admin / Elder previews don't count)
    // @route GET /documents/{attachment}
    public function show(Request $request, StudyAttachment $attachment): StreamedResponse
    {
        $disk = Storage::disk('local');
        $path = $attachment->storage_path;

        abort_unless($disk->exists($path), 404);

        if (! $request->user()?->canManageRoles()) {
            $attachment->increment('views');
        }

        return $attachment->extension === 'pdf'
            ? $disk->response($path, $attachment->name_with_extension)
            : $disk->download($path, $attachment->name_with_extension);
    }

    // @desc Upload one or more attachments (PDF / DOCX) to a Bible study
    // @route POST /manage-studies/{study}/attachments
    public function store(Request $request, BibleStudy $study): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        // Named error bag so validation errors reopen the Attachments modal
        $validated = $request->validateWithBag('uploadAttachments', [
            'locale' => ['required', Rule::in(StudyAttachment::LOCALES)],
            'type' => ['required', Rule::in(StudyAttachment::TYPES)],
            'files' => ['required', 'array'],
            'files.*' => ['file', 'mimes:' . implode(',', StudyAttachment::EXTENSIONS), 'max:20480'], // 20 MB each
        ]);

        $directory = $study->documentDirectory($validated['locale']);

        foreach ($validated['files'] as $file) {
            [$filename, $extension] = $this->safeName($file, $validated['type'], $validated['locale']);

            // Files keep their uploaded name; the same name in the same language replaces the file and keeps its view count
            $file->storeAs($directory, "{$filename}.{$extension}", 'local');

            $study->attachments()->updateOrCreate(
                ['locale' => $validated['locale'], 'filename' => $filename, 'extension' => $extension],
                ['type' => $validated['type']],
            );
        }

        return back()
            ->with('status', trans_choice('dashboard/index.attachments_uploaded', count($validated['files']), ['count' => count($validated['files'])]))
            ->with('attachments_study', $study->id);
    }

    // @desc Delete one attachment and its file
    // @route DELETE /manage-studies/attachments/{attachment}
    public function destroy(Request $request, StudyAttachment $attachment): RedirectResponse
    {
        // Double layer check at the controller endpoint
        if (!$request->user()->canManageRoles()) {
            abort(403, __('home/index.unauthorized'));
        }

        Storage::disk('local')->delete($attachment->storage_path);

        $name = $attachment->name_with_extension;
        $studyId = $attachment->bible_study_id;
        $attachment->delete();

        return back()
            ->with('status', __('dashboard/index.attachment_deleted', ['name' => $name]))
            ->with('attachments_study', $studyId);
    }

    // Markers added before the extension so file names tell types and languages apart:
    // jn_01.01-18.q.pdf, jn_01.01-18.lec.pdf, jn_01.01-18.q.fr.pdf (type first, then language)
    private const TYPE_SUFFIXES = ['question_sheet' => 'q', 'lecture' => 'lec'];
    private const LOCALE_SUFFIXES = ['fr_CA' => 'fr'];

    /**
     * Uploaded file name split into [name, extension], lower case and safe for any file system,
     * with single-digit numbers zero-padded and the type / language markers added:
     * "JN 1.1-18.PDF" uploaded as a French question sheet becomes ["jn_01.01-18.q.fr", "pdf"].
     */
    private function safeName(UploadedFile $file, string $type, string $locale): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $name = strtolower(Str::ascii(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))); // "Leçon" -> "lecon"

        $name = preg_replace('/\s+/', '_', $name);          // Spaces become underscores
        $name = preg_replace('/[^a-z0-9._-]/', '', $name);  // Drop anything else (brackets, quotes...)
        $name = preg_replace('/_{2,}/', '_', $name);        // "a _ b" -> "a_b", not "a___b"
        $name = trim($name, '_-.');

        if ($name === '') {
            $name = 'attachment-' . now()->timestamp;
        }

        // Numbers 1-9 get a leading zero so files sort and read in order: "jn_1.1-18" -> "jn_01.01-18"
        $name = preg_replace('/(?<!\d)([1-9])(?!\d)/', '0$1', $name);

        // Language marker goes last, so take it off first: "jn_01.01-18.fr" -> "jn_01.01-18"
        $localeSuffix = self::LOCALE_SUFFIXES[$locale] ?? null;
        if ($localeSuffix) {
            $name = preg_replace('/\.' . $localeSuffix . '$/', '', $name);
        }

        // Add ".q" / ".lec" unless the name already has it (e.g. "jn_01.01-18.q")
        $typeSuffix = self::TYPE_SUFFIXES[$type] ?? null;
        if ($typeSuffix && !preg_match('/\.' . $typeSuffix . '(\.|$)/', $name)) {
            $name .= ".{$typeSuffix}";
        }

        if ($localeSuffix) {
            $name .= ".{$localeSuffix}";
        }

        return [$name, $extension];
    }
}
