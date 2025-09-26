<?php

namespace App\Livewire;

use App\Models\Request;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use App\Models\ResearchProject;

class Templates extends Component
{

    public $showModal = false;
    public $currentFile;
    public $currentFileType;

    public $purpose;

    public $templates;

    public function mount()
    {
        $this->templates = ResearchProject::all();
    }

    public function requestACM($id)
    {
        // dd($this->purpose . ' ' . $id);

        $existing = Request::where('user_id', auth()->id())
                          ->where('research_project_id', $id)
                          ->first();
        
        if ($existing) {
            $this->purpose = '';
            $this->modal('previewFile'.$id)->close();
            Toaster::warning('You have already requested this project.');
            return;
        }

        Request::create([
            'user_id' => auth()->id(),
            'research_project_id' => $id,
            'purpose' => $this->purpose,
            'status' => 'pending',
        ]);

        Toaster::success('Request Success');
        $this->modal('previewFile'.$id)->close();
        $this->purpose = '';
    }

    public function render()
    {
        return view('livewire.templates');
    }
}
