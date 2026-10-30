<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Partner;
use App\Services\Notifications\Channels\PushChannel;
use App\Services\Notifications\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Sistem Araçları — push/AI/zamanlayıcı teşhisi ve kendine test bildirimi.
 */
class SystemToolsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $fcmPath = (string) config('services.fcm.credentials');

        $myDevices = 0;
        $allDevices = 0;
        try {
            $myDevices = $user->deviceTokens()->count();
            $allDevices = DeviceToken::count();
        } catch (\Throwable $e) {
            // tablo yoksa 0
        }

        $demoPartners = null;
        try {
            if (Schema::hasTable('partners') && Schema::hasColumn('partners', 'is_demo')) {
                $demoPartners = Partner::where('is_demo', true)->count();
            }
        } catch (\Throwable $e) {
            $demoPartners = null;
        }

        return view('admin.tools.index', [
            'fcmOk' => $fcmPath !== '' && is_file($fcmPath),
            'aiOk' => filled(config('services.anthropic.key')),
            'schedule' => MatchCenterController::systemStatus(),
            'myDevices' => $myDevices,
            'allDevices' => $allDevices,
            'demoPartners' => $demoPartners,
        ]);
    }

    public function testPush(NotificationService $notifications): RedirectResponse
    {
        return $this->sendTest($notifications, 'Göztepe Tribünleri', 'Test bildirimi 💛❤️', [], 'Test bildirimi');
    }

    public function testGoal(NotificationService $notifications): RedirectResponse
    {
        return $this->sendTest($notifications, '⚽ GOOOOL GÖZTEPE! 💛❤️', 'Test: Göztepe 1-0 · 23’', ['kanal' => 'goztepe_gol'], 'Gol bildirimi');
    }

    private function sendTest(NotificationService $notifications, string $title, string $body, array $data, string $what): RedirectResponse
    {
        $user = auth()->user();
        $devices = $user->deviceTokens()->count();
        $fcmPath = (string) config('services.fcm.credentials');
        $fcmOk = $fcmPath !== '' && is_file($fcmPath);

        PushChannel::$lastReport = [];
        try {
            $notifications->send($user, $title, $body, null, 'sistem', ['push'], $data);
        } catch (\Throwable $e) {
            return back()->with('status', $what.' gönderilemedi: '.$e->getMessage());
        }

        $lines = PushChannel::$lastReport;
        if ($devices === 0) {
            $msg = $what.': hesabınıza kayıtlı cihaz yok. Telefonunuzda mobil uygulamaya bu hesapla en az bir kez giriş yapın.';
        } elseif (! $fcmOk) {
            $msg = $what.': FCM servis hesabı dosyası bulunamadı, bildirim yalnızca kuyruğa kaydedildi (cihaza gitmedi).';
        } elseif (! $lines) {
            $msg = $what.': gönderim denendi ama cihaz sonucu dönmedi (bildirim tercihi kapalı olabilir; laravel.log [Push] satırlarına bakın).';
        } else {
            $msg = $what.' gönderildi ('.$devices.' cihaz) → '.implode(' | ', $lines);
        }

        return back()->with('status', $msg);
    }
}
