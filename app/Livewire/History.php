<?php

namespace App\Livewire;

use App\Models\History as HistoryModel;
use Livewire\Component;

class History extends Component
{
    public $search = '';
    public $date = '';

    public function refreshHistory()
    {
        \Log::info('History refreshed at ' . now());
    }


    public function render()
    {
        $histories = HistoryModel::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('detail', 'like', '%' . $this->search . '%');
            })
            ->when($this->date, function ($query) {
                $query->whereDate('created_at', $this->date);
            })
            ->latest()
            ->get();

        return view('livewire.history', [
            'histories' => $histories,
        ]);
    }
}
