<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\BibleBookController;

use App\Http\Controllers\StudySeriesController;

use App\Http\Controllers\BibleStudyController;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QuestionnaireController;

use App\Http\Controllers\SwitchLanguageController;

use App\Http\Controllers\GivingController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// en_CA
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/giving', [GivingController::class, 'index'])->name('giving');

// fr_CA
Route::get('/apropos', [AboutController::class, 'index'])->name('apropos');
Route::get('/evenements', [EventController::class, 'index'])->name('evenements');
Route::get('/donner', [GivingController::class, 'index'])->name('donner');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

// en_CA
Route::get('/view-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');
// fr_CA
    Route::get('/visionner-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');

// Bible Books and Study Series
Route::get('/bible-books', [BibleBookController::class, 'index'])->name('bible-books');
Route::get('/study-series', [StudySeriesController::class, 'index'])->name('study-series');

// Bible Studies
// en_CA
Route::get('/bible-studies', [BibleStudyController::class, 'index'])->name('bible-studies');
Route::get('/bible-studies/create', [BibleStudyController::class, 'create'])->name('bible-studies.create');
Route::post('/bible-studies/store', [BibleStudyController::class, 'store'])->name('bible-studies.store');
Route::get('/bible-studies/{id}', [BibleStudyController::class, 'show'])->name('bible-studies.show');

// fr_CA
Route::get('/etudes-bibliques', [BibleStudyController::class, 'index'])->name('etudes-bibliques');
Route::get('/etudes-bibliques/create', [BibleStudyController::class, 'create'])->name('etudes-bibliques.creer');
Route::post('/bible-studies/store', [BibleStudyController::class, 'store'])->name('etudes-bibliques.sauvarder');
Route::get('/bible-studies/{id}', [BibleStudyController::class, 'show'])->name('etudes-bibliques.visionner');

// Authentication Routes

// en_CA
Route::get('/login', [LoginController::class, 'login'])->name('login'); // Shows the login form
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate'); // Handles login submission

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// en_CA
// Route::get('/enregister', [RegisterController::class, 'register'])->name('enregistrer'); // Shows the form
// Route::post('/enregister', [RegisterController::class, 'store'])->name('enregistrer.sauvgarder'); // Handles form submission

Route::get('/connexion', [LoginController::class, 'login'])->name('connexion'); // Shows the login form
Route::post('/connexion', [LoginController::class, 'authenticate'])->name('connexion.authentifier'); // Handles login submission

Route::post('/deconnexion', [LoginController::class, 'logout'])->name('deconnexion');

// User Dashboard
Route::middleware('auth')->group(function () {

    //Dashboard related routes
    // en_CA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // fr_CA
    Route::get('/tableau', [DashboardController::class, 'index'])->name('tableau');

    // Profile related routes - Avatar, User name and User Password

        // en_CA

        // Avatar related routes
        Route::get('/dashboard/avatar', [DashboardController::class, 'showAvatar'])->name('avatar.show');
        Route::put('/dashboard/update-avatar', [DashboardController::class, 'updateAvatar'])->name('avatar.update');

        // Password related routes
        Route::put('/dashboard/update-password', [DashboardController::class, 'updatePassword'])->name('password.update');

    // User Management routes
    // Route::put('/dashboard/add-user', [DashboardController::class, 'addUser'])->name('add.user');

});

// Migrations -- Comment out when not in use.

Route::get('/reset-migrations', function () {
    try {
        //1. clear config cache
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        //2. rollback migrations
        Artisan::call('migrate:reset', ['--force' => true]);
        return 'Success: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/fresh-migrations', function () {
    try {
        //1. clear config cache
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        //2. rollback migrations
        Artisan::call('migrate:fresh', ['--force' => true]);
        return 'Success: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/run-migrations', function () {
    try {
        //1. clear config cache
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        //2. run migrations
        Artisan::call('migrate', ['--force' => true]);
        return 'Success: ' . Artisan::output();
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/run-seeders', function () {

    try {
        // Force the seeder to run
        Artisan::call('db:seed', ['--force' => true]);

        // Fetch the raw error output if the artisan command caught it internally
        return response('<pre>' . Artisan::output() . '</pre>');
    } catch (\Exception $e) {
        // This will print the exact SQL error MySQL is throwing out
        return response('Failed: ' . $e->getMessage(), 500);
    }

});