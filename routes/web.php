<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

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

// ca-EN
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/giving', [GivingController::class, 'index'])->name('giving');

// ca-FR
Route::get('/apropos', [AboutController::class, 'index'])->name('apropos');
Route::get('/evenements', [EventController::class, 'index'])->name('evenements');
Route::get('/donner', [GivingController::class, 'index'])->name('donner');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

// ca-EN
Route::get('/view-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');
// ca-FR
    Route::get('/visionner-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');

// Bible Books and Study Series
Route::get('/bible-books', [BibleBookController::class, 'index'])->name('bible-books');
Route::get('/study-series', [StudySeriesController::class, 'index'])->name('study-series');

// Bible Studies
// ca-EN
Route::get('/bible-studies', [BibleStudyController::class, 'index'])->name('bible-studies');
Route::get('/bible-studies/create', [BibleStudyController::class, 'create'])->name('bible-studies.create');
Route::post('/bible-studies/store', [BibleStudyController::class, 'store'])->name('bible-studies.store');
Route::get('/bible-studies/{id}', [BibleStudyController::class, 'show'])->name('bible-studies.show');

// ca-FR
Route::get('/etudes-bibliques', [BibleStudyController::class, 'index'])->name('etudes-bibliques');
Route::get('/etudes-bibliques/create', [BibleStudyController::class, 'create'])->name('etudes-bibliques.creer');
Route::post('/bible-studies/store', [BibleStudyController::class, 'store'])->name('etudes-bibliques.sauvarder');
Route::get('/bible-studies/{id}', [BibleStudyController::class, 'show'])->name('etudes-bibliques.visionner');

// Authentication Routes

// ca-EN
// Route::get('/register', [RegisterController::class, 'register'])->name('register'); // Shows the form
// Route::post('/register', [RegisterController::class, 'store'])->name('register.store'); // Handles form submission

Route::get('/login', [LoginController::class, 'login'])->name('login'); // Shows the login form
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate'); // Handles login submission

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ca-FR
// Route::get('/enregister', [RegisterController::class, 'register'])->name('enregistrer'); // Shows the form
// Route::post('/enregister', [RegisterController::class, 'store'])->name('enregistrer.sauvgarder'); // Handles form submission

Route::get('/connexion', [LoginController::class, 'login'])->name('connexion'); // Shows the login form
Route::post('/connexion', [LoginController::class, 'authenticate'])->name('connexion.authentifier'); // Handles login submission

Route::post('/deconnexion', [LoginController::class, 'logout'])->name('deconnexion');

// User Dashboard
Route::middleware('auth')->group(function () {
    Route::get('/user-dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('user-dashboard');
    Route::get('/tableau-utilisateur', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('tableau-utilisateur');
});

// Migrations -- Comment out when not in use.
/*
Route::get('/fresh-migrations-xyz', function () {
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

Route::get('/run-migrations-xyz', function () {
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
*/