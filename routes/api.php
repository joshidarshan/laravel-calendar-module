<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportApiController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('report/table', [ReportApiController::class, 'tableData']);
    Route::get('report/chart', [ReportApiController::class, 'chartData']);
});