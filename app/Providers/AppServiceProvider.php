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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in Production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register Activity Log for Logins
        Event::listen(
            Login::class,
            [LogSuccessfulLogin::class, 'handle']
        );
    }
}
