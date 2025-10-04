<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;

#[Title('Inbox')]
class UserInbox extends Component
{
    public $requests;
    public $archives;

    public function mount()
    {
        $this->fetchRequests();
    }

    public function fetchRequests()
    {
        $this->requests = Request::where('user_id', auth()->user()->id)
                                ->where('status', '!=' , 'deleted')
                                ->get();

        $this->archives = Request::where('user_id', auth()->user()->id)
                                ->where('status' , 'deleted')
                                ->get();
        
    }

    public function restoreRequest($id)
    {
        $archive = Request::find($id);
        $archive->status = 'approved';
        $archive->save();
        $this->fetchRequests();
        $this->modal('archive')->close();
        Toaster::success('File Restored Successfully!');
    }

    public function deleteRequest($id)
    {
        $request = Request::find($id);
        $request->status = 'deleted';
        $request->save();
        $this->fetchRequests();
        Toaster::success('Reequest Deleted Successfully!');
    }

    public function render()
    {
        return view('livewire.user-inbox');
    }
}
