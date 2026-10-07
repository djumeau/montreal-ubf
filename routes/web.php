<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;

use App\Http\Controllers\StudySeriesController;

use App\Http\Controllers\BibleStudyController;
use App\Http\Controllers\StudyAttachmentController;

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ConfidentialityPolicyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventAttachmentController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PrayerTopicsController;
use App\Http\Controllers\StudyScheduleController;

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
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/giving', [GivingController::class, 'index'])->name('giving');

// fr_CA
Route::get('/apropos', [AboutController::class, 'index'])->name('apropos');
Route::get('/evenements', [EventController::class, 'index'])->name('evenements');
Route::get('/evenements/{event}', [EventController::class, 'show'])->name('evenements.show');
Route::get('/donner', [GivingController::class, 'index'])->name('donner');
Route::get('/confidentialite', [ConfidentialityPolicyController::class, 'index'])->name('confidentialite');

Route::get('/language/{locale}', [SwitchLanguageController::class, 'setLocale'])->name('locale');

// Bible study attachments (question sheets, lectures...) kept in private storage
Route::get('/documents/{attachment}', [StudyAttachmentController::class, 'show'])->name('attachments.show');

// Event attachments (documents, media) kept in private storage; access follows the event's minimum profile
Route::get('/event-documents/{attachment}', [EventAttachmentController::class, 'show'])->name('event-attachments.show');

// Bible Studies
// en_CA
Route::get('/bible-studies', [BibleStudyController::class, 'index'])->name('bible-studies');
Route::get('/bible-studies/create', [BibleStudyController::class, 'create'])->name('bible-studies.create');
Route::post('/bible-studies/store', [BibleStudyController::class, 'store'])->name('bible-studies.store');
Route::get('/bible-studies/{id}', [BibleStudyController::class, 'show'])->name('bible-studies.show');

// fr_CA
Route::get('/etudes-bibliques', [BibleStudyController::class, 'index'])->name('etudes-bibliques');
Route::get('/etudes-bibliques/creer', [BibleStudyController::class, 'create'])->name('etudes-bibliques.creer');
Route::post('/etudes-bibliques/sauvegarder', [BibleStudyController::class, 'store'])->name('etudes-bibliques.sauvegarder');
Route::get('/etudes-bibliques/{id}', [BibleStudyController::class, 'show'])->name('etudes-bibliques.visionner');

