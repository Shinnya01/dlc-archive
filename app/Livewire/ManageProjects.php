<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ResearchProject;

class ManageProjects extends Component
{
    public $projects;

    public function mount()
    {
        $this->projects = ResearchProject::all();
    }

    public function render()
    {
        return view('livewire.manage-projects');
    }
}
