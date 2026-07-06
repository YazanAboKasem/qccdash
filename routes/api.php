<?php

use App\Http\Controllers\Api\V1\AdminAuthController;
use App\Http\Controllers\Api\V1\AdminCampaignController;
use App\Http\Controllers\Api\V1\AdminDeviceController;
use App\Http\Controllers\Api\V1\AdminSurveyController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SurveyController;
use App\Http\Controllers\Api\V1\SyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes – V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ─── Public (no auth) ───────────────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'authenticate']);
    Route::post('/admin/login', [AdminAuthController::class, 'login']);

    // ─── Device-authenticated routes ────────────────────────────
    Route::middleware('device.auth')->group(function () {
        Route::get('/auth/verify', [AuthController::class, 'verify']);

        // Surveys (device-facing)
        Route::get('/surveys/active', [SurveyController::class, 'active']);
        Route::get('/surveys/{uuid}', [SurveyController::class, 'show']);

        // Response submission
        Route::post('/responses', [SyncController::class, 'submit']);
        Route::post('/sync/batch', [SyncController::class, 'batchSync']);
    });

    // ─── Admin routes (Sanctum-authenticated) ───────────────────
    Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

        // Auth
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);

        // Dashboard & Reports
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/activity', [DashboardController::class, 'recentActivity']);
        Route::get('/surveys/{survey}/stats', [DashboardController::class, 'surveyStats']);
        Route::get('/surveys/{survey}/distribution', [DashboardController::class, 'answerDistribution']);
        Route::get('/surveys/{survey}/trend', [DashboardController::class, 'dailyTrend']);
        Route::get('/surveys/{survey}/hourly', [DashboardController::class, 'hourlyDistribution']);
        Route::get('/surveys/{survey}/export', [DashboardController::class, 'export']);

        // Campaigns
        Route::apiResource('campaigns', AdminCampaignController::class);
        Route::patch('/campaigns/{campaign}/status', [AdminCampaignController::class, 'updateStatus']);

        // Surveys
        Route::apiResource('surveys', AdminSurveyController::class);
        Route::post('/surveys/{survey}/duplicate', [AdminSurveyController::class, 'duplicate']);

        // Questions (nested under survey)
        Route::post('/surveys/{survey}/questions', [AdminSurveyController::class, 'addQuestion']);
        Route::put('/questions/{question}', [AdminSurveyController::class, 'updateQuestion']);
        Route::delete('/questions/{question}', [AdminSurveyController::class, 'deleteQuestion']);
        Route::post('/surveys/{survey}/questions/reorder', [AdminSurveyController::class, 'reorderQuestions']);
        Route::patch('/questions/{question}/toggle', [AdminSurveyController::class, 'toggleQuestion']);

        // Devices
        Route::apiResource('devices', AdminDeviceController::class);
        Route::post('/devices/{device}/regenerate-token', [AdminDeviceController::class, 'regenerateToken']);
        Route::patch('/devices/{device}/status', [AdminDeviceController::class, 'updateStatus']);
    });
});
