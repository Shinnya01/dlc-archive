<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;

class LogUserLogin
{
    public function handle(Login $event)
    {
        // if ($event->user->role === 'user') {
        //     LoginLog::create([
        //         'user_id' => $event->user->id,
        //     ]);
        // }
    }
}
