<?php

namespace App\Livewire\Admin;

use App\Mail\AnggotaDisetujui;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\KartuAnggota;
use App\Models\KartuTemplate;
use App\Models\User;
use App\Services\CardGenerationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class InputManual extends Component
{
    use WithFileUploads;

    public $fotoKtp;
    public $fotoWajah;

    public string $nik = '';
    public string $namaLengkap = '';
    public string $tempatLahir = '';
    public string $tanggalLahir = '';
    public string $jenisKelamin = '';
    public string $agama = '';
    public string $alamat = '';
    public string $rtRw = '';
    public string $kelurahan = '';
    public string $kecamatan = '';
    public $idpropinsi;
    public $idkabupaten;
    public $idkecamatan;
    public $idkelurahan;
    public string $email = '';
    public string $password = '';
    public string $noTelepon = '';

    public string $nomorAnggotaType = 'auto'; // 'auto' or 'manual'
    public string $manualNomorAnggota = '';

    public bool $confirmModalOpen = false;

    /** Lists for dropdowns */
    public $provinces = [];
    public $cities = [];
    public $districts = [];
    public $villages = [];

    public function mount()
    {
        $this->provinces = \App\Models\Propinsi::orderBy('propinsi')->get();
    }

    public function updatedIdpropinsi($value)
    {
        $this->idkabupaten = null;
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        $this->cities = $value ? \App\Models\Kabupaten::where('idpropinsi', $value)->orderBy('kabupaten')->get() : [];
        $this->districts = [];
        $this->villages = [];
    }

    public function updatedIdkabupaten($value)
    {
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        $this->districts = $value ? \App\Models\Kecamatan::where('idkabupaten', $value)->orderBy('kecamatan')->get() : [];
        $this->villages = [];

        $kab = \App\Models\Kabupaten::find($value);
        if ($kab) {
            $this->kabupaten = $kab->kabupaten;
        }
    }

    public function updatedIdkecamatan($value)
    {
        $this->idkelurahan = null;
        $this->villages = $value ? \App\Models\Kelurahan::where('idkecamatan', $value)->orderBy('kelurahan')->get() : [];

        $kec = \App\Models\Kecamatan::find($value);
        if ($kec) {
            $this->kecamatan = $kec->kecamatan;
        }
    }

    public function updatedIdkelurahan($value)
    {
        $kel = \App\Models\Kelurahan::find($value);
        if ($kel) {
            $this->kelurahan = $kel->kelurahan;
        }
    }

    public function rules()
    {
        $rules = [
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/', 'unique:anggota,nik'],
            'namaLengkap' => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'tempatLahir' => ['required', 'string', 'min:2'],
            'tanggalLahir' => ['required', 'date', 'before:today'],
            'jenisKelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['required', 'string', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'alamat' => ['required', 'string', 'min:10'],
            'rtRw' => ['nullable', 'string', 'max:10'],
            'idpropinsi' => ['required', 'string'],
            'idkabupaten' => ['required', 'string'],
            'idkecamatan' => ['required', 'string'],
            'idkelurahan' => ['required', 'string'],
            'kelurahan' => ['required', 'string', 'min:2'],
            'kecamatan' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'noTelepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'fotoKtp' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'fotoWajah' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'nomorAnggotaType' => ['required', 'in:auto,manual']
        ];

        if ($this->nomorAnggotaType === 'manual') {
            $rules['manualNomorAnggota'] = ['required', 'string', 'size:5', 'regex:/^\d{5}$/', 'unique:anggota,nomor_anggota'];
        }

        return $rules;
    }

    public function updatedManualNomorAnggota()
    {
        if ($this->nomorAnggotaType === 'manual') {
            $this->validateOnly('manualNomorAnggota');
        }
    }

    public function openConfirm()
    {
        $this->validate();
        $this->confirmModalOpen = true;
    }

    public function closeConfirm()
    {
        $this->confirmModalOpen = false;
    }

    public function simpan()
    {
        $this->validate();

        DB::transaction(function () {
            $ktpPath = $this->fotoKtp->store('foto_ktp', 'local');
            $wajahPath = $this->fotoWajah->store('foto_wajah', 'local');

            $user = User::create([
                'name' => $this->namaLengkap,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'anggota',
            ]);

            $user->assignRole('anggota');

            // Generate or Assign Number
            if ($this->nomorAnggotaType === 'manual') {
                $newNomor = $this->manualNomorAnggota;
            } else {
                // New Rule: idprop + idkab + {nourut}
                $propId = $this->idpropinsi ?? '00';
                $kabId = $this->idkabupaten ?? '0000';
                
                $lastAnggota = Anggota::where('idkabupaten', $kabId)
                    ->where('nomor_anggota', 'LIKE', $propId . $kabId . '%')
                    ->whereNotNull('nomor_anggota')
                    ->orderBy('nomor_anggota', 'desc')
                    ->lockForUpdate()
                    ->first();
                
                $nextNumber = 1;
                if ($lastAnggota) {
                    $lastNomor = $lastAnggota->nomor_anggota;
                    $lastSequence = intval(substr($lastNomor, -5));
                    $nextNumber = $lastSequence + 1;
                }
                
                $newNomor = $propId . $kabId . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nik' => $this->nik,
                'nama_lengkap' => $this->namaLengkap,
                'tempat_lahir' => $this->tempatLahir,
                'tanggal_lahir' => $this->tanggalLahir,
                'jenis_kelamin' => $this->jenisKelamin,
                'agama' => $this->agama,
                'alamat' => $this->alamat,
                'rt_rw' => $this->rtRw,
                'kelurahan' => $this->kelurahan,
                'kecamatan' => $this->kecamatan,
                'idpropinsi' => $this->idpropinsi,
                'idkabupaten' => $this->idkabupaten,
                'idkecamatan' => $this->idkecamatan,
                'idkelurahan' => $this->idkelurahan,
                'no_telepon' => $this->noTelepon,
                'foto_ktp_path' => $ktpPath,
                'foto_wajah_path' => $wajahPath,
                'status' => 'disetujui',
                'nomor_anggota' => $newNomor,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Create DB record then Generate file
            $template = KartuTemplate::where('is_active', true)->first();
            
            $kartu = KartuAnggota::create([
                'anggota_id' => $anggota->id,
                'nomor_anggota' => $newNomor,
                'template_id' => $template ? $template->id : null,
                'berlaku_hingga' => Carbon::now()->addYears(5)
            ]);

            app(CardGenerationService::class)->generate($anggota);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'manual_insert_member',
                'description' => "Peniputan data anggota manual: {$anggota->nama_lengkap} ({$newNomor})",
                'ip_address' => request()->ip(),
            ]);

            Mail::to($user->email)->queue(new AnggotaDisetujui($anggota));
        });

        session()->flash('message', 'Data anggota berhasil disimpan dan kartu telah digenerate.');
        return redirect()->route('admin.manajemen');
    }

    /**
     * Get safe temporary URL.
     */
    public function getKtpPreviewUrl(): ?string
    {
        if (! $this->fotoKtp || ! is_object($this->fotoKtp) || ! method_exists($this->fotoKtp, 'temporaryUrl')) {
            return null;
        }

        try {
            return $this->fotoKtp->temporaryUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getWajahPreviewUrl(): ?string
    {
        if (! $this->fotoWajah || ! is_object($this->fotoWajah) || ! method_exists($this->fotoWajah, 'temporaryUrl')) {
            return null;
        }

        try {
            return $this->fotoWajah->temporaryUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.admin.input-manual')
            ->layout('components.layouts.app', ['title' => 'Input Manual Anggota']);
    }
}
