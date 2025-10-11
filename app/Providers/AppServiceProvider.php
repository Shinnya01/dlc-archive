<?php

namespace App\Providers;

use App\Models\LoginLog;
use App\Listeners\LogUserLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Event::listen(Login::class, function ($event) {
        // only track users with role = 'user'
        if ($event->user->role === 'user') {
            LoginLog::create([
                'user_id' => $event->user->id,
                'logged_in_at' => now(),
            ]);

        }
    });
    }
}
