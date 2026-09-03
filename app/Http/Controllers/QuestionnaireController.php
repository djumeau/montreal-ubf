<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuestionnaireController extends Controller
{
    // @desc Show questionnaire PDF
    // @route GET /
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
