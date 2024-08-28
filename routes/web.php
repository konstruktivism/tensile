<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

// Create a new route for the projects page
Route::get('/projects', [ProjectController::class, 'index'])->name('projects');

Route::get('/project/{project}', [ProjectController::class, 'read'])->name('project');
