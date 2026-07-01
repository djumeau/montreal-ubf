<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/language/{locale}', function($locale) {

    if (in_array($locale, ['en', 'fr']))
        Session::put('Locale', $locale);

    return redirect()->back();

})->name('locale');