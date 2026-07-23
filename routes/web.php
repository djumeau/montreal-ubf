<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SwitchLanguageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

Route::get('/view-pdf', function () {
    $path = 'questionnaires/nt/john_2026/jn_07.53-08.11.q.pdf';

    // Check if the file is actually there
    if (!Storage::disk('public')->exists($path)) {
        return 'File not found at storage/app/public/' . $path;
    }

    // Stream the file straight to the browser
    return Storage::disk('public')->response($path);
});