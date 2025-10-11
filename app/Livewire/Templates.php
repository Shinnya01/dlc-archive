<?php

namespace App\Livewire;

use App\Models\History;
use App\Models\Request;
use Livewire\Component;
use Livewire\Attributes\Title;
use Masmerise\Toaster\Toaster;
use App\Models\ResearchProject;

#[Title('Templates')]
class Templates extends Component
{

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



        // $existing = Request::where('user_id', auth()->id())
        //                 ->where('research_project_id', $id)
        //                 ->exists();

       
        // if ($existing) {
        //     $this->purpose = '';
        //     $this->modal('previewFile'.$id)->close();
        //     Toaster::warning('You have already requested this project.');
        //     return;
        // }

        Request::create([
            'user_id' => auth()->id(),
            'research_project_id' => $id,
            'purpose' => $this->purpose,
            'status' => 'pending',
        ]);

        History::create([
            'user_id' => Auth()->id(),
            'detail' => 'ACM Request',
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
