<?php

namespace App\Http\Controllers;

use App\Models\EventAttachment;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
}
