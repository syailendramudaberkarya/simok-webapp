<?php

namespace App\Livewire\Anggota;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KartuDigital extends Component
{
    public $anggota;
    public $kartu;

    public function mount()
    {
        $this->anggota = Auth::user()->anggota;
        
        // Prevent access if not approved (Although middleware should catch it)
        if (!$this->anggota || !$this->anggota->isApproved()) {
            return redirect()->route('anggota.profil');
        }

        // Ideally, card should be generated during approval step by Admin. 
        // We fetch the first valid card.
        $this->kartu = $this->anggota->kartuAnggota()->latest()->first();
    }

    public function render()
    {
        // Require QR Code data for rendering dynamically on view if needed, 
        // or just link to verify page.
        $verifyUrl = route('verify', ['nomor' => $this->anggota->nomor_anggota ?? 'N/A']);

        return view('livewire.anggota.kartu-digital', [
            'verifyUrl' => $verifyUrl
        ])->layout('components.layouts.app', ['title' => 'Kartu Anggota Digital']);
    }
}
