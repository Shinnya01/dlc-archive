<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ManageUsers extends Component
{
    public $users;

    public function mount()
    {
        $this->users = User::where('role', 'user')->get();
    }
    
    public function render()
    {
        return view('livewire.manage-users');
    }
}
