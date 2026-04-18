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
use Livewire\WithFileUploads;

class ManajemenAnggota extends Component
{
    use WithPagination, WithFileUploads;

    public $importFile;
    public $importModalOpen = false;

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

    public function openImportModal()
    {
        $this->importModalOpen = true;
    }

    public function closeImportModal()
    {
        $this->importModalOpen = false;
        $this->importFile = null;
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $path = $this->importFile->getRealPath();
            $rows = \Spatie\SimpleExcel\SimpleExcelReader::create($path)
                ->noHeaderRow()
                ->fromSheet(2)
                ->getRows();

            $importedCount = 0;
            $userAuth = auth()->user();

            DB::transaction(function () use ($rows, &$importedCount, $userAuth) {
                foreach ($rows as $row) {
                    $normalizedRow = array_values($row);

                    // Pengecekan data minimal (Index 2: Nama, Index 3: NIK)
                    if (!isset($normalizedRow[2]) || !isset($normalizedRow[3])) {
                        continue;
                    }

                    $nama = trim($normalizedRow[2]);
                    $nik = trim($normalizedRow[3]);

                    if (empty($nama) || empty($nik)) {
                        continue;
                    }

                    // Lewati baris jika ini adalah baris header
                    if (strtolower($nama) === 'nama' || strtolower($nik) === 'nik') {
                        continue;
                    }

                    // Mapping Wilayah
                    $idpropinsi = null;
                    if (!empty($normalizedRow[17])) {
                        $search = strtolower(trim($normalizedRow[17]));
                        if (str_contains($search, 'dki'))
                            $search = str_replace('dki', 'daerah khusus ibukota', $search);
                        if (str_contains($search, 'diy'))
                            $search = str_replace('diy', 'daerah istimewa', $search);
                        $prop = \App\Models\Propinsi::whereRaw('LOWER(propinsi) LIKE ?', ['%' . $search . '%'])->first();
                        if (!$prop) {
                            $noSpace = str_replace([' ', '.', '-'], '', $search);
                            $prop = \App\Models\Propinsi::whereRaw("REPLACE(REPLACE(REPLACE(LOWER(propinsi), ' ', ''), '.', ''), '-', '') LIKE ?", ['%' . $noSpace . '%'])->first();
                        }
                        if ($prop)
                            $idpropinsi = $prop->id;
                    }

                    $idkabupaten = null;
                    if (!empty($normalizedRow[18])) {
                        $search = strtolower(trim($normalizedRow[18]));
                        $kabQuery = \App\Models\Kabupaten::whereRaw('LOWER(kabupaten) LIKE ?', ['%' . $search . '%']);
                        if ($idpropinsi)
                            $kabQuery->where('idpropinsi', $idpropinsi);
                        $kab = $kabQuery->first();
                        if (!$kab) {
                            $noSpace = str_replace([' ', '.', '-', 'kabupaten', 'kab', 'kota'], '', $search);
                            $kabFuzzyQuery = \App\Models\Kabupaten::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(kabupaten), ' ', ''), '.', ''), '-', ''), 'kabupaten', ''), 'kota', '') LIKE ?", ['%' . $noSpace . '%']);
                            if ($idpropinsi)
                                $kabFuzzyQuery->where('idpropinsi', $idpropinsi);
                            $kab = $kabFuzzyQuery->first();
                        }
                        if ($kab)
                            $idkabupaten = $kab->id;
                    }

                    $idkecamatan = null;
                    if (!empty($normalizedRow[19])) {
                        $kecQuery = \App\Models\Kecamatan::whereRaw('LOWER(kecamatan) LIKE ?', ['%' . strtolower(trim($normalizedRow[19])) . '%']);
                        if ($idkabupaten)
                            $kecQuery->where('idkabupaten', $idkabupaten);
                        $kec = $kecQuery->first();
                        if ($kec)
                            $idkecamatan = $kec->id;
                    }

                    $idkelurahan = null;
                    if (!empty($normalizedRow[20])) {
                        $kelQuery = \App\Models\Kelurahan::whereRaw('LOWER(kelurahan) LIKE ?', ['%' . strtolower(trim($normalizedRow[20])) . '%']);
                        if ($idkecamatan)
                            $kelQuery->where('idkecamatan', $idkecamatan);
                        $kel = $kelQuery->first();
                        if ($kel)
                            $idkelurahan = $kel->id;
                    }

                    // Security: Skip jika wilayah tidak masuk lingkup Admin
                    if ($userAuth->tingkatan !== 'DPN') {
                        if ($userAuth->tingkatan === 'DPD' && $idpropinsi != $userAuth->kantor->idpropinsi)
                            continue;
                        if ($userAuth->tingkatan === 'DPC' && $idkabupaten != $userAuth->kantor->idkabupaten)
                            continue;
                        if ($userAuth->tingkatan === 'PR' && $idkecamatan != $userAuth->kantor->idkecamatan)
                            continue;
                    }

                    // Penomoran Anggota
                    $nomor_anggota = trim($normalizedRow[1] ?? '');
                    if (empty($nomor_anggota)) {
                        $propId = $idpropinsi ?? '00';
                        $kabId = $idkabupaten ?? '0000';

                        $lastAnggota = \App\Models\Anggota::where('idkabupaten', $kabId)
                            ->where('nomor_anggota', 'LIKE', $propId . $kabId . '%')
                            ->whereNotNull('nomor_anggota')
                            ->orderBy('nomor_anggota', 'desc')
                            ->lockForUpdate()
                            ->first();

                        $nextNumber = 1;
                        if ($lastAnggota) {
                            $lastSequence = intval(substr($lastAnggota->nomor_anggota, -5));
                            $nextNumber = $lastSequence + 1;
                        }
                        $nomor_anggota = $propId . $kabId . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                    }

                    // Pembuatan User Akun
                    $email = trim($normalizedRow[7] ?? '');
                    $username = trim($normalizedRow[21] ?? ('user_' . time() . rand(100, 999)));
                    $user = null;

                    if (!empty($email)) {
                        $user = \App\Models\User::where('email', $email)->first();
                    }
                    if (!$user && !empty($username)) {
                        $user = \App\Models\User::where('username', $username)->first();
                    }

                    if (!$user) {
                        $user = \App\Models\User::create([
                            'name' => trim($normalizedRow[2]),
                            'username' => $username,
                            'email' => empty($email) ? ($username . '@simok.local') : $email,
                            'password' => \Illuminate\Support\Facades\Hash::make(trim($normalizedRow[22] ?? 'Password123!')),
                            'tingkatan' => 'PR',
                            'role' => 'anggota',
                        ]);
                        $user->assignRole('anggota');
                    }

                    // Format Tanggal
                    $rawTglLahir = $normalizedRow[5] ?? null;
                    $tglLahir = null;
                    try {
                        if ($rawTglLahir instanceof \DateTimeInterface) {
                            $tglLahir = $rawTglLahir->format('Y-m-d');
                        } elseif (!empty(trim((string) $rawTglLahir))) {
                            $strVal = trim((string) $rawTglLahir);
                            $strVal = str_ireplace(
                                ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'],
                                ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
                                $strVal
                            );
                            if (is_numeric($strVal)) {
                                $tglLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($strVal)->format('Y-m-d');
                            } else {
                                $tglLahir = \Illuminate\Support\Carbon::parse($strVal)->format('Y-m-d');
                            }
                        }
                    } catch (\Exception $e) {
                        $tglLahir = '1970-01-01';
                    }

                    if (empty($tglLahir)) {
                        $tglLahir = '1970-01-01';
                    }

                    $rawTglDaftar = $normalizedRow[24] ?? null;
                    $tglDaftar = now();
                    try {
                        if ($rawTglDaftar instanceof \DateTimeInterface) {
                            $tglDaftar = $rawTglDaftar->format('Y-m-d H:i:s');
                        } elseif (!empty(trim((string) $rawTglDaftar))) {
                            $strVal = trim((string) $rawTglDaftar);
                            if (is_numeric($strVal)) {
                                $tglDaftar = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($strVal)->format('Y-m-d H:i:s');
                            } else {
                                $tglDaftar = \Illuminate\Support\Carbon::parse($strVal)->format('Y-m-d H:i:s');
                            }
                        }
                    } catch (\Exception $e) {
                        $tglDaftar = now();
                    }

                    $nik = trim((string) ($normalizedRow[3] ?? ''));

                    // Simpan Anggota
                    $anggota = \App\Models\Anggota::updateOrCreate(
                        ['nik' => $nik],
                        [
                            'user_id' => $user->id,
                            'nomor_anggota' => $nomor_anggota,
                            'nama_lengkap' => trim($normalizedRow[2]),
                            'tempat_lahir' => trim($normalizedRow[4] ?? ''),
                            'tanggal_lahir' => $tglLahir,
                            'no_telepon' => trim($normalizedRow[6] ?? ''),
                            'alamat' => trim($normalizedRow[8] ?? ''),
                            'rt_rw' => trim($normalizedRow[9] ?? '') . '/' . trim($normalizedRow[10] ?? ''),
                            'jenis_kelamin' => trim($normalizedRow[11] ?? ''),
                            'agama' => trim($normalizedRow[12] ?? ''),
                            'status_perkawinan' => trim($normalizedRow[13] ?? ''),
                            'pekerjaan' => trim($normalizedRow[14] ?? ''),
                            'kewarganegaraan' => trim($normalizedRow[15] ?? 'WNI'),
                            'golongan_darah' => trim($normalizedRow[16] ?? '-'),
                            'idpropinsi' => $idpropinsi,
                            'idkabupaten' => $idkabupaten,
                            'idkecamatan' => $idkecamatan,
                            'idkelurahan' => $idkelurahan,
                            'propinsi' => trim($normalizedRow[17] ?? ''),
                            'kabupaten' => trim($normalizedRow[18] ?? ''),
                            'kecamatan' => trim($normalizedRow[19] ?? ''),
                            'kelurahan' => trim($normalizedRow[20] ?? ''),
                            'tingkatan' => 'PR',
                            'status' => strtolower(trim($normalizedRow[23] ?? '')) === 'disetujui' ? 'disetujui' : 'menunggu',
                            'created_at' => $tglDaftar,
                            'updated_at' => now(),
                        ]
                    );

                    $importedCount++;
                }
            });

            $this->closeImportModal();
            session()->flash('message', "Import berhasil! {$importedCount} data anggota telah direkam/diperbarui.");
        } catch (\Exception $e) {
            $this->addError('importFile', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Anggota::scoped(auth()->user());

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
