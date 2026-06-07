<?php

namespace App\Providers;

use App\Services\Payment\FakeGateway;
use App\Services\Payment\IyzicoGateway;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Ödeme sağlayıcısı: config('services.payment.driver') ile seçilir.
        $this->app->bind(PaymentGateway::class, function () {
            $driver = config('services.payment.driver', 'fake');

            return match ($driver) {
                'iyzico' => new IyzicoGateway(
                    (string) config('services.iyzico.api_key'),
                    (string) config('services.iyzico.secret_key'),
                    (string) config('services.iyzico.base_url'),
                ),
                default => new FakeGateway(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
