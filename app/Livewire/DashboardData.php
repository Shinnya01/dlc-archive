<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\ResearchProject;

class DashboardData extends Component
{
    public $userCount;
    public $projectCount;
    public $name;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->updateCounts();
    }

    public function updateCounts()
    {
        \Log::info('User refresh triggered at ' . now());
        $this->userCount = User::where('role', 'user')->count();
        $this->projectCount = ResearchProject::count();
    }

    public function render()
    {
        return view('livewire.dashboard-data');
    }
}
