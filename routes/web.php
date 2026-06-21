<?php

use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FixtureController as AdminFixtureController;
use App\Http\Controllers\Admin\HistoryEventController as AdminHistoryEventController;
use App\Http\Controllers\Admin\LegendController as AdminLegendController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\MembershipFeatureController as AdminMembershipFeatureController;
use App\Http\Controllers\Admin\MembershipPlanController as AdminMembershipPlanController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\SponsorController as AdminSponsorController;
use App\Http\Controllers\Admin\StudentVerificationController as AdminStudentVerificationController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Member\CampaignController as MemberCampaignController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\FixtureController as MemberFixtureController;
use App\Http\Controllers\Member\NewsController as MemberNewsController;
use App\Http\Controllers\Member\NotificationController as MemberNotificationController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Member\SeffafKasaController as MemberSeffafKasaController;
use App\Http\Controllers\Member\StudentVerificationController as MemberStudentVerificationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SeffafKasaController;
use Illuminate\Support\Facades\Route;

// --- Genel kamuya açık site ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/haberler', [NewsController::class, 'index'])->name('haberler.index');
Route::get('/haberler/{post}', [NewsController::class, 'show'])->name('haberler.show');
Route::get('/hakkimizda', [PageController::class, 'hakkimizda'])->name('hakkimizda');
Route::get('/sanli-tarihimiz', [PageController::class, 'sanliTarihimiz'])->name('sanli-tarihimiz');
Route::get('/uyelik-avantajlari', [PageController::class, 'uyelikAvantajlari'])->name('uyelik.avantajlar');
Route::get('/taraftar-sesi', [PageController::class, 'taraftarSesi'])->name('taraftar-sesi');
Route::get('/iletisim', [PageController::class, 'iletisim'])->name('iletisim');

