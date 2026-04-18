<?php

namespace App\Livewire\Admin;

use App\Models\Kantor;
use App\Models\Propinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\SimpleExcel\SimpleExcelReader;

class ManajemenKantor extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $jenjangFilter = '';
    public $statusFilter = '';
    public $latitude, $longitude, $status = 'Aktif';

    // Modal state
    public $isModalOpen = false;
    public $isDeleteModalOpen = false;
    public $isImportModalOpen = false;
    public $importFile;
    public $importErrors = [];

    // Form properties
    public $kantor_id;
    public $nama_kantor, $jenjang, $telepon, $email, $alamat, $kode_pos;
    public $parent_id;

    // Regional IDs
    public $idpropinsi, $idkabupaten, $idkecamatan, $idkelurahan;
    public $provinsi, $kabupaten, $kecamatan, $kelurahan;

    protected $updatesQueryString = ['search', 'jenjangFilter', 'statusFilter'];

    protected function rules()
    {
        $user = auth()->user();
        $hMap = ['DPN' => 1, 'DPD' => 2, 'DPC' => 3, 'PR' => 4, 'PAR' => 5];
        $currHier = $hMap[$user->tingkatan] ?? 1;

        return [
            'nama_kantor' => 'required|string|max:255',
            'jenjang' => 'required|in:DPN,DPD,DPC,PR,PAR',
            'parent_id' => !in_array($this->jenjang, ['DPN', 'DPD']) ? 'required|exists:kantor,id' : 'nullable',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string',
            'kode_pos' => 'nullable|string|max:10',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'idpropinsi' => (in_array($this->jenjang, ['DPD', 'DPC', 'PR', 'PAR']) && $currHier < 2) ? 'required|string|min:2|max:10' : 'nullable',
            'idkabupaten' => (in_array($this->jenjang, ['DPC', 'PR', 'PAR']) && $currHier < 3) ? 'required|string|min:2|max:10' : 'nullable',
            'idkecamatan' => (in_array($this->jenjang, ['PR', 'PAR']) && $currHier < 4) ? 'required|string|min:2|max:15' : 'nullable',
            'idkelurahan' => ($this->jenjang === 'PAR' && $currHier < 5) ? 'required|string|min:2|max:20' : 'nullable',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openImportModal()
    {
        $this->reset('importFile', 'importErrors');
        $this->isImportModalOpen = true;
    }

    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
        $this->reset('importFile', 'importErrors');
    }

    public function import()
    {
        $this->validate([
            'importFile' => 'required|file|extensions:xlsx,xls,csv|max:10240', // max 10MB
        ]);

        if (!class_exists(SimpleExcelReader::class)) {
            session()->flash('error', 'Package spatie/simple-excel belum di-install. Hubungi Admin (System perlu menjalankan: composer require spatie/simple-excel).');
            return;
        }

        try {
            // Catatan: spatie/simple-excel by default membaca sheet pertama (aktif).
            // Jika Anda menggunakan file multi-sheet, pastikan Data Kantor berada pada sheet aktif,
            // atau gunakan format CSV.
            $rows = SimpleExcelReader::create($this->importFile->path())
                ->noHeaderRow()
                ->fromSheet(2)
                ->getRows();

            $successCount = 0;
            $this->importErrors = [];

            foreach ($rows as $row) {
                // Konversi row menjadi basic array
                $values = array_values(is_array($row) ? $row : iterator_to_array($row));

                $normalizedRow = [
                    'namakantor' => trim($values[1] ?? '') === '' ? null : trim($values[1]),
                    'jenjang' => trim($values[2] ?? '') === '' ? null : trim($values[2]),
                    'telepon' => trim($values[3] ?? '') === '' ? null : trim($values[3]),
                    'email' => trim($values[4] ?? '') === '' ? null : trim($values[4]),
                    'alamat' => trim($values[5] ?? '') === '' ? null : trim($values[5]),
                    'kodepos' => trim($values[6] ?? '') === '' ? null : trim($values[6]),
                    'provinsi' => trim($values[7] ?? '') === '' ? null : trim($values[7]),
                    'kabupaten' => trim($values[8] ?? '') === '' ? null : trim($values[8]),
                    'kecamatan' => trim($values[9] ?? '') === '' ? null : trim($values[9]),
                    'kelurahan' => trim($values[10] ?? '') === '' ? null : trim($values[10]),
                    'latitude' => trim($values[11] ?? '') === '' ? null : trim($values[11]),
                    'longitude' => trim($values[12] ?? '') === '' ? null : trim($values[12]),
                    'status' => trim($values[13] ?? '') === '' ? null : trim($values[13]),
                ];

                $jenjangVal = strtoupper($normalizedRow['jenjang'] ?? '');

                if (empty($normalizedRow['namakantor']) || !in_array($jenjangVal, ['DPN', 'DPD', 'DPC', 'PR', 'PAR'])) {
                    continue;
                }

                // Excel berisi TEKS wilayah. Kita mencari ID-nya di database.
                $idpropinsi = null;
                $txtProvinsi = null;
                if (!empty($normalizedRow['provinsi'])) {
                    $txtProvinsi = trim($normalizedRow['provinsi']);
                    $search = strtolower($txtProvinsi);
                    if (str_contains($search, 'dki'))
                        $search = str_replace('dki', 'daerah khusus ibukota', $search);
                    if (str_contains($search, 'diy'))
                        $search = str_replace('diy', 'daerah istimewa', $search);

                    $prop = Propinsi::whereRaw('LOWER(propinsi) LIKE ?', ['%' . $search . '%'])->first();
                    if (!$prop) {
                        $noSpace = str_replace([' ', '.', '-'], '', $search);
                        $prop = Propinsi::whereRaw("REPLACE(REPLACE(REPLACE(LOWER(propinsi), ' ', ''), '.', ''), '-', '') LIKE ?", ['%' . $noSpace . '%'])->first();
                    }
                    if ($prop) {
                        $idpropinsi = $prop->id;
                        $txtProvinsi = \Illuminate\Support\Str::title(strtolower($prop->propinsi));
                    }
                }

                $idkabupaten = null;
                $txtKabupaten = null;
                if (!empty($normalizedRow['kabupaten'])) {
                    $txtKabupaten = trim($normalizedRow['kabupaten']);
                    $search = strtolower($txtKabupaten);

                    $kabQuery = Kabupaten::whereRaw('LOWER(kabupaten) LIKE ?', ['%' . $search . '%']);
                    if ($idpropinsi)
                        $kabQuery->where('idpropinsi', $idpropinsi);
                    $kab = $kabQuery->first();

                    if (!$kab) {
                        $noSpace = str_replace([' ', '.', '-', 'kabupaten', 'kab', 'kota'], '', $search);
                        $kabFuzzyQuery = Kabupaten::whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(kabupaten), ' ', ''), '.', ''), '-', ''), 'kabupaten', ''), 'kota', '') LIKE ?", ['%' . $noSpace . '%']);
                        if ($idpropinsi)
                            $kabFuzzyQuery->where('idpropinsi', $idpropinsi);
                        $kab = $kabFuzzyQuery->first();
                    }
                    if ($kab) {
                        $idkabupaten = $kab->id;
                        $txtKabupaten = \Illuminate\Support\Str::title(strtolower($kab->kabupaten));
                    }
                }

                $idkecamatan = null;
                $txtKecamatan = null;
                if (!empty($normalizedRow['kecamatan'])) {
                    $txtKecamatan = trim($normalizedRow['kecamatan']);
                    $kecQuery = Kecamatan::whereRaw('LOWER(kecamatan) LIKE ?', ['%' . strtolower($txtKecamatan) . '%']);
                    if ($idkabupaten)
                        $kecQuery->where('idkabupaten', $idkabupaten);
                    $kec = $kecQuery->first();

                    if ($kec) {
                        $idkecamatan = $kec->id;
                        $txtKecamatan = strtoupper($kec->kecamatan);
                    }
                }

                $idkelurahan = null;
                $txtKelurahan = null;
                if (!empty($normalizedRow['kelurahan'])) {
                    $txtKelurahan = trim($normalizedRow['kelurahan']);
                    $kelQuery = Kelurahan::whereRaw('LOWER(kelurahan) LIKE ?', ['%' . strtolower($txtKelurahan) . '%']);
                    if ($idkecamatan)
                        $kelQuery->where('idkecamatan', $idkecamatan);
                    $kel = $kelQuery->first();

                    if ($kel) {
                        $idkelurahan = $kel->id;
                        $txtKelurahan = \Illuminate\Support\Str::title(strtolower($kel->kelurahan));
                    }
                }

                $jenjang = strtoupper(trim($normalizedRow['jenjang']));

                // --- Scope Enforcement for Import ---
                $currentUser = auth()->user();
                if ($currentUser->tingkatan !== 'DPN' && $currentUser->kantor) {
                    $adminKantor = $currentUser->kantor;

                    // Skip if row targets a higher or different hierarchy than admin
                    $hierarchy = ['DPN' => 1, 'DPD' => 2, 'DPC' => 3, 'PR' => 4, 'PAR' => 5];
                    if ($hierarchy[$jenjang] <= $hierarchy[$currentUser->tingkatan]) {
                        continue;
                    }

                    // Enforce admin's region
                    if (in_array($currentUser->tingkatan, ['DPD', 'DPC', 'PR', 'PAR'])) {
                        if ($idpropinsi && $idpropinsi !== $adminKantor->idpropinsi)
                            continue;
                        $idpropinsi = $adminKantor->idpropinsi;
                        $txtProvinsi = \Illuminate\Support\Str::title(strtolower(Propinsi::find($idpropinsi)?->propinsi));
                    }
                    if (in_array($currentUser->tingkatan, ['DPC', 'PR', 'PAR'])) {
                        if ($idkabupaten && $idkabupaten !== $adminKantor->idkabupaten)
                            continue;
                        $idkabupaten = $adminKantor->idkabupaten;
                        $txtKabupaten = \Illuminate\Support\Str::title(strtolower(Kabupaten::find($idkabupaten)?->kabupaten));
                    }
                    if (in_array($currentUser->tingkatan, ['PR', 'PAR'])) {
                        if ($idkecamatan && $idkecamatan !== $adminKantor->idkecamatan)
                            continue;
                        $idkecamatan = $adminKantor->idkecamatan;
                        $txtKecamatan = strtoupper(Kecamatan::find($idkecamatan)?->kecamatan);
                    }
                    if ($currentUser->tingkatan === 'PAR') {
                        if ($idkelurahan && $idkelurahan !== $adminKantor->idkelurahan)
                            continue;
                        $idkelurahan = $adminKantor->idkelurahan;
                        $txtKelurahan = \Illuminate\Support\Str::title(strtolower(Kelurahan::find($idkelurahan)?->kelurahan));
                    }
                }
                // --- End Scope Enforcement ---

                $parent_id = null;
                if (in_array($jenjang, ['DPD', 'DPC', 'PR', 'PAR'])) {
                    $parentQuery = Kantor::query();
                    if ($jenjang === 'DPD') {
                        $parent_id = Kantor::where('jenjang', 'DPN')->value('id');
                    } elseif ($jenjang === 'DPC' && $idpropinsi) {
                        $parent_id = Kantor::where('jenjang', 'DPD')->where('idpropinsi', $idpropinsi)->value('id');
                    } elseif ($jenjang === 'PR' && $idkabupaten) {
                        $parent_id = Kantor::where('jenjang', 'DPC')->where('idkabupaten', $idkabupaten)->value('id');
                    } elseif ($jenjang === 'PAR' && $idkecamatan) {
                        $parent_id = Kantor::where('jenjang', 'PR')->where('idkecamatan', $idkecamatan)->value('id');
                    }
                }

                // Insert / Update Kantor
                Kantor::updateOrCreate(
                    [
                        'nama_kantor' => trim($normalizedRow['namakantor']),
                        'jenjang' => $jenjang,
                    ],
                    [
                        'telepon' => $normalizedRow['telepon'] ?? null,
                        'email' => $normalizedRow['email'] ?? null,
                        'alamat' => $normalizedRow['alamat'] ?? null,
                        'kode_pos' => $normalizedRow['kodepos'] ?? null,
                        'idpropinsi' => $idpropinsi,
                        'provinsi' => $txtProvinsi,
                        'idkabupaten' => $idkabupaten,
                        'kabupaten' => $txtKabupaten,
                        'idkecamatan' => $idkecamatan,
                        'kecamatan' => $txtKecamatan,
                        'idkelurahan' => $idkelurahan,
                        'kelurahan' => $txtKelurahan,
                        'latitude' => $normalizedRow['latitude'] ?? null,
                        'longitude' => $normalizedRow['longitude'] ?? null,
                        'status' => ucfirst(strtolower($normalizedRow['status'] ?? 'Aktif')),
                        'parent_id' => $parent_id,
                    ]
                );

                $successCount++;
            }

            session()->flash('message', "Import berhasil! {$successCount} data kantor telah ditambahkan atau diperbarui.");
            $this->closeImportModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $propinsis = \App\Models\Propinsi::orderBy('propinsi')->get();
        $kabupatens = $this->idpropinsi ? \App\Models\Kabupaten::where('idpropinsi', $this->idpropinsi)->orderBy('kabupaten')->get() : [];
        $kecamatans = $this->idkabupaten ? \App\Models\Kecamatan::where('idkabupaten', $this->idkabupaten)->orderBy('kecamatan')->get() : [];
        $kelurahans = $this->idkecamatan ? \App\Models\Kelurahan::where('idkecamatan', $this->idkecamatan)->orderBy('kelurahan')->get() : [];

        $parentOptions = [];
        $user = auth()->user();
        if ($this->jenjang === 'DPC' && $this->idpropinsi) {
            $parentOptions = Kantor::scoped($user)->where('jenjang', 'DPD')->where('idpropinsi', $this->idpropinsi)->get();
        } elseif ($this->jenjang === 'PR' && $this->idkabupaten) {
            $parentOptions = Kantor::scoped($user)->where('jenjang', 'DPC')->where('idkabupaten', $this->idkabupaten)->get();
        } elseif ($this->jenjang === 'PAR' && $this->idkecamatan) {
            $parentOptions = Kantor::scoped($user)->where('jenjang', 'PR')->where('idkecamatan', $this->idkecamatan)->get();
        }

        // Auto-select parent if there's only one valid parent option
        if (($parentOptions instanceof \Illuminate\Database\Eloquent\Collection ? $parentOptions->count() : count($parentOptions)) === 1 && empty($this->parent_id)) {
            $this->parent_id = $parentOptions->first()->id;
        }

        $query = Kantor::with('parent')
            ->scoped(auth()->user())
            ->when(auth()->user()->tingkatan !== 'DPN', function ($q) use ($user) {
                // Ensure they only see offices strictly below their level
                $hMap = ['DPN' => 1, 'DPD' => 2, 'DPC' => 3, 'PR' => 4, 'PAR' => 5];
                $currentHier = $hMap[$user->tingkatan] ?? 1;
                $allowed = array_keys(array_filter($hMap, fn($val) => $val > $currentHier));
                $q->whereIn('jenjang', $allowed);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('nama_kantor', 'like', '%' . $this->search . '%')
                        ->orWhere('alamat', 'like', '%' . $this->search . '%')
                        ->orWhere('kabupaten', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->jenjangFilter, function ($q) {
                $q->where('jenjang', $this->jenjangFilter);
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('jenjang')
            ->orderBy('nama_kantor');

        return view('livewire.admin.manajemen-kantor', [
            'kantors' => $query->paginate(10),
            'propinsis' => $propinsis,
            'kabupatens' => $kabupatens,
            'kecamatans' => $kecamatans,
            'kelurahans' => $kelurahans,
            'parentOptions' => $parentOptions,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Kantor - SiMOK']);
    }

    public function toggleStatus($id)
    {
        $kantor = Kantor::scoped(auth()->user())->findOrFail($id);
        $kantor->status = $kantor->status === 'Aktif' ? 'Non-Aktif' : 'Aktif';
        $kantor->save();

        session()->flash('message', 'Status kantor berhasil diubah.');
    }

    public function createKantor()
    {
        $this->resetInputFields();

        $user = auth()->user();
        if ($user->tingkatan !== 'DPN' && $user->kantor) {
            $kantor = $user->kantor;
            if (in_array($user->tingkatan, ['DPD', 'DPC', 'PR', 'PAR'])) {
                $this->idpropinsi = $kantor->idpropinsi;
                $this->provinsi = $kantor->provinsi;
            }
            if (in_array($user->tingkatan, ['DPC', 'PR', 'PAR'])) {
                $this->idkabupaten = $kantor->idkabupaten;
                $this->kabupaten = $kantor->kabupaten;
            }
            if (in_array($user->tingkatan, ['PR', 'PAR'])) {
                $this->idkecamatan = $kantor->idkecamatan;
                $this->kecamatan = $kantor->kecamatan;
            }
            if ($user->tingkatan === 'PAR') {
                $this->idkelurahan = $kantor->idkelurahan;
                $this->kelurahan = $kantor->kelurahan;
            }
        }

        $this->isModalOpen = true;
    }

    public function editKantor($id)
    {
        $this->resetInputFields();
        $kantor = Kantor::scoped(auth()->user())->findOrFail($id);

        $this->kantor_id = $id;
        $this->nama_kantor = $kantor->nama_kantor;
        $this->jenjang = $kantor->jenjang;
        $this->telepon = $kantor->telepon;
        $this->email = $kantor->email;
        $this->alamat = $kantor->alamat;
        $this->kode_pos = $kantor->kode_pos;
        $this->latitude = $kantor->latitude;
        $this->longitude = $kantor->longitude;
        $this->parent_id = $kantor->parent_id;
        $this->idpropinsi = $kantor->idpropinsi;
        $this->idkabupaten = $kantor->idkabupaten;
        $this->idkecamatan = $kantor->idkecamatan;
        $this->idkelurahan = $kantor->idkelurahan;
        $this->provinsi = $kantor->provinsi;
        $this->kabupaten = $kantor->kabupaten;
        $this->kecamatan = $kantor->kecamatan;
        $this->kelurahan = $kantor->kelurahan;

        $this->isModalOpen = true;
    }

    public function updatedJenjang()
    {
        $this->parent_id = null;
        if ($this->jenjang === 'DPN') {
            $this->idpropinsi = null;
            $this->idkabupaten = null;
            $this->idkecamatan = null;
            $this->idkelurahan = null;
        }
    }

    public function updatedIdpropinsi($val)
    {
        $this->idkabupaten = null;
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        if ($this->jenjang === 'DPC')
            $this->parent_id = null;
        if ($val)
            $this->provinsi = \App\Models\Propinsi::find($val)?->propinsi;
    }

    public function updatedIdkabupaten($val)
    {
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        if ($this->jenjang === 'PR')
            $this->parent_id = null;
        if ($val)
            $this->kabupaten = \App\Models\Kabupaten::find($val)?->kabupaten;
    }

    public function updatedIdkecamatan($val)
    {
        $this->idkelurahan = null;
        if ($this->jenjang === 'PAR')
            $this->parent_id = null;
        if ($val)
            $this->kecamatan = \App\Models\Kecamatan::find($val)?->kecamatan;
    }

    public function updatedIdkelurahan($val)
    {
        if ($val)
            $this->kelurahan = \App\Models\Kelurahan::find($val)?->kelurahan;
    }

    public function saveKantor()
    {
        $this->validate();

        $user = auth()->user();
        $kantor = $user->kantor;

        // Verify hierarchy restriction
        $hierarchyMap = ['DPN' => 1, 'DPD' => 2, 'DPC' => 3, 'PR' => 4, 'PAR' => 5];
        $currentHierarchy = $hierarchyMap[$user->tingkatan] ?? 1;
        $targetHierarchy = $hierarchyMap[$this->jenjang] ?? 99;

        if ($user->tingkatan !== 'DPN' && $targetHierarchy <= $currentHierarchy) {
            $this->addError('jenjang', 'Mengingat Anda adalah admin ' . $user->tingkatan . ', Anda hanya dapat mengelola kantor untuk tingkat di bawah Anda.');
            return;
        }

        if ($user->tingkatan !== 'DPN' && $kantor) {
            // Hanya menimpa parent_id jika sedang mengelola tingkatan persis di bawahnya
            if ($targetHierarchy === $currentHierarchy + 1) {
                $this->parent_id = $kantor->id;
            }

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

        // Final security check for edit
        if ($this->kantor_id) {
            Kantor::scoped($user)->findOrFail($this->kantor_id);
        }

        Kantor::updateOrCreate(['id' => $this->kantor_id], [
            'nama_kantor' => $this->nama_kantor,
            'jenjang' => $this->jenjang,
            'parent_id' => in_array($this->jenjang, ['DPN', 'DPD']) ? null : $this->parent_id,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'kode_pos' => $this->kode_pos,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'idpropinsi' => $this->idpropinsi,
            'idkabupaten' => $this->idkabupaten,
            'idkecamatan' => $this->idkecamatan,
            'idkelurahan' => $this->idkelurahan,
            'provinsi' => $this->jenjang !== 'DPN' && $this->idpropinsi ? \Illuminate\Support\Str::title(strtolower(\App\Models\Propinsi::find($this->idpropinsi)?->propinsi)) : null,
            'kabupaten' => in_array($this->jenjang, ['DPC', 'PR', 'PAR']) && $this->idkabupaten ? \Illuminate\Support\Str::title(strtolower(\App\Models\Kabupaten::find($this->idkabupaten)?->kabupaten)) : null,
            'kecamatan' => in_array($this->jenjang, ['PR', 'PAR']) && $this->idkecamatan ? strtoupper(\App\Models\Kecamatan::find($this->idkecamatan)?->kecamatan) : null,
            'kelurahan' => $this->jenjang === 'PAR' && $this->idkelurahan ? \Illuminate\Support\Str::title(strtolower(\App\Models\Kelurahan::find($this->idkelurahan)?->kelurahan)) : null,
            'status' => $this->kantor_id ? Kantor::find($this->kantor_id)->status : 'Aktif',
        ]);

        session()->flash('message', $this->kantor_id ? 'Data kantor berhasil diperbarui.' : 'Kantor baru berhasil ditambahkan.');

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->kantor_id = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteKantor()
    {
        Kantor::scoped(auth()->user())->findOrFail($this->kantor_id)->delete();
        session()->flash('message', 'Data kantor berhasil dihapus.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->kantor_id = null;
        $this->nama_kantor = '';
        $this->jenjang = '';
        $this->parent_id = null;
        $this->telepon = '';
        $this->email = '';
        $this->alamat = '';
        $this->kode_pos = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->idpropinsi = null;
        $this->idkabupaten = null;
        $this->idkecamatan = null;
        $this->idkelurahan = null;
        $this->provinsi = '';
        $this->kabupaten = '';
        $this->kecamatan = '';
        $this->kelurahan = '';
    }
}