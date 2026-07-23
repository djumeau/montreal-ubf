<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionnaireController extends Controller
{
    public function show(string $dir, string $filename): BinaryFileResponse
    {
        // $dir can now look like: "nt/john_2026" or "nt/medical/2026"
        $path = "questionnaires/{$dir}/{$filename}";

        // Security check: Block directory traversal attempts
        if (str_contains($filename, '..') || str_contains($dir, '..')) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Requested questionnaire does not exist.');
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
