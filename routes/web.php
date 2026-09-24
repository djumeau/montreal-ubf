<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;

use App\Http\Controllers\BibleBookController;

use App\Http\Controllers\StudySeriesController;

use App\Http\Controllers\BibleStudyController;
use App\Http\Controllers\StudyAttachmentController;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ConfidentialityPolicyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\QuestionnaireController;

use App\Http\Controllers\SwitchLanguageController;

use App\Http\Controllers\GivingController;
use App\View\Components\ConfidentialityPolicy;

// en_CA and fr_CA
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// en_CA
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/confidentiality', [ConfidentialityPolicyController::class, 'index'])->name('confidentiality');

Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/giving', [GivingController::class, 'index'])->name('giving');

// fr_CA
Route::get('/apropos', [AboutController::class, 'index'])->name('apropos');
Route::get('/evenements', [EventController::class, 'index'])->name('evenements');
Route::get('/donner', [GivingController::class, 'index'])->name('donner');
Route::get('/confidentialite', [ConfidentialityPolicyController::class, 'index'])->name('confidentialite');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

// en_CA
Route::get('/view-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');
// fr_CA
    Route::get('/visionner-pdf/{dir}/{filename}', [QuestionnaireController::class, 'show'])
    ->where('dir', '.*') // Allows slashes inside the dir parameter
    ->name('pdf.view');

// Bible Books
Route::get('/bible-books', [BibleBookController::class, 'index'])->name('bible-books');

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

    //Dashboard related routes - Default View Personal Profile

    // en_CA
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/manage-users', [DashboardController::class, 'manageUsers'])->name('manage-users');

    Route::get('/manage-series', [DashboardController::class, 'manageSeries'])->name('manage-series');

    Route::get('/manage-studies', [DashboardController::class, 'manageStudies'])->name('manage-studies');

    // Manage Users actions - Add User, Change Role, Reset Password
    Route::post('/manage-users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/manage-users/{user}/role', [RoleController::class, 'update'])->name('users.update-role');
    Route::post('/manage-users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/manage-users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Manage Study Series actions - Add, Edit, Delete
    Route::post('/manage-series', [StudySeriesController::class, 'store'])->name('series.store');
    Route::put('/manage-series/{series}', [StudySeriesController::class, 'update'])->name('series.update');
    Route::delete('/manage-series/{series}', [StudySeriesController::class, 'destroy'])->name('series.destroy');

    // Manage Studies actions - Add, Edit, Delete
    Route::post('/manage-studies', [BibleStudyController::class, 'store'])->name('study.store');
    Route::put('/manage-studies/{study}', [BibleStudyController::class, 'update'])->name('study.update');
    Route::delete('/manage-studies/{study}', [BibleStudyController::class, 'destroy'])->name('study.destroy');

    // Manage Study Attachments actions - Upload, Delete
    Route::post('/manage-studies/{study}/attachments', [StudyAttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/manage-studies/attachments/{attachment}', [StudyAttachmentController::class, 'destroy'])->name('attachments.destroy');

    // fr_CA
    Route::get('/tableau', [DashboardController::class, 'index'])->name('tableau');

    Route::get('/gerer-utilisateurs', [DashboardController::class, 'manageUsers'])->name('gerer-utilisateurs');

    Route::get('/gerer-serie', [DashboardController::class, 'manageSeries'])->name('gerer-serie');

    Route::get('/gerer-etudes', [DashboardController::class, 'manageStudies'])->name('gerer-etudes');

    // Profile related routes - Avatar, User name and User Password

    // 2. The missing Profile Text Update Route
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update'); // <-- This fixes your current error!

    // 3. The Avatar Upload Route (from your modal form)
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar');

    // 3b. Account Deletion Route (from the Delete User confirmation modal)
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // 4. The Secure Avatar Streaming Route
    Route::get('/private/avatar/{filename}', [ProfileController::class, 'streamAvatar'])
        ->name('private.avatar');

});

// Route to clear cache and views altogether
Route::get('/clear-all', function () {
    Artisan::call('optimize:clear');
    return 'All caches and compiled views have been cleared!';
});

// Migrations -- Comment out when not in use.
/*
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

*/