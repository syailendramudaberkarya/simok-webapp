<?php

namespace App\Livewire\Anggota;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profil extends Component
{
    use WithFileUploads;

    public $user;
    public $anggota;

    // Editable fields
    public $email;
    public $no_telepon;
    public $alamat;
    public $foto_wajah_baru;

    public function mount()
    {
        $this->user = Auth::user();
        $this->anggota = $this->user->anggota;

        $this->email = $this->user->email;
        $this->no_telepon = $this->anggota->no_telepon ?? '';
        $this->alamat = $this->anggota->alamat ?? '';
    }

    public function rules()
    {
        return [
            'email' => "required|email|unique:users,email,{$this->user->id}",
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'alamat' => 'required|string|min:10',
            'foto_wajah_baru' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function updateProfil()
    {
        $this->validate();

        $changes = [];

        if ($this->user->email !== $this->email) {
            $this->user->update(['email' => $this->email]);
            $changes[] = 'email';
        }

        $anggotaData = [];
        
        if ($this->anggota->no_telepon !== $this->no_telepon) {
            $anggotaData['no_telepon'] = $this->no_telepon;
            $changes[] = 'nomor telepon';
        }

        if ($this->anggota->alamat !== $this->alamat) {
            $anggotaData['alamat'] = $this->alamat;
            $changes[] = 'alamat';
        }

        if ($this->foto_wajah_baru) {
            // Hapus foto lama, simpan foto baru
            if ($this->anggota->foto_wajah_path && Storage::disk('local')->exists($this->anggota->foto_wajah_path)) {
                Storage::disk('local')->delete($this->anggota->foto_wajah_path);
            }
            
            $anggotaData['foto_wajah_path'] = $this->foto_wajah_baru->store('foto_wajah', 'local');
            $changes[] = 'foto wajah';
        }

        if (!empty($anggotaData)) {
            $this->anggota->update($anggotaData);
        }

        if (!empty($changes)) {
            ActivityLog::create([
                'user_id' => $this->user->id,
                'action' => 'update_profile',
                'description' => 'Memperbarui profil: ' . implode(', ', $changes),
                'ip_address' => request()->ip(),
            ]);

            session()->flash('message', 'Profil berhasil diperbarui!');
        }

        $this->foto_wajah_baru = null; // reset input
    }

    /**
     * Get safe temporary URL.
     */
    public function getFotoWajahPreviewUrl(): ?string
    {
        if (! $this->foto_wajah_baru || ! is_object($this->foto_wajah_baru) || ! method_exists($this->foto_wajah_baru, 'temporaryUrl')) {
            return null;
        }

        try {
            return $this->foto_wajah_baru->temporaryUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.anggota.profil')
            ->layout('components.layouts.app', ['title' => 'Profil Keanggotaan']);
    }
}
