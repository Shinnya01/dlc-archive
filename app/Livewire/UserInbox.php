<?php

namespace App\Livewire;

use App\Models\History;
use App\Models\Request;
use Livewire\Component;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Storage;

#[Title('Inbox')]
class UserInbox extends Component
{
    public $requests;
    public $archives;

    public function mount()
    {
        $this->fetchRequests();
    }
    
    public function downloadRequest($id)
    {
        $request = \App\Models\Request::find($id);

        if (!$request) {
            \Masmerise\Toaster\Toaster::error('Request not found.');
            return;
        }

        $file = str_replace('public/', '', $request->pdf_path);
        if (!\Storage::disk('public')->exists($file)) {
            \Masmerise\Toaster\Toaster::error('File not found.');
            return;
        }

        // 🟢 Log to History
        History::create([
            'user_id' => auth()->id(),
            'detail' => 'Download',
        ]);

        return \Storage::disk('public')->download($file);
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
