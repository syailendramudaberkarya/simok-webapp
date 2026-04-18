<?php

namespace App\Livewire\Admin;

use App\Models\Kantor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ManajemenAdmin extends Component
{
    use WithPagination;

    public $search = '';
    public $tingkatanFilter = '';

    // Form fields
    public $user_id;
    public $name, $username, $email, $password;
    public $tingkatan;
    public $kantor_id;

    public $isModalOpen = false;
    public $isDeleteModalOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'tingkatanFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|regex:/^[a-zA-Z0-9_.]+$/|max:255|unique:users,username,' . $this->user_id,
            'email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'password' => $this->user_id ? 'nullable|string|min:8' : 'required|string|min:8',
            'tingkatan' => 'required|in:DPN,DPD,DPC,PR,PAR',
            'kantor_id' => $this->tingkatan !== 'DPN' ? 'required|exists:kantor,id' : 'nullable',
        ];
    }

    public function getAvailableTingkatan()
    {
        $user = auth()->user();
        if ($user->tingkatan === 'DPN') {
            return ['DPD', 'DPC']; // Only DPD and DPC admins allowed
        } elseif ($user->tingkatan === 'DPD') {
            return ['DPD', 'DPC']; // Can manage/create DPD or DPC within their scope
        } elseif ($user->tingkatan === 'DPC') {
            return ['DPC']; // Can only manage/create DPC (if allowed)
        }
        return [];
    }

    public function render()
    {
        $currentUser = auth()->user();
        
        // Scope the users query to only include admins inside the current user's regional scope.
        $query = User::where('role', 'admin')
            ->where(function ($q) use ($currentUser) {
                // Determine what offices the current user can see
                if ($currentUser->tingkatan === 'DPN') {
                    // DPN can see everyone
                } else {
                    $q->whereHas('kantor', function ($kantorQuery) use ($currentUser) {
                        $kantorQuery->scoped($currentUser);
                    });
                }
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('username', 'like', '%' . $this->search . '%')
                       ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tingkatanFilter, function ($q) {
                $q->where('tingkatan', $this->tingkatanFilter);
            })
            ->with('kantor');

        // Provide valid kantor choices for the selector based on current admin's scope and selected tingkatan
        $kantorOptions = [];
        if ($this->tingkatan && $this->tingkatan !== 'DPN') {
            $kantorOptions = Kantor::scoped($currentUser)->where('jenjang', $this->tingkatan)->orderBy('nama_kantor')->get();
        }

        return view('livewire.admin.manajemen-admin', [
            'users' => $query->paginate(10),
            'availableTingkatan' => $this->getAvailableTingkatan(),
            'kantorOptions' => $kantorOptions,
        ])->layout('components.layouts.app', ['title' => 'Manajemen Admin - SiMOK']);
    }

    public function createAdmin()
    {
        $this->resetInput();
        $this->isModalOpen = true;
    }

    public function editAdmin($id)
    {
        $this->resetInput();
        $user = User::findOrFail($id);

        // Security check: Must not allow editing users outside their scope
        if (auth()->user()->tingkatan !== 'DPN') {
            if (!$user->kantor_id) {
                session()->flash('error', 'Anda tidak memiliki otoritas untuk mengedit admin pusat.');
                $this->closeModal();
                return;
            }
            // Validate the user's kantor is within scope
            $isInScope = Kantor::scoped(auth()->user())->where('id', $user->kantor_id)->exists();
            if (!$isInScope) {
                session()->flash('error', 'Anda tidak memiliki akses untuk mengedit admin di wilayah ini.');
                $this->closeModal();
                return;
            }
        }

        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->tingkatan = $user->tingkatan;
        $this->kantor_id = $user->kantor_id;

        $this->isModalOpen = true;
    }

    public function saveAdmin()
    {
        $this->validate();

        // Enforce hierarchy rules:
        $available = $this->getAvailableTingkatan();
        if (!in_array($this->tingkatan, $available)) {
            $this->addError('tingkatan', 'Anda tidak memiliki izin untuk membuat admin di tingkatan ini.');
            return;
        }

        // Must enforce the chosen Kantor is actually within their scope
        if ($this->tingkatan !== 'DPN' && $this->kantor_id) {
            Kantor::scoped(auth()->user())->findOrFail($this->kantor_id);
        }

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => 'admin',
            'tingkatan' => $this->tingkatan,
            'kantor_id' => $this->tingkatan === 'DPN' ? null : $this->kantor_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->user_id], $data);
        
        if (!$this->user_id) {
            $user->assignRole('admin');
        }

        session()->flash('message', $this->user_id ? 'Admin berhasil diperbarui.' : 'Admin baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->user_id = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteAdmin()
    {
        if (!$this->user_id) {
            session()->flash('error', 'ID pengguna tidak valid.');
            $this->closeModal();
            return;
        }

        $user = User::findOrFail($this->user_id);
        
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun Anda sendiri.');
            $this->closeModal();
            return;
        }

        // Security check for regional admins
        if (auth()->user()->tingkatan !== 'DPN') {
            if (!$user->kantor_id) {
                session()->flash('error', 'Anda tidak memiliki otoritas untuk menghapus admin pusat.');
                $this->closeModal();
                return;
            }
            
            // Check if the target user's office is within the current admin's scope
            $isInScope = Kantor::scoped(auth()->user())->where('id', $user->kantor_id)->exists();
            if (!$isInScope) {
                session()->flash('error', 'Anda tidak memiliki akses untuk menghapus admin di wilayah ini.');
                $this->closeModal();
                return;
            }
        }

        $user->delete();
        session()->flash('message', 'Admin berhasil dihapus.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->resetInput();
        $this->resetValidation();
    }

    private function resetInput()
    {
        $this->user_id = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->tingkatan = '';
        $this->kantor_id = null;
    }
}
