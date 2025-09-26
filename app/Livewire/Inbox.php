<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Inbox extends Component
{
    public $requests;

    public function mount()
    {
        $this->fetchRequest();
    }

    public function approveRequest($id)
    {
        $request = Request::find($id);
        if ($request) {
            $request->status = 'approved';
            $request->save();
            $this->fetchRequest();
            Toaster::success('Request Approved');
        }
    }

    public function rejectRequest($id)
    {
        $request = Request::find($id);
        if ($request) {
            $request->delete();
            $this->fetchRequest();
            Toaster::success('Request Rejected');
        }
    }

    public function fetchRequest()
    {
        $this->requests = Request::all();
    }

    public function render()
    {
        return view('livewire.inbox');
    }
}
