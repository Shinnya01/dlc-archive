<?php

namespace App\Livewire;

use Livewire\Component;
use Masmerise\Toaster\Toaster;

class TestToast extends Component
{
    public function testToast()
    {
        Toaster::success('This is a success message!');
    }

    public function render()
    {
        return view('livewire.test-toast');
    }
}
