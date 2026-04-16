<?php

namespace App\Livewire;

use App\Mail\PendaftaranBerhasil;
use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\User;
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

    public string $passwordConfirmation = '';

    public string $noTelepon = '';

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

    public function updatedIdkelurahan($value)
    {
        $kel = \App\Models\Kelurahan::find($value);
        if ($kel) {
            $this->kelurahan = $kel->kelurahan;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fotoKtp' => $this->fotoKtp instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                ? ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120']
                : ['required', 'string'],
            'fotoWajah' => $this->fotoWajah instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                ? ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120']
                : ['required', 'string'],
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
            'password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required'],
            'noTelepon' => ['required', 'string', 'regex:/^(\+62|08)\d{8,13}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
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

    public function updatedFotoKtp()
    {
        $this->validateOnly('fotoKtp');
        $this->scanMessage = null;
        $this->scanSuccess = false;

        try {
            // Get path directly without moving the file
            $ktpFullPath = $this->fotoKtp->getRealPath();

            // Scan using service
            $ocrService = app(KtpOcrService::class);
            $parsedData = $ocrService->scan($ktpFullPath);

            if (!empty($parsedData) && count($parsedData) > 0) {
                $this->nik = $parsedData['nik'] ?? $this->nik;
                $this->namaLengkap = $parsedData['nama'] ?? $this->namaLengkap;
                $this->tempatLahir = $parsedData['tempat_lahir'] ?? $this->tempatLahir;
                $this->tanggalLahir = $parsedData['tanggal_lahir'] ?? $this->tanggalLahir;
                $this->jenisKelamin = $parsedData['jenis_kelamin'] ?? $this->jenisKelamin;
                $this->agama = $parsedData['agama'] ?? $this->agama;
                $this->alamat = $parsedData['alamat'] ?? $this->alamat;
                $this->rtRw = $parsedData['rt_rw'] ?? $this->rtRw;
                $this->kelurahan = $parsedData['kelurahan'] ?? $this->kelurahan;
                $this->kecamatan = $parsedData['kecamatan'] ?? $this->kecamatan;

                $this->scanSuccess = true;
                $this->scanMessage = 'Formulir telah terisi otomatis berdasarkan KTP, harap periksa kembali data di bawah.';
            } else {
                $this->scanSuccess = false;
                $this->scanMessage = 'Gagal mengekstrak data otomatis. Silakan isi form di bawah.';
            }
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            $this->scanSuccess = false;
            $this->scanMessage = 'Terjadi kesalahan saat pemindaian KTP.';
        }
    }

    /**
     * Submit registration form.
     */
    public function daftar(): void
    {
        $this->validate();

        DB::transaction(function (): void {
            $ktpPath = $this->fotoKtp->store('foto_ktp', 'local');
            $wajahPath = $this->fotoWajah->store('foto_wajah', 'local');

            $user = User::create([
                'name' => $this->namaLengkap,
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
