<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class AdminAccounts extends Component
{
    public $admins;

    public function mount()
    {
        $this->admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.admin-accounts');
    }
}
