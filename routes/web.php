<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.tasks', TaskController::class)
        ->only(['store', 'update', 'destroy'])
        ->whereNumber('task'); // A nasty bug occured because I did not specify that a task identification should be a number!
    Route::delete('/projects/{project}/tasks/bulk-destroy',
        [TaskController::class, 'bulkDestroy'])
        ->name('projects.tasks.bulkDestroy');
    Route::resource('tags', TagsController::class);

    Route::resource('projects.tasks.subtasks', SubtaskController::class)
        ->only(['store', 'update', 'destroy']);
    Route::post(
        '/projects/{project}/tasks/{task}/reminder', [TaskController::class, 'sendReminder']
    )->name('projects.tasks.reminder');

    Route::get('/archived-tasks', [TaskController::class, 'showArchivedTasks'])
        ->name('show.archived.tasks');
    Route::patch('/archived-tasks/{task}/uncomplete', [TaskController::class, 'uncompleteArchivedTask'])
        ->name('archived-tasks.uncomplete');

});

require __DIR__.'/auth.php';
