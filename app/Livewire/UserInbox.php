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
        $request = Request::where('user_id', auth()->id())->findOrFail($id);

        // if (!Storage::exists($request->pdf_path)) {
        //     Toaster::error('File not found.');
        //     return;
        // }

        // 🟢 Show info toast
        Toaster::info('Preparing your download...');

        // 🟢 Log to History
        History::create([
            'user_id' => auth()->id(),
            'detail' => 'Download',
        ]);

        // 🟢 Dispatch event to browser (Livewire <-> JS bridge)
        // $this->dispatch('triggerDownload', Storage::url($request->pdf_path));
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
