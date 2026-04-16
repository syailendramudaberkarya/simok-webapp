<?php

namespace App\Livewire\Admin;

use App\Mail\AnggotaDisetujui;
use App\Mail\AnggotaDitolak;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\KartuAnggota;
use App\Models\KartuTemplate;
use App\Services\CardGenerationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenAnggota extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // Modal view properties
    public $detailAnggota = null;
    public $viewModalOpen = false;
    // Reject properties
    public $rejectModalOpen = false;
    public $rejectReason = '';
    public $rejectAnggotaId = null;

    // Zoom properties
    public $zoomModalOpen = false;
    public $zoomImageUrl = '';

    public function openZoomModal($path)
    {
        $this->zoomImageUrl = route('file.private', ['path' => $path]);
        $this->zoomModalOpen = true;
    }

    public function closeZoomModal()
    {
        $this->zoomModalOpen = false;
        $this->zoomImageUrl = '';
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewDetail($id)
    {
        $this->detailAnggota = Anggota::with('user')->findOrFail($id);
        $this->viewModalOpen = true;
    }

    public function closeViewModal()
    {
        $this->viewModalOpen = false;
        $this->detailAnggota = null;
    }

    public function setujui($id)
    {
        $anggota = Anggota::with('user')->findOrFail($id);

        if ($anggota->status === 'disetujui') {
            return;
        }

        DB::transaction(function () use ($anggota) {
            // New Rule: idprop + idkab + {nourut}
            $propId = $anggota->idpropinsi ?? '00';
            $kabId = $anggota->idkabupaten ?? '0000';
            
            // Find the last sequence for this specific kabupaten and ensure it matches the prefix
            $lastAnggota = Anggota::where('idkabupaten', $kabId)
                ->where('nomor_anggota', 'LIKE', $propId . $kabId . '%')
                ->whereNotNull('nomor_anggota')
                ->orderBy('nomor_anggota', 'desc')
                ->first();
            
            $nextNumber = 1;
            if ($lastAnggota) {
                // Extract last 5 digits as the sequence
                $lastNomor = $lastAnggota->nomor_anggota;
                $lastSequence = intval(substr($lastNomor, -5));
                $nextNumber = $lastSequence + 1;
            }
            
            $newNomor = $propId . $kabId . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $anggota->update([
                'status' => 'disetujui',
                'nomor_anggota' => $newNomor,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Kirim email notifikasi persetujuan
            \Illuminate\Support\Facades\Mail::to($anggota->user->email)->queue(new \App\Mail\AnggotaDisetujui($anggota));

            // Create DB record then Generate Physical PDF
            $template = KartuTemplate::where('is_active', true)->first();
            
            $kartu = KartuAnggota::create([
                'anggota_id' => $anggota->id,
                'nomor_anggota' => $newNomor,
                'template_id' => $template ? $template->id : null,
                'berlaku_hingga' => Carbon::now()->addYears(5),
            ]);

            app(CardGenerationService::class)->generate($anggota);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'approve_member',
                'description' => "Menyetujui pendaftaran anggota: {$anggota->nama_lengkap} ({$newNomor})",
                'ip_address' => request()->ip(),
            ]);

            Mail::to($anggota->user->email)->queue(new AnggotaDisetujui($anggota));
        });

        session()->flash('message', 'Anggota berhasil disetujui.');
        $this->closeViewModal();
    }

    public function openRejectModal($id)
    {
        $this->rejectAnggotaId = $id;
        $this->rejectReason = '';
        $this->rejectModalOpen = true;
    }

    public function closeRejectModal()
    {
        $this->rejectModalOpen = false;
        $this->rejectAnggotaId = null;
        $this->rejectReason = '';
    }

    public function tolak()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:10'
        ]);

        $anggota = Anggota::with('user')->findOrFail($this->rejectAnggotaId);

        DB::transaction(function () use ($anggota) {
            $anggota->update([
                'status' => 'ditolak',
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'reject_member',
                'description' => "Menolak pendaftaran anggota: {$anggota->nama_lengkap}. Alasan: {$this->rejectReason}",
                'ip_address' => request()->ip(),
            ]);

            Mail::to($anggota->user->email)->queue(new AnggotaDitolak($anggota, $this->rejectReason));
        });

        session()->flash('warning', 'Pendaftaran anggota telah ditolak.');
        $this->closeRejectModal();
        $this->closeViewModal();
    }

    public function tunda($id)
    {
        $anggota = Anggota::findOrFail($id);
        
        $anggota->update([
            'status' => 'menunggu'
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'pending_member',
            'description' => "Mengembalikan status pendaftaran anggota ke menunggu: {$anggota->nama_lengkap}",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Status dikembalikan ke menunggu.');
        $this->closeViewModal();
    }

    public function render()
    {
        $query = Anggota::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                  ->orWhere('nik', 'like', '%' . $this->search . '%')
                  ->orWhere('nomor_anggota', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $anggotas = $query->latest()->paginate(10);

        return view('livewire.admin.manajemen-anggota', compact('anggotas'))
            ->layout('components.layouts.app', ['title' => 'Manajemen Anggota']);
    }
}
