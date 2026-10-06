<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $emailInput = $request->input('email');
            $email = is_string($emailInput)
                ? hash('sha256', Str::lower(substr($emailInput, 0, 255)))
                : 'invalid';

            return [
                Limit::perMinute(30)->by('login-ip:'.$request->ip()),
                Limit::perMinute(5)->by('login-user:'.$email.'|'.$request->ip()),
            ];
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $emailInput = $request->input('email');
            $email = is_string($emailInput)
                ? hash('sha256', Str::lower(substr($emailInput, 0, 255)))
                : 'invalid';

            return [
                Limit::perMinute(20)->by('password-reset-ip:'.$request->ip()),
                Limit::perMinute(5)->by('password-reset-user:'.$email.'|'.$request->ip()),
            ];
        });
    }
}
