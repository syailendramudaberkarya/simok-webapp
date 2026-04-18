<?php

namespace App\Livewire\Admin;

use App\Models\Kantor;
use App\Models\User;
use App\Models\Anggota;
use Livewire\Component;
use Livewire\WithPagination;

class StrukturOrganisasi extends Component
{
    use WithPagination;

    public $selectedLevel = null;
    public $search = '';

    protected $queryString = [
        'selectedLevel' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectLevel($level)
    {
        if ($this->selectedLevel === $level) {
            $this->selectedLevel = null; // Toggle off
        } else {
            $this->selectedLevel = $level;
            $this->resetPage();
            $this->search = '';
        }
    }

    public function getVisibleLevels()
    {
        $user = auth()->user();
        if ($user->tingkatan === 'DPN') {
            return ['DPN', 'DPD', 'DPC', 'PR', 'PAR'];
        } elseif ($user->tingkatan === 'DPD') {
            return ['DPC', 'PR', 'PAR'];
        } elseif ($user->tingkatan === 'DPC') {
            return ['PR', 'PAR'];
        } elseif ($user->tingkatan === 'PR') {
            return ['PAR'];
        }
        return [];
    }

    public function render()
    {
        $levels = $this->getVisibleLevels();
        $user = auth()->user();
        
        $stats = [];
        foreach ($levels as $lvl) {
            $stats[$lvl] = Kantor::scoped($user)->where('jenjang', $lvl)->count();
        }

        $listKantor = null;
        if ($this->selectedLevel && in_array($this->selectedLevel, $levels)) {
            $listKantor = Kantor::scoped($user)
                ->where('jenjang', $this->selectedLevel)
                ->when($this->search, function($q) {
                    $q->where('nama_kantor', 'like', '%' . $this->search . '%')
                      ->orWhere('alamat', 'like', '%' . $this->search . '%');
                })
                ->orderBy('nama_kantor')
                ->paginate(12);
        }

        return view('livewire.admin.struktur-organisasi', [
            'levels' => $levels,
            'stats' => $stats,
            'listKantor' => $listKantor,
        ])->layout('components.layouts.app', ['title' => 'Struktur Organisasi - SiMOK']);
    }
}
