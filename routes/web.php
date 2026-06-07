<?php

use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\NotificationController as MemberNotificationController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\SeffafKasaController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('seffaf-kasa'));

// Şeffaf Kasa (herkese açık vitrin)
Route::get('/seffaf-kasa', [SeffafKasaController::class, 'index'])->name('seffaf-kasa');

// --- Misafir (giriş yapmamış) ---
Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/giris', [AuthController::class, 'login']);
    Route::get('/uye-ol', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/uye-ol', [AuthController::class, 'register']);
});

Route::post('/cikis', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- Üye alanı ---
Route::middleware('auth')->prefix('panel')->name('uye.')->group(function () {
    Route::get('/', [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bildirimler', [MemberNotificationController::class, 'index'])->name('bildirimler');

    Route::get('/aidat', [MemberPaymentController::class, 'showAidat'])->name('aidat');
    Route::get('/bagis', [MemberPaymentController::class, 'showBagis'])->name('bagis');
    Route::post('/odeme', [MemberPaymentController::class, 'start'])->name('odeme.start');
});

// Ödeme geri dönüşü (sağlayıcıdan döner; auth zorunlu değil)
Route::get('/odeme/callback/{payment}', [MemberPaymentController::class, 'callback'])->name('odeme.callback');

// --- Yönetim paneli ---
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/hareketler', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/hareketler/yeni', [AdminTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/hareketler', [AdminTransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/hareketler/{transaction}', [AdminTransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/uyeler', [AdminMemberController::class, 'index'])->name('members.index');
    Route::patch('/uyeler/{member}', [AdminMemberController::class, 'update'])->name('members.update');

    Route::get('/bildirim-gonder', [AdminNotificationController::class, 'create'])->name('notifications.create');
    Route::post('/bildirim-gonder', [AdminNotificationController::class, 'send'])->name('notifications.send');

    Route::get('/kampanyalar', [AdminCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/kampanyalar/yeni', [AdminCampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/kampanyalar', [AdminCampaignController::class, 'store'])->name('campaigns.store');
    Route::patch('/kampanyalar/{campaign}/durum', [AdminCampaignController::class, 'toggle'])->name('campaigns.toggle');
});
