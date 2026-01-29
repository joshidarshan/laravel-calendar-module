<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CalendarTaskController;

Route::get('/calendar-tasks', [CalendarTaskController::class, 'index']);
Route::post('/calendar-tasks', [CalendarTaskController::class, 'store']);
