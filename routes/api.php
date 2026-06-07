<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SeffafKasaController;
use Illuminate\Support\Facades\Route;

// --- Public uçlar ---
Route::get('/seffaf-kasa', [SeffafKasaController::class, 'index']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Ödeme webhook (sağlayıcıdan sunucuya — auth yok, imza ile doğrulanır)
Route::post('/odeme/webhook/{payment}', [PaymentController::class, 'webhook']);

// --- Sanctum korumalı uçlar (mobil) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/cihaz', [AuthController::class, 'registerDevice']);

    // Bildirim merkezi
    Route::get('/bildirimler', [NotificationController::class, 'index']);
    Route::post('/bildirimler/{id}/okundu', [NotificationController::class, 'markRead']);
    Route::post('/bildirimler/tumu-okundu', [NotificationController::class, 'markAllRead']);
    Route::post('/bildirim-tercihi', [NotificationController::class, 'updatePreference']);

    // Ödeme
    Route::post('/odeme/baslat', [PaymentController::class, 'start']);

    // Kampanya / pop-up
    Route::get('/kampanya/aktif', [CampaignController::class, 'active']);
    Route::post('/kampanya/{message}/tikla', [CampaignController::class, 'click']);
});
