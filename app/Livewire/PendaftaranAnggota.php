<?php

namespace App\Livewire;

use App\Mail\PendaftaranBerhasil;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\User;
use App\DataTransferObjects\KtpData;
use App\Services\KtpOcrService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class PendaftaranAnggota extends Component
{
    use WithFileUploads;

    /** File uploads */
    public $fotoKtp;

    public $fotoWajah;

    public ?string $scanMessage = null;

    public bool $scanSuccess = false;

    /** Form fields (KTP data) */
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
    public string $kecamatan = '';
    public string $kelurahan = '';
    public $idpropinsi;
    public $idkabupaten;
    public $idkecamatan;
    public $idkelurahan;

    /** Account fields */
    public string $email = '';

    public string $password = '';
    public string $username = '';

    public string $passwordConfirmation = '';

    public string $noTelepon = '';

    public string $tingkatan = 'PR';


    /** Registration result */
    public bool $registrationComplete = false;

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

        // Update the text field for legacy compatibility/OCR if needed
        $prop = \App\Models\Propinsi::find($value);
        if ($prop) {
            $this->propinsi = $prop->propinsi;
        }
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

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fotoKtp' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'fotoWajah' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'nik' => ['required', 'string', 'size:16', 'regex:/^\d{16}$/', 'unique:anggota,nik'],
            'namaLengkap' => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z\s\.\']+$/'],
            'tempatLahir' => ['required', 'string', 'min:2'],
            'tanggalLahir' => ['required', 'date', 'before:today'],
            'jenisKelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['required', 'string', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu'],
            'alamat' => ['required', 'string', 'min:10'],
            'rtRw' => ['nullable', 'string', 'max:10'],
            'propinsi' => ['required', 'string', 'min:2'],
            'kabupaten' => ['required', 'string', 'min:2'],
            'kecamatan' => ['required', 'string', 'min:2'],
            'kelurahan' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'unique:users,username', 'regex:/^[a-zA-Z0-9_.]+$/'],
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required'],
            'noTelepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
            'tingkatan' => ['required', 'in:DPN,DPD,DPC,PR,PAR'],
            'statusPerkawinan' => ['required', 'string'],
            'pekerjaan' => ['required', 'string'],
            'kewarganegaraan' => ['required', 'string'],
            'golonganDarah' => ['nullable', 'string', 'max:5'],
        ];
    }
    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'nik.regex' => 'NIK hanya boleh berisi angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'namaLengkap.required' => 'Nama lengkap wajib diisi.',
            'namaLengkap.min' => 'Nama lengkap minimal 3 karakter.',
            'namaLengkap.regex' => 'Nama lengkap hanya boleh berisi huruf.',
            'tempatLahir.required' => 'Tempat lahir wajib diisi.',
            'tanggalLahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggalLahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenisKelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib dipilih.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.min' => 'Alamat minimal 10 karakter.',
            'kelurahan.required' => 'Kelurahan wajib diisi.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal 1 huruf kapital dan 1 angka.',
            'password.same' => 'Konfirmasi password tidak cocok.',
            'passwordConfirmation.required' => 'Konfirmasi password wajib diisi.',
            'noTelepon.required' => 'Nomor telepon wajib diisi.',
            'noTelepon.regex' => 'Format nomor telepon tidak valid (08xx atau +62xx, 10-15 digit).',
            'fotoKtp.required' => 'Foto KTP wajib diunggah.',
            'fotoKtp.image' => 'File KTP harus berupa gambar.',
            'fotoKtp.mimes' => 'Format file KTP harus JPG atau PNG.',
            'fotoKtp.max' => 'Ukuran file KTP maksimal 5MB.',
            'fotoWajah.required' => 'Foto wajah/selfie wajib diunggah.',
            'fotoWajah.image' => 'File wajah harus berupa gambar.',
            'fotoWajah.mimes' => 'Format file wajah harus JPG atau PNG.',
            'fotoWajah.max' => 'Ukuran file wajah maksimal 5MB.',
        ];
    }

    public function updatedFotoKtp(): void
    {
        if (!$this->fotoKtp) {
            return;
        }

        $this->validateOnly('fotoKtp');
        $this->scanMessage = null;
        $this->scanSuccess = false;

        try {
            Log::info('Automatic KTP OCR starting...', ['file' => $this->fotoKtp->getClientOriginalName()]);

            $ktpData = app(KtpOcrService::class)->scan($this->fotoKtp->getRealPath());

            if ($ktpData->isEmpty()) {
                $this->scanMessage = 'Gagal memindai data otomatis. Silakan isi formulir secara manual.';
                Log::warning('KTP OCR returned no data');

                return;
            }

            $this->applyKtpData($ktpData);
            $this->scanSuccess = true;
            $this->scanMessage = 'Data KTP berhasil dipindai otomatis. Harap teliti kembali hasil pengisian.';
            Log::info('KTP OCR Success', ['fields' => $ktpData->extractedFieldCount()]);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            $this->scanMessage = 'Pemindaian otomatis tidak berhasil. Silakan isi secara manual.';
        }
    }

    /**
     * Apply scanned KTP data to form fields.
     */
    private function applyKtpData(KtpData $data): void
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

        $prop = \App\Models\Propinsi::whereRaw('LOWER(propinsi) LIKE ?', ['%' . trim($searchProv) . '%'])->first();

        // Fallback: Super fuzzy (no spaces)
        if (!$prop) {
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

            if (!$kab) {
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

    /**
     * Submit registration form.
     */
    public function daftar(): void
    {
        $this->validate();

        // Resolve IDs from text inputs before saving
        $this->resolveRegionalIds();

        DB::transaction(function (): void {
            $ktpPath = $this->fotoKtp->store('foto_ktp', 'local');
            $wajahPath = $this->fotoWajah->store('foto_wajah', 'local');

            $user = User::create([
                'name' => $this->namaLengkap,
                'username' => $this->username,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'anggota',
            ]);

            $user->assignRole('anggota');

            Anggota::create([
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
                'status' => 'menunggu',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'register',
                'description' => "Pendaftaran anggota baru: {$this->namaLengkap}",
                'ip_address' => request()->ip(),
            ]);

            Mail::to($user->email)->queue(new PendaftaranBerhasil($user));
        });

        $this->registrationComplete = true;
    }

    /**
     * Get safe temporary URL for KTP preview.
     */
    public function getKtpPreviewUrl(): ?string
    {
        if (!$this->fotoKtp || !is_object($this->fotoKtp) || !method_exists($this->fotoKtp, 'temporaryUrl')) {
            return null;
        }

        try {
            return $this->fotoKtp->temporaryUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get safe temporary URL for Wajah preview.
     */
    public function getWajahPreviewUrl(): ?string
    {
        if (!$this->fotoWajah || !is_object($this->fotoWajah) || !method_exists($this->fotoWajah, 'temporaryUrl')) {
            return null;
        }

        try {
            return $this->fotoWajah->temporaryUrl();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render(): mixed
    {
        return view('livewire.pendaftaran-anggota')
            ->layout('components.layouts.guest', ['title' => 'Pendaftaran Anggota — SiMOK']);
    }
}