// Şeffaf Kasa (herkese açık)
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

    // Reklam pop-up butonu → tıklamayı say, hedefe (varsayılan bağış) git
    Route::get('/kampanya/{message}/git', [MemberCampaignController::class, 'go'])->name('kampanya.git');

    // Tüm üyelere ortak içerik alanları
    Route::get('/fikstur', [MemberFixtureController::class, 'index'])->name('fikstur');
    Route::get('/haberler', [MemberNewsController::class, 'index'])->name('haberler');
    Route::get('/seffaf-kasa', [MemberSeffafKasaController::class, 'index'])->name('seffaf-kasa');

    Route::get('/aidat', [MemberPaymentController::class, 'showAidat'])->name('aidat');
    Route::get('/bagis', [MemberPaymentController::class, 'showBagis'])->name('bagis');
    Route::post('/odeme', [MemberPaymentController::class, 'start'])->name('odeme.start');

    // Profil / iletişim bilgileri
    Route::get('/profil', [MemberProfileController::class, 'edit'])->name('profil');
    Route::post('/profil', [MemberProfileController::class, 'update'])->name('profil.update');

    // Öğrenci doğrulama (belge yükleme + durum)
    Route::get('/ogrenci-dogrulama', [MemberStudentVerificationController::class, 'show'])->name('ogrenci-dogrulama');
    Route::post('/ogrenci-dogrulama', [MemberStudentVerificationController::class, 'store'])->name('ogrenci-dogrulama.store');
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

    Route::get('/haberler', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/haberler/yeni', [AdminPostController::class, 'create'])->name('posts.create');
    Route::post('/haberler', [AdminPostController::class, 'store'])->name('posts.store');
    Route::get('/haberler/{post}/duzenle', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::put('/haberler/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::delete('/haberler/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    // Web Yönetimi
    Route::get('/slider', [AdminSliderController::class, 'index'])->name('sliders.index');
    Route::get('/slider/yeni', [AdminSliderController::class, 'create'])->name('sliders.create');
    Route::post('/slider', [AdminSliderController::class, 'store'])->name('sliders.store');
    Route::get('/slider/{slider}/duzenle', [AdminSliderController::class, 'edit'])->name('sliders.edit');
    Route::put('/slider/{slider}', [AdminSliderController::class, 'update'])->name('sliders.update');
    Route::delete('/slider/{slider}', [AdminSliderController::class, 'destroy'])->name('sliders.destroy');

    Route::get('/sponsorlar', [AdminSponsorController::class, 'index'])->name('sponsors.index');
    Route::get('/sponsorlar/yeni', [AdminSponsorController::class, 'create'])->name('sponsors.create');
    Route::post('/sponsorlar', [AdminSponsorController::class, 'store'])->name('sponsors.store');
    Route::get('/sponsorlar/{sponsor}/duzenle', [AdminSponsorController::class, 'edit'])->name('sponsors.edit');
    Route::put('/sponsorlar/{sponsor}', [AdminSponsorController::class, 'update'])->name('sponsors.update');
    Route::delete('/sponsorlar/{sponsor}', [AdminSponsorController::class, 'destroy'])->name('sponsors.destroy');

    // Fikstür / Maçlar (ana sayfa "Haftanın Maçı")
    Route::get('/maclar', [AdminFixtureController::class, 'index'])->name('fixtures.index');
    Route::get('/maclar/yeni', [AdminFixtureController::class, 'create'])->name('fixtures.create');
    Route::post('/maclar', [AdminFixtureController::class, 'store'])->name('fixtures.store');
    Route::get('/maclar/{fixture}/duzenle', [AdminFixtureController::class, 'edit'])->name('fixtures.edit');
    Route::put('/maclar/{fixture}', [AdminFixtureController::class, 'update'])->name('fixtures.update');
    Route::delete('/maclar/{fixture}', [AdminFixtureController::class, 'destroy'])->name('fixtures.destroy');

    // Şanlı Tarihimiz (zaman tüneli + foto galeri)
    Route::get('/sanli-tarihimiz', [AdminHistoryEventController::class, 'index'])->name('history.index');
    Route::get('/sanli-tarihimiz/yeni', [AdminHistoryEventController::class, 'create'])->name('history.create');
    Route::post('/sanli-tarihimiz', [AdminHistoryEventController::class, 'store'])->name('history.store');
    Route::get('/sanli-tarihimiz/{history}/duzenle', [AdminHistoryEventController::class, 'edit'])->name('history.edit');
    Route::put('/sanli-tarihimiz/{history}', [AdminHistoryEventController::class, 'update'])->name('history.update');
    Route::delete('/sanli-tarihimiz/{history}', [AdminHistoryEventController::class, 'destroy'])->name('history.destroy');

    // Efsaneler (Şanlı Tarihimiz futbolcular)
    Route::get('/efsaneler', [AdminLegendController::class, 'index'])->name('legends.index');
    Route::get('/efsaneler/yeni', [AdminLegendController::class, 'create'])->name('legends.create');
    Route::post('/efsaneler', [AdminLegendController::class, 'store'])->name('legends.store');
    Route::get('/efsaneler/{legend}/duzenle', [AdminLegendController::class, 'edit'])->name('legends.edit');
    Route::put('/efsaneler/{legend}', [AdminLegendController::class, 'update'])->name('legends.update');
    Route::delete('/efsaneler/{legend}', [AdminLegendController::class, 'destroy'])->name('legends.destroy');

    Route::get('/site-ayarlari', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('/site-ayarlari', [AdminSettingController::class, 'update'])->name('settings.update');

    // Üyelik planları + avantaj tablosu (panelden düzenlenebilir)
    Route::get('/uyelik-planlari', [AdminMembershipPlanController::class, 'index'])->name('membership.plans.index');
    Route::get('/uyelik-planlari/{plan}/duzenle', [AdminMembershipPlanController::class, 'edit'])->name('membership.plans.edit');
    Route::put('/uyelik-planlari/{plan}', [AdminMembershipPlanController::class, 'update'])->name('membership.plans.update');

    Route::get('/uyelik-avantajlari', [AdminMembershipFeatureController::class, 'index'])->name('membership.features.index');
    Route::get('/uyelik-avantajlari/yeni', [AdminMembershipFeatureController::class, 'create'])->name('membership.features.create');
    Route::post('/uyelik-avantajlari', [AdminMembershipFeatureController::class, 'store'])->name('membership.features.store');
    Route::get('/uyelik-avantajlari/{feature}/duzenle', [AdminMembershipFeatureController::class, 'edit'])->name('membership.features.edit');
    Route::put('/uyelik-avantajlari/{feature}', [AdminMembershipFeatureController::class, 'update'])->name('membership.features.update');
    Route::delete('/uyelik-avantajlari/{feature}', [AdminMembershipFeatureController::class, 'destroy'])->name('membership.features.destroy');

    Route::get('/uyeler', [AdminMemberController::class, 'index'])->name('members.index');
    Route::patch('/uyeler/{member}', [AdminMemberController::class, 'update'])->name('members.update');

    // Öğrenci doğrulama talepleri (onay / red)
    Route::get('/ogrenci-dogrulama', [AdminStudentVerificationController::class, 'index'])->name('student-verifications.index');
    Route::get('/ogrenci-dogrulama/{verification}/belge', [AdminStudentVerificationController::class, 'document'])->name('student-verifications.document');
    Route::patch('/ogrenci-dogrulama/{verification}', [AdminStudentVerificationController::class, 'update'])->name('student-verifications.update');

    Route::get('/bildirim-gonder', [AdminNotificationController::class, 'create'])->name('notifications.create');
    Route::post('/bildirim-gonder', [AdminNotificationController::class, 'send'])->name('notifications.send');

    Route::get('/kampanyalar', [AdminCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/kampanyalar/yeni', [AdminCampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/kampanyalar', [AdminCampaignController::class, 'store'])->name('campaigns.store');
    Route::patch('/kampanyalar/{campaign}/durum', [AdminCampaignController::class, 'toggle'])->name('campaigns.toggle');
});
