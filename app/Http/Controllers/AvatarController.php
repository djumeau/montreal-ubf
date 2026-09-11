<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    /**
     * Return the list of available avatar files as JSON, for the picker modal.
     */
    public function library(Request $request)
    {
        $files = collect(Storage::disk('local')->files('avatar'))
            ->filter(fn ($path) => in_array(
                strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                $this->allowedExtensions
            ))
            ->map(fn ($path) => [
                'filename' => basename($path),
                'url' => route('avatar.show', basename($path)),
            ])
            ->values();

        return response()->json($files);
    }

    /**
     * Set the authenticated user's avatar to one of the library files.
     */
    public function select(Request $request)
    {
        $validated = $request->validate([
            'filename' => ['required', 'string'],
        ]);

        // basename() guards against path traversal (e.g. "../../.env")
        $filename = basename($validated['filename']);
        $path = 'avatar/' . $filename;

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (! in_array($extension, $this->allowedExtensions)) {
            return $this->fail($request, 'Unsupported file type.');
        }

        if (! Storage::disk('local')->exists($path)) {
            return $this->fail($request, 'Selected avatar file was not found.');
        }

        $user = $request->user();
        $user->update(['avatar_file' => $path]);

        if ($request->wantsJson()) {
            return response()->json(['avatar_url' => $user->avatar_url]);
        }

        return back()->with('status', 'avatar-updated');
    }

    /**
     * Stream a private avatar image to an authenticated user.
     */
    public function show(Request $request, string $filename)
    {
        $path = 'avatar/' . basename($filename);

        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    protected function fail(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->withErrors(['filename' => $message]);
    }
}