<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class RiwayatAktivitas extends Component
{
    use WithPagination;

    public $search = '';

    public $actionFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActionFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::with('user.kantor')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($qu) {
                        $qu->where('name', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->actionFilter) {
            $query->where('action', $this->actionFilter);
        }

        // Admin scope: only see logs from their territory if not DPN
        $userAuth = auth()->user();
        if ($userAuth->tingkatan !== 'DPN') {
            $query->whereHas('user', function ($q) use ($userAuth) {
                $q->where('kantor_id', $userAuth->kantor_id);
            });
        }

        $logs = $query->paginate(15);
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('livewire.admin.riwayat-aktivitas', [
            'logs' => $logs,
            'actions' => $actions,
        ])->layout('components.layouts.app', ['title' => 'Riwayat Aktivitas']);
    }
}
