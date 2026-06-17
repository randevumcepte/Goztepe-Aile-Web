<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SeffafKasaController;
use App\Http\Controllers\Api\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Api\Member\FixtureController as MemberFixtureController;
use App\Http\Controllers\Api\Member\NewsController as MemberNewsController;
use App\Http\Controllers\Api\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Api\Member\StudentVerificationController as MemberStudentVerificationController;
use App\Http\Controllers\Api\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\FixtureController as AdminFixtureController;
use App\Http\Controllers\Api\Admin\HistoryEventController as AdminHistoryEventController;
use App\Http\Controllers\Api\Admin\LegendController as AdminLegendController;
use App\Http\Controllers\Api\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Api\Admin\MembershipFeatureController as AdminMembershipFeatureController;
use App\Http\Controllers\Api\Admin\MembershipPlanController as AdminMembershipPlanController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\PostController as AdminPostController;
use App\Http\Controllers\Api\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Api\Admin\SponsorController as AdminSponsorController;
use App\Http\Controllers\Api\Admin\StudentVerificationController as AdminStudentVerificationController;
use App\Http\Controllers\Api\Admin\TransactionController as AdminTransactionController;
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

    // --- Üye alanı (mobil) ---
    Route::get('/uye/ozet', [MemberDashboardController::class, 'index']);
    Route::get('/uye/fikstur', [MemberFixtureController::class, 'index']);
    Route::get('/uye/haberler', [MemberNewsController::class, 'index']);
    Route::get('/uye/haberler/{post}', [MemberNewsController::class, 'show']);
    Route::get('/uye/profil', [MemberProfileController::class, 'show']);
    Route::post('/uye/profil', [MemberProfileController::class, 'update']);
    Route::get('/uye/ogrenci-dogrulama', [MemberStudentVerificationController::class, 'show']);
    Route::post('/uye/ogrenci-dogrulama', [MemberStudentVerificationController::class, 'store']);

    // --- Yönetim paneli (yalnız staff roller) ---
    Route::middleware('staff')->prefix('admin')->group(function () {
        Route::get('/ozet', [AdminDashboardController::class, 'index']);

        // Kasa hareketleri
        Route::get('/hareketler', [AdminTransactionController::class, 'index']);
        Route::get('/hareketler/form', [AdminTransactionController::class, 'create']);
        Route::post('/hareketler', [AdminTransactionController::class, 'store']);
        Route::delete('/hareketler/{transaction}', [AdminTransactionController::class, 'destroy']);

        // Haberler
        Route::get('/haberler', [AdminPostController::class, 'index']);
        Route::get('/haberler/form', [AdminPostController::class, 'create']);
        Route::post('/haberler', [AdminPostController::class, 'store']);
        Route::post('/haberler/{post}', [AdminPostController::class, 'update']);
        Route::delete('/haberler/{post}', [AdminPostController::class, 'destroy']);

        // Slider
        Route::get('/slider', [AdminSliderController::class, 'index']);
        Route::post('/slider', [AdminSliderController::class, 'store']);
        Route::post('/slider/{slider}', [AdminSliderController::class, 'update']);
        Route::delete('/slider/{slider}', [AdminSliderController::class, 'destroy']);

        // Sponsorlar
        Route::get('/sponsorlar', [AdminSponsorController::class, 'index']);
        Route::post('/sponsorlar', [AdminSponsorController::class, 'store']);
        Route::post('/sponsorlar/{sponsor}', [AdminSponsorController::class, 'update']);
        Route::delete('/sponsorlar/{sponsor}', [AdminSponsorController::class, 'destroy']);

        // Maçlar / fikstür
        Route::get('/maclar', [AdminFixtureController::class, 'index']);
        Route::post('/maclar', [AdminFixtureController::class, 'store']);
        Route::post('/maclar/{fixture}', [AdminFixtureController::class, 'update']);
        Route::delete('/maclar/{fixture}', [AdminFixtureController::class, 'destroy']);

        // Şanlı tarihimiz
        Route::get('/sanli-tarihimiz', [AdminHistoryEventController::class, 'index']);
        Route::post('/sanli-tarihimiz', [AdminHistoryEventController::class, 'store']);
        Route::post('/sanli-tarihimiz/{history}', [AdminHistoryEventController::class, 'update']);
        Route::delete('/sanli-tarihimiz/{history}', [AdminHistoryEventController::class, 'destroy']);

        // Efsaneler
        Route::get('/efsaneler', [AdminLegendController::class, 'index']);
        Route::post('/efsaneler', [AdminLegendController::class, 'store']);
        Route::post('/efsaneler/{legend}', [AdminLegendController::class, 'update']);
        Route::delete('/efsaneler/{legend}', [AdminLegendController::class, 'destroy']);

        // Üyelik planları
        Route::get('/uyelik-planlari', [AdminMembershipPlanController::class, 'index']);
        Route::post('/uyelik-planlari/{plan}', [AdminMembershipPlanController::class, 'update']);

        // Üyelik avantajları
        Route::get('/uyelik-avantajlari', [AdminMembershipFeatureController::class, 'index']);
        Route::post('/uyelik-avantajlari', [AdminMembershipFeatureController::class, 'store']);
        Route::post('/uyelik-avantajlari/{feature}', [AdminMembershipFeatureController::class, 'update']);
        Route::delete('/uyelik-avantajlari/{feature}', [AdminMembershipFeatureController::class, 'destroy']);

        // Üyeler
        Route::get('/uyeler', [AdminMemberController::class, 'index']);
        Route::patch('/uyeler/{member}', [AdminMemberController::class, 'update']);

        // Öğrenci doğrulama talepleri
        Route::get('/ogrenci-dogrulama', [AdminStudentVerificationController::class, 'index']);
        Route::patch('/ogrenci-dogrulama/{verification}', [AdminStudentVerificationController::class, 'update']);

        // Bildirim gönder
        Route::get('/bildirim-gonder/form', [AdminNotificationController::class, 'create']);
        Route::post('/bildirim-gonder', [AdminNotificationController::class, 'send']);

        // Kampanyalar
        Route::get('/kampanyalar', [AdminCampaignController::class, 'index']);
        Route::post('/kampanyalar', [AdminCampaignController::class, 'store']);
        Route::patch('/kampanyalar/{campaign}/durum', [AdminCampaignController::class, 'toggle']);

        // Site ayarları
        Route::get('/site-ayarlari', [AdminSettingController::class, 'show']);
        Route::post('/site-ayarlari', [AdminSettingController::class, 'update']);
    });
});
