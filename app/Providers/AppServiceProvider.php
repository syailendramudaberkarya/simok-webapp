<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\KtpScanner\OcrSpaceClient::class, function ($app) {
            return new \App\Services\KtpScanner\OcrSpaceClient(
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
    }
}
