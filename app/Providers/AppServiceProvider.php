<?php

namespace App\Providers;

use App\Listeners\LogSuccessfulLogin;
use App\Models\Anggota;
use App\Observers\AnggotaObserver;
use App\Services\KtpScanner\OcrSpaceClient;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OcrSpaceClient::class, function ($app) {
            return new OcrSpaceClient(
                config('services.ocr.api_key', '')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in Production or if current request is secure
        if ($this->app->environment('production') || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            URL::forceScheme('https');
        }

        // Register Activity Log for Logins
        Event::listen(
            Login::class,
            [LogSuccessfulLogin::class, 'handle']
        );

        Anggota::observe(AnggotaObserver::class);
    }
}
