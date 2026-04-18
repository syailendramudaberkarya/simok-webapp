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
    public $username;
    public $no_telepon;
    public $alamat;
    public $foto_wajah_baru;
    public $foto_ktp_baru;

    // Fields editable only when rejected
    public $nik;
    public $nama_lengkap;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $jenis_kelamin;
    public $agama;
    public $tingkatan;
    public $status_perkawinan;
    public $pekerjaan;
    public $kewarganegaraan;
    public $golongan_darah;
    
    // Regional fields
    public $idpropinsi;
    public $idkabupaten;
    public $idkecamatan;
    public $idkelurahan;

    // For labels (compatibility/OCR display)
    public $propinsi;
    public $kabupaten;
    public $kecamatan;
    public $kelurahan;

    // Dropdown lists
    public $cities = [];
    public $districts = [];
    public $villages = [];

    // OCR status
    public $isScanning = false;
    public $scanError = null;

    public function mount()
    {
        $this->user = Auth::user();
        $this->anggota = $this->user->anggota;

        $this->email = $this->user->email;
        $this->username = $this->user->username;
        $this->no_telepon = $this->anggota->no_telepon ?? '';
        $this->alamat = $this->anggota->alamat ?? '';
        
        // Populate additional fields if rejected
        $this->nik = $this->anggota->nik;
        $this->nama_lengkap = $this->anggota->nama_lengkap;
        $this->tempat_lahir = $this->anggota->tempat_lahir;
        $this->tanggal_lahir = $this->anggota->tanggal_lahir?->format('Y-m-d');
        $this->jenis_kelamin = $this->anggota->jenis_kelamin;
        $this->agama = $this->anggota->agama;
        $this->tingkatan = $this->anggota->tingkatan;
        $this->status_perkawinan = $this->anggota->status_perkawinan;
        $this->pekerjaan = $this->anggota->pekerjaan;
        $this->kewarganegaraan = $this->anggota->kewarganegaraan;
        $this->golongan_darah = $this->anggota->golongan_darah;

        // Initialize regional data
        $this->idpropinsi = $this->anggota->idpropinsi;
        $this->idkabupaten = $this->anggota->idkabupaten;
        $this->idkecamatan = $this->anggota->idkecamatan;
        $this->idkelurahan = $this->anggota->idkelurahan;

        $this->propinsi = $this->anggota->propinsi;
        $this->kabupaten = $this->anggota->kabupaten;
        $this->kecamatan = $this->anggota->kecamatan;
        $this->kelurahan = $this->anggota->kelurahan;

        if ($this->idpropinsi) {
            $this->cities = \App\Models\Kabupaten::where('idpropinsi', $this->idpropinsi)->orderBy('kabupaten')->get();
        }
        if ($this->idkabupaten) {
            $this->districts = \App\Models\Kecamatan::where('idkabupaten', $this->idkabupaten)->orderBy('kecamatan')->get();
        }
        if ($this->idkecamatan) {
            $this->villages = \App\Models\Kelurahan::where('idkecamatan', $this->idkecamatan)->orderBy('kelurahan')->get();
        }
    }

    public function rules()
    {
        return [
            'email' => "required|email|unique:users,email,{$this->user->id}",
            'username' => "required|string|min:3|max:20|unique:users,username,{$this->user->id}|regex:/^[a-zA-Z0-9_.]+$/",
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'alamat' => 'required|string|min:10',
            'foto_wajah_baru' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'foto_ktp_baru' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'tingkatan' => ['required', 'in:DPN,DPD,PR,PAR'],
            'nik' => $this->anggota->status === 'ditolak' ? 'required|string|size:16' : 'nullable',
            'nama_lengkap' => $this->anggota->status === 'ditolak' ? 'required|string|max:255' : 'nullable',
        ];
    }

    public function ajukanUlang()
    {
        if ($this->anggota->status !== 'ditolak') {
            return;
        }

        $this->validate([
            'nik' => 'required|string|size:16',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string',
            'tingkatan' => 'required|in:DPN,DPD,PR,PAR',
            'status_perkawinan' => 'required|string',
            'pekerjaan' => 'required|string',
            'kewarganegaraan' => 'required|string',
            'golongan_darah' => 'nullable|string|max:5',
            'alamat' => 'required|string|min:10',
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'propinsi' => 'required|string|min:2',
            'kabupaten' => 'required|string|min:2',
            'kecamatan' => 'required|string|min:2',
            'kelurahan' => 'required|string|min:2',
        ]);

        // Resolve IDs from text inputs before saving
        $this->resolveRegionalIds();

        $updateData = [
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'agama' => $this->agama,
            'status_perkawinan' => $this->status_perkawinan,
            'pekerjaan' => $this->pekerjaan,
            'kewarganegaraan' => $this->kewarganegaraan,
            'golongan_darah' => $this->golongan_darah,
            'tingkatan' => $this->tingkatan,
            'alamat' => $this->alamat,
            'no_telepon' => $this->no_telepon,
            'idpropinsi' => $this->idpropinsi,
            'idkabupaten' => $this->idkabupaten,
            'idkecamatan' => $this->idkecamatan,
            'idkelurahan' => $this->idkelurahan,
            'propinsi' => $this->propinsi,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'kelurahan' => $this->kelurahan,
            'status' => 'menunggu',
            'alasan_tolak' => null, // Clear reason after resubmit
        ];

        if ($this->foto_ktp_baru) {
             if ($this->anggota->foto_ktp_path && Storage::disk('local')->exists($this->anggota->foto_ktp_path)) {
                Storage::disk('local')->delete($this->anggota->foto_ktp_path);
            }
            $updateData['foto_ktp_path'] = $this->foto_ktp_baru->store('foto_ktp', 'local');
        }

        if ($this->foto_wajah_baru) {
             if ($this->anggota->foto_wajah_path && Storage::disk('local')->exists($this->anggota->foto_wajah_path)) {
                Storage::disk('local')->delete($this->anggota->foto_wajah_path);
            }
            $updateData['foto_wajah_path'] = $this->foto_wajah_baru->store('foto_wajah', 'local');
        }

        $this->anggota->update($updateData);

        ActivityLog::create([
            'user_id' => $this->user->id,
            'action' => 'resubmit_registration',
            'description' => 'Mengajukan ulang pendaftaran setelah ditolak',
            'ip_address' => request()->ip(),
        ]);

        session()->flash('message', 'Pendaftaran Anda telah diajukan ulang dan menunggu verifikasi.');
        return redirect()->route('anggota.profil');
    }

    public function updateProfil()
    {
        $this->validate();

        $changes = [];

        if ($this->user->email !== $this->email) {
            $this->user->update(['email' => $this->email]);
            $changes[] = 'email';
        }

        if ($this->user->username !== $this->username) {
            $this->user->update(['username' => $this->username]);
            $changes[] = 'username';
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

        if ($this->anggota->tingkatan !== $this->tingkatan) {
            $anggotaData['tingkatan'] = $this->tingkatan;
            $changes[] = 'tingkatan';
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

    public function updatedPropinsi()
    {
        $this->resolveRegionalIds();
    }

    public function updatedKabupaten()
    {
        $this->resolveRegionalIds();
    }

    public function updatedKecamatan()
    {
        $this->resolveRegionalIds();
    }

    public function updatedKelurahan()
    {
        $this->resolveRegionalIds();
    }

    public function scanKtp()
    {
        if (!$this->foto_ktp_baru) {
             $this->addError('foto_ktp_baru', 'Silakan pilih foto KTP terlebih dahulu.');
             return;
        }

        $this->isScanning = true;
        $this->scanError = null;

        try {
            $ocrService = app(\App\Services\KtpOcrService::class);
            $ktpData = $ocrService->scan($this->foto_ktp_baru->getRealPath());
            
            if ($ktpData) {
                $this->applyKtpData($ktpData);
            } else {
                $this->scanError = 'Gagal mengekstrak data dari KTP. Silakan pastikan foto jelas.';
            }
        } catch (\Exception $e) {
            $this->scanError = 'Terjadi kesalahan saat scan KTP: ' . $e->getMessage();
        } finally {
            $this->isScanning = false;
        }
    }

    private function applyKtpData(\App\DataTransferObjects\KtpData $data)
    {
        if ($data->nik) $this->nik = $data->nik;
        if ($data->nama) $this->nama_lengkap = $data->nama;
        if ($data->tempatLahir) $this->tempat_lahir = $data->tempatLahir;
        if ($data->tanggalLahir) $this->tanggal_lahir = $data->tanggalLahir;
        if ($data->jenisKelamin) $this->jenis_kelamin = $data->jenisKelamin;
        if ($data->agama) $this->agama = $data->agama;
        if ($data->statusKawin) $this->status_perkawinan = $data->statusKawin;
        if ($data->pekerjaan) $this->pekerjaan = $data->pekerjaan;
        if ($data->kewarganegaraan) $this->kewarganegaraan = $data->kewarganegaraan;
        if ($data->golonganDarah) $this->golongan_darah = $data->golonganDarah;
        if ($data->alamat) $this->alamat = $data->alamat;
        if ($data->provinsi) $this->propinsi = $data->provinsi;
        if ($data->kabupaten) $this->kabupaten = $data->kabupaten;
        if ($data->kecamatan) $this->kecamatan = $data->kecamatan;
        if ($data->kelurahan) $this->kelurahan = $data->kelurahan;

        // Resolve IDs immediately
        $this->resolveRegionalIds();
    }

    /**
     * Format regional strings according to user rules.
     */
    private function formatRegionalStrings(): void
    {
        if ($this->propinsi) {
            $this->propinsi = \Illuminate\Support\Str::title(strtolower(trim($this->propinsi)));
        }
        if ($this->kelurahan) {
            $this->kelurahan = \Illuminate\Support\Str::title(strtolower(trim($this->kelurahan)));
        }
        if ($this->kecamatan) {
            $this->kecamatan = strtoupper(trim($this->kecamatan));
        }

        if ($this->kabupaten) {
            $kabRaw = strtoupper(trim($this->kabupaten));
            $name = trim(\Illuminate\Support\Str::replaceFirst('KABUPATEN', '', $kabRaw));
            $name = trim(\Illuminate\Support\Str::replaceFirst('KAB.', '', $name));
            $name = trim(\Illuminate\Support\Str::replaceFirst('KAB', '', $name));
            $name = trim(\Illuminate\Support\Str::replaceFirst('KOTA', '', $name));
            $this->kabupaten = strtoupper($name);
        }
    }

    /**
     * Lookup IDs based on formatted text inputs.
     */
    private function resolveRegionalIds(): void
    {
        // Reset IDs first
        $this->idpropinsi = null;
        $this->idkabupaten = null;
        $this->idkecamatan = null;
        $this->idkelurahan = null;

        $this->formatRegionalStrings();

        // 1. Provinsi (Handle DKI, DIY, etc.)
        $searchProv = strtolower($this->propinsi);
        // Expand abbreviations
        if (str_contains($searchProv, 'dki')) {
            $searchProv = str_replace('dki', 'daerah khusus ibukota', $searchProv);
        }
        if (str_contains($searchProv, 'diy')) {
            $searchProv = str_replace('diy', 'daerah istimewa', $searchProv);
        }
        if (str_contains($searchProv, 'd.i.')) {
            $searchProv = str_replace('d.i.', 'daerah istimewa', $searchProv);
        }

        $prop = \App\Models\Propinsi::whereRaw('LOWER(propinsi) LIKE ?', ['%' . trim($searchProv) . '%'])->first();

        // Fallback: Super fuzzy (no spaces)
        if (! $prop) {
            $noSpace = str_replace([' ', '.', '-'], '', $searchProv);
            $prop = \App\Models\Propinsi::whereRaw("REPLACE(REPLACE(REPLACE(LOWER(propinsi), ' ', ''), '.', ''), '-', '') LIKE ?", ['%' . $noSpace . '%'])->first();
        }

        if ($prop) {
            $this->idpropinsi = (string) $prop->id;

            // 2. Kabupaten
            $searchKab = strtolower($this->kabupaten);
            $kab = \App\Models\Kabupaten::where('idpropinsi', $this->idpropinsi)
                ->whereRaw('LOWER(kabupaten) LIKE ?', ['%' . $searchKab . '%'])
                ->first();

            if (! $kab) {
                 $noSpaceKab = str_replace([' ', '.', '-', 'KAB', 'KOTA'], '', $searchKab);
                 $kab = \App\Models\Kabupaten::where('idpropinsi', $this->idpropinsi)
                     ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(kabupaten), ' ', ''), '.', ''), '-', ''), 'KAB', ''), 'KOTA', '') LIKE ?", ['%' . $noSpaceKab . '%'])
                     ->first();
            }

            if ($kab) {
                $this->idkabupaten = (string) $kab->id;

                // 3. Kecamatan
                $kec = \App\Models\Kecamatan::where('idkabupaten', $this->idkabupaten)
                    ->whereRaw('LOWER(kecamatan) LIKE ?', ['%' . strtolower($this->kecamatan) . '%'])
                    ->first();

                if ($kec) {
                    $this->idkecamatan = (string) $kec->id;

                    // 4. Kelurahan
                    $kel = \App\Models\Kelurahan::where('idkecamatan', $this->idkecamatan)
                        ->whereRaw('LOWER(kelurahan) LIKE ?', ['%' . strtolower($this->kelurahan) . '%'])
                        ->first();

                    if ($kel) {
                        $this->idkelurahan = (string) $kel->id;
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.anggota.profil')
            ->layout('components.layouts.app', ['title' => 'Profil Keanggotaan']);
    }
}
