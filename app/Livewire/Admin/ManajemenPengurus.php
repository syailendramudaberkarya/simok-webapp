<?php

namespace App\Livewire\Admin;

use App\Models\Pengurus;
use App\Models\Anggota;
use App\Models\Kantor;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenPengurus extends Component
{
    use WithPagination;

    public $search = '';
    public $memberSearch = '';
    public $searchResults = [];

    // Form fields
    public $pengurus_id = null;
    public $noanggota;
    public $anggota_id;
    public $nama;
    public $kategorijabatan;
    public $subkategorijabatan;
    public $jabatan;
    public $kantor_id;
    public $kantor;
    public $keterangan;

    // Modals state
    public $isFormOpen = false;
    public $isDeleteModalOpen = false;
    public $itemToDelete = null;

    protected $queryString = ['search' => ['except' => '']];

    // Data Constants
    public array $categories = [
        'Penasehat' => [
            'Dewan Pembina',
            'Dewan Pengarah',
        ],
        'Pengurus Harian' => [
            'Badan Pengurus Harian',
        ],
        'Pengurus Bidang' => [
            'Bidang Produksi, Distribusi, Akses & Ketahanan Pangan',
            'Bidang Pemberdayaan Petani & Masyarakat',
            'Bidang Riset, Inovasi, Data, IT & Digitalisasi Pangan',
            'Bidang Edukasi, Kampanye & Literasi Pangan',
            'Bidang Humas, Media & Kemitraan',
            'Bidang Hukum, Advokasi & Kebijakan Pangan',
            'Bidang Pengembangan Organisasi & SDM',
        ],
    ];

    public array $jabatans = [
        'Ketua',
        'Wakil Ketua',
        'Sekretaris',
        'Bendahara',
        'Koordinator',
        'Anggota',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedMemberSearch($value)
    {
        if (strlen($value) < 3) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Anggota::where('status', 'disetujui')
            ->where(function ($query) use ($value) {
                $query->where('nama_lengkap', 'like', '%' . $value . '%')
                    ->orWhere('nomor_anggota', 'like', '%' . $value . '%');
            })
            ->limit(5)
            ->get(['id', 'nama_lengkap', 'nomor_anggota'])
            ->toArray();
    }

    public function selectMember($id, $nama, $nomor)
    {
        $this->anggota_id = $id;
        $this->nama = $nama;
        $this->noanggota = $nomor;
        $this->memberSearch = '';
        $this->searchResults = [];
    }

    public function openForm($id = null)
    {
        $this->resetValidation();
        $this->reset('noanggota', 'anggota_id', 'nama', 'kategorijabatan', 'subkategorijabatan', 'jabatan', 'kantor_id', 'kantor', 'keterangan', 'pengurus_id', 'memberSearch', 'searchResults');
        
        if ($id) {
            $pengurus = Pengurus::findOrFail($id);
            $this->pengurus_id = $pengurus->id;
            $this->noanggota = $pengurus->noanggota;
            $this->anggota_id = $pengurus->anggota_id;
            $this->nama = $pengurus->nama;
            $this->kategorijabatan = $pengurus->kategorijabatan;
            $this->subkategorijabatan = $pengurus->subkategorijabatan;
            $this->jabatan = $pengurus->jabatan;
            $this->kantor_id = $pengurus->kantor_id;
            $this->kantor = $pengurus->kantor;
            $this->keterangan = $pengurus->keterangan;
        }

        $this->isFormOpen = true;
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->reset('noanggota', 'anggota_id', 'nama', 'kategorijabatan', 'subkategorijabatan', 'jabatan', 'kantor_id', 'kantor', 'keterangan', 'pengurus_id', 'memberSearch', 'searchResults');
    }

    public function confirmDelete($id)
    {
        $this->itemToDelete = $id;
        $this->isDeleteModalOpen = true;
    }

    public function closeDeleteModal()
    {
        $this->isDeleteModalOpen = false;
        $this->itemToDelete = null;
    }

    public function delete()
    {
        if ($this->itemToDelete) {
            Pengurus::findOrFail($this->itemToDelete)->delete();
            session()->flash('message', 'Data pengurus berhasil dihapus.');
        }
        $this->closeDeleteModal();
    }

    public function save()
    {
        $this->validate([
            'noanggota' => 'required|string',
            'nama' => 'required|string',
            'kategorijabatan' => 'required|string',
            'subkategorijabatan' => 'required|string',
            'jabatan' => 'required|string',
            'kantor_id' => 'required|exists:kantor,id',
            'keterangan' => 'nullable|string',
        ]);

        // Sync kantor name
        if ($this->kantor_id) {
            $this->kantor = Kantor::find($this->kantor_id)?->nama_kantor;
        }

        Pengurus::updateOrCreate(
            ['id' => $this->pengurus_id],
            [
                'noanggota' => $this->noanggota,
                'anggota_id' => $this->anggota_id,
                'nama' => $this->nama,
                'kategorijabatan' => $this->kategorijabatan,
                'subkategorijabatan' => $this->subkategorijabatan,
                'jabatan' => $this->jabatan,
                'kantor_id' => $this->kantor_id,
                'kantor' => $this->kantor,
                'keterangan' => $this->keterangan,
            ]
        );

        session()->flash('message', $this->pengurus_id ? 'Data pengurus berhasil diperbarui.' : 'Data pengurus berhasil ditambahkan.');
        $this->closeForm();
    }

    public function render()
    {
        $pengurus = Pengurus::when($this->search, function ($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('noanggota', 'like', '%' . $this->search . '%')
                  ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                  ->orWhere('kantor', 'like', '%' . $this->search . '%');
        })->latest()->paginate(10);

        // Fetch offices based on user scope (Hierarchy)
        $kantors = Kantor::scoped(auth()->user())
            ->whereIn('jenjang', ['DPC', 'PR']) // As per user request: "dari PR di DPC itu"
            ->orderBy('jenjang')
            ->orderBy('nama_kantor')
            ->get();

        return view('livewire.admin.manajemen-pengurus', compact('pengurus', 'kantors'))
            ->layout('components.layouts.app', ['title' => 'Manajemen Pengurus']);
    }
}
