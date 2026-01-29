<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskAssignmentController;

// Task Assignments Routes
Route::prefix('assignments')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TaskAssignmentController::class, 'dashboard'])->name('assignments.dashboard');

    // CRUD operations
    Route::get('/', [TaskAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/create', [TaskAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/', [TaskAssignmentController::class, 'store'])->name('assignments.store');
    
    Route::get('/{assignment}', [TaskAssignmentController::class, 'show'])->name('assignments.show');
    Route::get('/{assignment}/edit', [TaskAssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/{assignment}', [TaskAssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/{assignment}', [TaskAssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Status actions
    Route::patch('/{assignment}/start', [TaskAssignmentController::class, 'start'])->name('assignments.start');
    Route::patch('/{assignment}/complete', [TaskAssignmentController::class, 'complete'])->name('assignments.complete');
    Route::patch('/{assignment}/hold', [TaskAssignmentController::class, 'hold'])->name('assignments.hold');
    Route::patch('/{assignment}/cancel', [TaskAssignmentController::class, 'cancel'])->name('assignments.cancel');
    Route::patch('/{assignment}/progress', [TaskAssignmentController::class, 'updateProgress'])->name('assignments.updateProgress');

    // API endpoints
    Route::get('/task/{taskId}/assignments', [TaskAssignmentController::class, 'getTaskAssignments']);
    Route::get('/user/{user}/assignments', [TaskAssignmentController::class, 'getUserAssignments']);
    Route::get('/assignments/recent/data', [TaskAssignmentController::class, 'recentData'])
    ->name('assignments.recent.data');
});
