<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarTaskController;

Route::get('/calendar', [CalendarTaskController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarTaskController::class, 'events'])->name('calendar.events');
Route::get('/calendar/search', [CalendarTaskController::class, 'search'])->name('calendar.search');
Route::post('/calendar', [CalendarTaskController::class, 'store'])->name('calendar.store');
Route::put('/calendar/{id}', [CalendarTaskController::class, 'update'])->name('calendar.update');
Route::post('/calendar/{id}/complete', [CalendarTaskController::class, 'complete'])->name('calendar.complete');
Route::post('/calendar/{id}/uncomplete', [CalendarTaskController::class, 'uncomplete'])->name('calendar.uncomplete');
Route::delete('/calendar/{id}', [CalendarTaskController::class, 'destroy'])->name('calendar.destroy');

    