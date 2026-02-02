<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // JSON report for dashboard
    Route::get('/employee-report', [ReportController::class, 'employeeReport']);

    Route::get('/employee-report/download', [ReportController::class, 'downloadExcel'])
        ->name('employee.report.download');
});
// Login
Route::post('/login', [AuthController::class, 'login']);
