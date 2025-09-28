<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;

class UserInbox extends Component
{
    public $requests;

    public function mount()
    {
        $this->requests = Request::where('user_id', auth()->user()->id)->get();
    }

    public function render()
    {
        return view('livewire.user-inbox');
    }
}
