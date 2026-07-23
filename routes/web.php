<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SwitchLanguageController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

Route::get('/clear-everything-safely', function () {
    // 1. Clear view caches (crucial for Vite manifest updates)
    Artisan::call('view:clear');
    
    // 2. Clear route, config, and application cache
    Artisan::call('optimize:clear');
    
    return 'All Laravel caches have been cleared successfully!';
});