<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QuestionnaireController;

use App\Http\Controllers\SwitchLanguageController;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

Route::get('/view-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');

Route::get('/bible-studies', [App\Http\Controllers\BibleStudyController::class, 'index'])->name('bible-studies');

Route::get('/bible-studies/{id}', [App\Http\Controllers\BibleStudyController::class, 'show'])->name('bible-studies.show');