// Bible Study Schedule
Route::get('/bible-study-schedule', [StudyScheduleController::class, 'index'])->name('bible-study-schedule');
Route::get('/horaire-etudes-bibliques', [StudyScheduleController::class, 'index'])->name('horaire-etudes-bibliques');

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

    Route::get('/manage-schedule', [StudyScheduleController::class, 'manage'])->name('manage-schedule');

    Route::get('/manage-inquiries', [InquiryController::class, 'index'])->name('manage-inquiries');

    Route::get('/manage-prayer-topics', [PrayerTopicsController::class, 'index'])->name('manage-prayer-topics');

    // Manage Prayer Topics actions - Add, Edit, Move up / down, Delete
    Route::post('/manage-prayer-topics', [PrayerTopicsController::class, 'store'])->name('prayer-topics.store');
    Route::put('/manage-prayer-topics/{prayerTopic}', [PrayerTopicsController::class, 'update'])->name('prayer-topics.update');
    Route::put('/manage-prayer-topics/{prayerTopic}/move', [PrayerTopicsController::class, 'move'])->name('prayer-topics.move');
    Route::delete('/manage-prayer-topics/{prayerTopic}', [PrayerTopicsController::class, 'destroy'])->name('prayer-topics.destroy');

    // Manage Inquiries actions - Delete the ticked ones (Delete Selected), Delete one
    Route::delete('/manage-inquiries', [InquiryController::class, 'destroySelected'])->name('inquiries.destroy-selected');
    Route::delete('/manage-inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
    Route::post('/manage-inquiries/{inquiry}/read', [InquiryController::class, 'markRead'])->name('inquiries.read');
    Route::put('/manage-inquiries/{inquiry}/answer', [InquiryController::class, 'answer'])->name('inquiries.answer');
    Route::put('/manage-inquiries/{inquiry}/unanswer', [InquiryController::class, 'unanswer'])->name('inquiries.unanswer');

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

    // Manage Schedule actions - Add, Edit, Copy the week's recurring events to the following week
    Route::post('/manage-schedule', [StudyScheduleController::class, 'store'])->name('schedule.store');
    Route::post('/manage-schedule/copy-week', [StudyScheduleController::class, 'copyWeek'])->name('schedule.copy-week');
    Route::put('/manage-schedule/{event}', [StudyScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('/manage-schedule/{event}', [StudyScheduleController::class, 'destroy'])->name('schedule.destroy');

    // Manage Schedule attachments and images actions - Upload, Delete
    Route::post('/manage-schedule/{event}/attachments', [EventAttachmentController::class, 'store'])->name('event-attachments.store');
    Route::delete('/manage-schedule/attachments/{attachment}', [EventAttachmentController::class, 'destroy'])->name('event-attachments.destroy');
    Route::post('/manage-schedule/{event}/images', [EventAttachmentController::class, 'storeImages'])->name('event-images.store');
    Route::delete('/manage-schedule/{event}/images/{type}', [EventAttachmentController::class, 'destroyImage'])->name('event-images.destroy');

    // fr_CA
    Route::get('/tableau', [DashboardController::class, 'index'])->name('tableau');

    Route::get('/gerer-utilisateurs', [DashboardController::class, 'manageUsers'])->name('gerer-utilisateurs');

    Route::get('/gerer-serie', [DashboardController::class, 'manageSeries'])->name('gerer-serie');

    Route::get('/gerer-etudes', [DashboardController::class, 'manageStudies'])->name('gerer-etudes');

    Route::get('/gerer-horaire', [StudyScheduleController::class, 'manage'])->name('gerer-horaire');

    Route::get('/gerer-demandes', [InquiryController::class, 'index'])->name('gerer-demandes');

    Route::get('/gerer-sujets-de-priere', [PrayerTopicsController::class, 'index'])->name('gerer-sujets-de-priere');

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

// Migration status (read-only): lists each migration as Ran / Pending
Route::get('/migration-status', function () {
    try {
        Artisan::call('migrate:status');
        return response('<pre>' . e(Artisan::output()) . '</pre>');
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth');

// Migrations -- Comment out when not in use.

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
})->middleware('auth');

// Seeds only the prayer_topics table with the sample topics; the other tables are untouched
Route::get('/run-prayer-topic-seeder', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'PrayerTopicSeeder', '--force' => true]);
        return response('<pre>' . e(Artisan::output()) . '</pre>');
    } catch (\Exception $e) {
        return response('Failed: ' . $e->getMessage(), 500);
    }
})->middleware('auth');

/*
// Study storage repair -- Comment out when not in use.
// /repair-storage previews, ?apply=1 copies missing files from the old folders, ?cleanup=1 removes leftovers.

Route::get('/repair-storage', function () {
    Artisan::call('study-storage:repair', [
        '--apply' => request()->boolean('apply'),
        '--cleanup' => request()->boolean('cleanup'),
    ]);

    return response('<pre>' . e(Artisan::output()) . '</pre>');
})->middleware('auth');

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

// Seeds only the events and event_attachments tables (replaces the events there); users and studies are untouched
Route::get('/run-event-seeder', function () {
    try {
        Artisan::call('db:seed', ['--class' => 'EventSeeder', '--force' => true]);
        return response('<pre>' . e(Artisan::output()) . '</pre>');
    } catch (\Exception $e) {
        return response('Failed: ' . $e->getMessage(), 500);
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