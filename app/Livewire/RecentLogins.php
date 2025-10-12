<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LoginLog;

class RecentLogins extends Component
{
    public $recentLogins = [];

    public function mount()
    {
        $this->loadRecentLogins();
    }

    public function loadRecentLogins()
    {
        \Log::info('RecentLogins refreshed at ' . now());

        $this->recentLogins = LoginLog::with('user')
            ->orderBy('logged_in_at', 'desc')
            ->get()
            ->unique('user_id');
    }

    public function render()
    {
        return view('livewire.recent-logins');
    }
}
