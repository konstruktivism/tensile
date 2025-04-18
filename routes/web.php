<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\MoneybirdController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('/project/{project}', [ProjectController::class, 'read'])->name('project');
    Route::get('/project/{project}/{week}', [ProjectController::class, 'viewWeek'])->name('project.viewWeek');

    Route::get('/import', [GoogleCalendarController::class, 'importEvents'])->name('import');
    Route::get('/import-weeks', [GoogleCalendarController::class, 'importWeeks'])->name('importWeeks');
});

Route::get('/login/magic', [MagicLinkController::class, 'showMagicLinkForm'])->name('login.magic');
Route::post('/login/magic', [MagicLinkController::class, 'sendMagicLink'])->name('login.magic.send');
Route::get('/magic-login', [MagicLinkController::class, 'login'])->name('magic.login');

Route::get('/api/hours-per-week', [StatsController::class, 'getHoursPerWeek']);
Route::get('/api/revenue-per-week', [StatsController::class, 'getRevenuePerWeek']);

Route::put('/moneybird/invoice/{projectId}', [MoneybirdController::class, 'updateInvoice']);

require __DIR__.'/auth.php';
