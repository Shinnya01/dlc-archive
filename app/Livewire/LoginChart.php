<?php

namespace App\Livewire;

use Livewire\Component;

class LoginChart extends Component
{
    public $labels = [];
    public $data = [];

    public function mount()
    {
        // Dummy data: Last 7 days login counts
        $this->labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $this->data = [5, 10, 7, 8, 12, 3, 6];
    }

    public function render()
    {
        return view('livewire.login-chart');
    }
}
