<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    // @desc Show home page (index)
    // @route GET /
    public function index(): View
    {
        // The featured study under the hero is loaded by the study component (see layout.blade.php)
        return view('pages.index');
    }

    // @desc Stream an image of the home page (e.g. the hero) from private storage: storage/app/private/home
    // @route GET /private/home/{filename}
    public function streamImage(string $filename): Response
    {
        $path = 'home/' . $filename;

        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'Image not found.');
        }

        // Shown to every visitor of the home page, so browsers and proxies may cache it
        return response()->file(Storage::disk('private')->path($path), [
            'Cache-Control' => 'public, max-age=86400, no-transform',
        ]);
    }
}
