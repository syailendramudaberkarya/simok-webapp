<?php

namespace App\Livewire\Admin;

use App\Models\Anggota;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalAnggota;
    public $menungguPersetujuan;
    public $disetujui;
    public $ditolak;
    public $baruBulanIni;
    
    // For Chart.js
    public $chartDataLabels = [];
    public $chartDataValues = [];
    
    // Recent registrations
    public $pendaftaranTerbaru;

    public function mount()
    {
        $this->loadStats();
        $this->loadChartData();
        $this->pendaftaranTerbaru = Anggota::scoped(auth()->user())->latest()->take(5)->get();
    }

    private function loadStats()
    {
        $this->totalAnggota = Anggota::scoped(auth()->user())->count();
        $this->menungguPersetujuan = Anggota::scoped(auth()->user())->where('status', 'menunggu')->count();
        $this->disetujui = Anggota::scoped(auth()->user())->where('status', 'disetujui')->count();
        $this->ditolak = Anggota::scoped(auth()->user())->where('status', 'ditolak')->count();
        $this->baruBulanIni = Anggota::scoped(auth()->user())->whereMonth('created_at', Carbon::now()->month)
                                     ->whereYear('created_at', Carbon::now()->year)
                                     ->count();
    }

    private function loadChartData()
    {
        // Get registrations grouped by month for the last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }

        $data = Anggota::scoped(auth()->user())->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as total'))
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($months as $month) {
            $this->chartDataLabels[] = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y');
            $this->chartDataValues[] = $data->get($month, 0);
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Administrasi']);
    }
}
