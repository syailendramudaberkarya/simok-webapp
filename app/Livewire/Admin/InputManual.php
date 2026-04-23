<?php

namespace App\Livewire\Admin;

use App\DataTransferObjects\KtpData;
use App\Mail\AnggotaDisetujui;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\Kabupaten;
use App\Models\KartuAnggota;
use App\Models\KartuTemplate;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Propinsi;
use App\Models\User;
use App\Services\CardGenerationService;
use App\Services\KtpOcrService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

    public string $statusPerkawinan = '';

    public string $pekerjaan = '';

    public string $kewarganegaraan = 'WNI';

    public string $golonganDarah = '';

    public string $alamat = '';

    public string $rtRw = '';

    public string $propinsi = '';

    public string $kabupaten = '';

    public string $kelurahan = '';

    public string $kecamatan = '';

    public $idpropinsi;

    public $idkabupaten;

    public $idkecamatan;

    public $idkelurahan;

    public string $email = '';

    public string $username = '';

    public string $password = '';

    public string $noTelepon = '';

    public string $tingkatan = 'PR';

    public string $nomorAnggotaType = 'auto'; // 'auto' or 'manual'

    public string $manualNomorAnggota = '';

    public bool $confirmModalOpen = false;

    /** Lists for dropdowns */
    public $provinces = [];

    public $cities = [];

    public $districts = [];

    public $villages = [];

    // OCR status
    public $isScanning = false;

    public $scanError = null;

    public function mount()
    {
        $user = auth()->user();
        $kantor = $user->kantor;

        if ($user->tingkatan === 'DPN' || ! $kantor) {
            $this->provinces = Propinsi::orderBy('propinsi')->get();
        } else {
            // Restrict based on admin scope
            $prop = Propinsi::find($kantor->idpropinsi);
            $this->provinces = $prop ? collect([$prop]) : collect();
            $this->idpropinsi = $kantor->idpropinsi;
            $this->propinsi = $prop->propinsi ?? '';

            // Auto-load sub-regions
            $this->cities = Kabupaten::where('idpropinsi', $this->idpropinsi)->orderBy('kabupaten')->get();
            if (in_array($user->tingkatan, ['DPC', 'PR', 'PAR'])) {
                $this->idkabupaten = $kantor->idkabupaten;
                $kab = Kabupaten::find($this->idkabupaten);
                $this->kabupaten = $kab->kabupaten ?? '';
                $this->districts = Kecamatan::where('idkabupaten', $this->idkabupaten)->orderBy('kecamatan')->get();
            }
            if (in_array($user->tingkatan, ['PR', 'PAR'])) {
                $this->idkecamatan = $kantor->idkecamatan;
                $kec = Kecamatan::find($this->idkecamatan);
                $this->kecamatan = $kec->kecamatan ?? '';
                $this->villages = Kelurahan::where('idkecamatan', $this->idkecamatan)->orderBy('kelurahan')->get();
            }
            if ($user->tingkatan === 'PAR') {
                $this->idkelurahan = $kantor->idkelurahan;
                $kel = Kelurahan::find($this->idkelurahan);
                $this->kelurahan = $kel->kelurahan ?? '';
            }
        }
    }

    public function updatedIdpropinsi($value)
    {
        $this->idkabupaten = null;
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        $this->cities = $value ? Kabupaten::where('idpropinsi', $value)->orderBy('kabupaten')->get() : [];
        $this->districts = [];
        $this->villages = [];
    }

    public function updatedIdkabupaten($value)
    {
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        $this->districts = $value ? Kecamatan::where('idkabupaten', $value)->orderBy('kecamatan')->get() : [];
        $this->villages = [];

        $kab = Kabupaten::find($value);
        if ($kab) {
            $this->kabupaten = $kab->kabupaten;
        }
    }

    public function updatedIdkecamatan($value)
    {
        $this->idkelurahan = null;
        $this->villages = $value ? Kelurahan::where('idkecamatan', $value)->orderBy('kelurahan')->get() : [];

        $kec = Kecamatan::find($value);
        if ($kec) {
            $this->kecamatan = $kec->kecamatan;
        }
    }

    public function updatedIdkelurahan($value)
    {
        $kel = Kelurahan::find($value);
        if ($kel) {
            $this->kelurahan = $kel->kelurahan;
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

    public function updatedFotoKtp()
    {
        $this->scanKtp();
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
            'statusPerkawinan' => ['required', 'string'],
            'pekerjaan' => ['required', 'string'],
            'kewarganegaraan' => ['required', 'string'],
            'golonganDarah' => ['nullable', 'string', 'max:5'],
            'alamat' => ['required', 'string', 'min:10'],
            'rtRw' => ['nullable', 'string', 'max:10'],
            'idpropinsi' => ['required', 'string'],
            'idkabupaten' => ['required', 'string'],
            'idkecamatan' => ['required', 'string'],
            'idkelurahan' => ['required', 'string'],
            'propinsi' => ['required', 'string', 'min:2'],
            'kabupaten' => ['required', 'string', 'min:2'],
            'kelurahan' => ['required', 'string', 'min:2'],
            'kecamatan' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users,username', 'regex:/^[a-zA-Z0-9_.]+$/'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'noTelepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'fotoKtp' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'fotoWajah' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'nomorAnggotaType' => ['required', 'in:auto,manual'],
            'tingkatan' => ['required', 'in:DPN,DPD,DPC,PR,PAR'],
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
        $user = auth()->user();
        $kantor = $user->kantor;

        // Force regional assignments based on admin level if not DPN
        if ($user->tingkatan !== 'DPN' && $kantor) {
            if (in_array($user->tingkatan, ['DPD', 'DPC', 'PR', 'PAR'])) {
                $this->idpropinsi = $kantor->idpropinsi;
            }
            if (in_array($user->tingkatan, ['DPC', 'PR', 'PAR'])) {
                $this->idkabupaten = $kantor->idkabupaten;
            }
            if (in_array($user->tingkatan, ['PR', 'PAR'])) {
                $this->idkecamatan = $kantor->idkecamatan;
            }
            if ($user->tingkatan === 'PAR') {
                $this->idkelurahan = $kantor->idkelurahan;
            }
        }

        $this->validate();

        DB::transaction(function () {
            $ktpPath = $this->fotoKtp->store('foto_ktp', 'local');
            $wajahPath = $this->fotoWajah->store('foto_wajah', 'local');

            $user = User::create([
                'name' => $this->namaLengkap,
                'email' => $this->email,
                'username' => $this->username,
                'password' => Hash::make($this->password),
                'role' => 'anggota',
            ]);

            $user->assignRole('anggota');

            // Generate or Assign Number
            if ($this->nomorAnggotaType === 'manual') {
                $newNomor = $this->manualNomorAnggota;
            } else {
                // New Rule: idprop + idkab + {nourut_per_kantor}
                $propId = $this->idpropinsi ?? '00';
                $kabId = $this->idkabupaten ?? '0000';
                $userAuth = auth()->user();
                $idKantor = $userAuth->kantor_id;

                // Gunakan urutan per kantor (berdasarkan kantor_id di tabel users untuk member tersebut)
                $totalExistingInKantor = Anggota::whereHas('user', function ($q) use ($idKantor) {
                    $q->where('kantor_id', $idKantor);
                })->whereNotNull('nomor_anggota')->lockForUpdate()->count();

                $nextNumber = $totalExistingInKantor + 1;

                $newNomor = $propId.$kabId.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            $anggota = Anggota::create([
                'user_id' => $user->id,
                'nik' => $this->nik,
                'nama_lengkap' => $this->namaLengkap,
                'tempat_lahir' => $this->tempatLahir,
                'tanggal_lahir' => $this->tanggalLahir,
                'jenis_kelamin' => $this->jenisKelamin,
                'agama' => $this->agama,
                'status_perkawinan' => $this->statusPerkawinan,
                'pekerjaan' => $this->pekerjaan,
                'kewarganegaraan' => $this->kewarganegaraan,
                'golongan_darah' => $this->golonganDarah,
                'alamat' => $this->alamat,
                'rt_rw' => $this->rtRw,
                'kelurahan' => $this->kelurahan,
                'kecamatan' => $this->kecamatan,
                'idpropinsi' => $this->idpropinsi,
                'idkabupaten' => $this->idkabupaten,
                'idkecamatan' => $this->idkecamatan,
                'idkelurahan' => $this->idkelurahan,
                'no_telepon' => $this->noTelepon,
                'tingkatan' => $this->tingkatan,
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
                'berlaku_hingga' => Carbon::now()->addYears(5),
            ]);

            app(CardGenerationService::class)->generate($anggota);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'manual_insert_member',
                'description' => "Peniputan data anggota manual: {$anggota->nama_lengkap} ({$newNomor})",
                'ip_address' => request()->ip(),
            ]);

            Mail::to($user->email)->queue(new AnggotaDisetujui($anggota, $this->password));
        });

        session()->flash('message', 'Data anggota berhasil disimpan dan kartu telah digenerate.');

        return redirect()->route('admin.manajemen');
    }

    public function scanKtp()
    {
        if (! $this->fotoKtp) {
            $this->addError('fotoKtp', 'Silakan pilih foto KTP terlebih dahulu.');

            return;
        }

        $this->isScanning = true;
        $this->scanError = null;

        try {
            $ocrService = app(KtpOcrService::class);
            $ktpData = $ocrService->scan($this->fotoKtp->getRealPath());

            if ($ktpData) {
                $this->applyKtpData($ktpData);
            } else {
                $this->scanError = 'Gagal mengekstrak data dari KTP. Silakan pastikan foto jelas.';
            }
        } catch (\Exception $e) {
            $this->scanError = 'Terjadi kesalahan saat scan KTP: '.$e->getMessage();
        } finally {
            $this->isScanning = false;
        }
    }

    private function applyKtpData(KtpData $data)
    {
        $fieldMap = [
            'nik' => 'nik',
            'nama' => 'namaLengkap',
            'tempatLahir' => 'tempatLahir',
            'tanggalLahir' => 'tanggalLahir',
            'jenisKelamin' => 'jenisKelamin',
            'agama' => 'agama',
            'statusKawin' => 'statusPerkawinan',
            'pekerjaan' => 'pekerjaan',
            'kewarganegaraan' => 'kewarganegaraan',
            'golonganDarah' => 'golonganDarah',
            'alamat' => 'alamat',
            'rtRw' => 'rtRw',
            'kelurahan' => 'kelurahan',
            'kecamatan' => 'kecamatan',
            'kabupaten' => 'kabupaten',
            'provinsi' => 'propinsi',
        ];

        foreach ($fieldMap as $dtoField => $formField) {
            if ($data->{$dtoField} !== null) {
                $this->{$formField} = $data->{$dtoField};
            }
        }

        // Resolve IDs immediately after scan
        $this->resolveRegionalIds();
    }

    /**
     * Format regional strings according to user rules.
     */
    private function formatRegionalStrings(): void
    {
        if ($this->propinsi) {
            $this->propinsi = Str::title(strtolower(trim($this->propinsi)));
        }
        if ($this->kelurahan) {
            $this->kelurahan = Str::title(strtolower(trim($this->kelurahan)));
        }
        if ($this->kecamatan) {
            $this->kecamatan = strtoupper(trim($this->kecamatan));
        }

        if ($this->kabupaten) {
            $kabRaw = strtoupper(trim($this->kabupaten));
            $name = trim(Str::replaceFirst('KABUPATEN', '', $kabRaw));
            $name = trim(Str::replaceFirst('KAB.', '', $name));
            $name = trim(Str::replaceFirst('KAB', '', $name));
            $name = trim(Str::replaceFirst('KOTA', '', $name));
            $this->kabupaten = strtoupper($name);
        }
    }

    /**
     * Lookup IDs based on formatted text inputs.
     */
    private function resolveRegionalIds(): void
    {
        // Reset IDs first to catch failed matches
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

        $prop = Propinsi::whereRaw('LOWER(propinsi) LIKE ?', ['%'.trim($searchProv).'%'])->first();

        // Fallback: Super fuzzy (no spaces)
        if (! $prop) {
            $noSpace = str_replace([' ', '.', '-'], '', $searchProv);
            $prop = Propinsi::whereRaw("REPLACE(REPLACE(REPLACE(LOWER(propinsi), ' ', ''), '.', ''), '-', '') LIKE ?", ['%'.$noSpace.'%'])->first();
        }

        if ($prop) {
            $this->idpropinsi = (string) $prop->id;
            $this->updatedIdpropinsi($prop->id);

            // 2. Kabupaten
            $searchKab = strtolower($this->kabupaten);
            $kab = Kabupaten::where('idpropinsi', $this->idpropinsi)
                ->whereRaw('LOWER(kabupaten) LIKE ?', ['%'.$searchKab.'%'])
                ->first();

            if (! $kab) {
                $noSpaceKab = str_replace([' ', '.', '-', 'KAB', 'KOTA'], '', $searchKab);
                $kab = Kabupaten::where('idpropinsi', $this->idpropinsi)
                    ->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(kabupaten), ' ', ''), '.', ''), '-', ''), 'KAB', ''), 'KOTA', '') LIKE ?", ['%'.$noSpaceKab.'%'])
                    ->first();
            }

            if ($kab) {
                $this->idkabupaten = (string) $kab->id;
                $this->updatedIdkabupaten($kab->id);

                // 3. Kecamatan
                $kec = Kecamatan::where('idkabupaten', $this->idkabupaten)
                    ->whereRaw('LOWER(kecamatan) LIKE ?', ['%'.strtolower($this->kecamatan).'%'])
                    ->first();

                if ($kec) {
                    $this->idkecamatan = (string) $kec->id;
                    $this->updatedIdkecamatan($kec->id);

                    // 4. Kelurahan
                    $kel = Kelurahan::where('idkecamatan', $this->idkecamatan)
                        ->whereRaw('LOWER(kelurahan) LIKE ?', ['%'.strtolower($this->kelurahan).'%'])
                        ->first();

                    if ($kel) {
                        $this->idkelurahan = (string) $kel->id;
                        $this->updatedIdkelurahan($kel->id);
                    }
                }
            }
        }
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
