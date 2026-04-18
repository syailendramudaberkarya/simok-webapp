<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Admin</h2>
            <p class="text-sm text-gray-500">Kelola akses admin cabang sesuai wilayah organisasi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="createAdmin"
                class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-4 py-2.5 text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Tambah Admin
            </button>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="p-3 text-sm text-red-700 rounded-xl bg-red-50 border border-red-200 mb-4">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('message'))
        <div class="p-3 text-sm text-emerald-700 rounded-xl bg-emerald-50 border border-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters + Table -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl pl-10 pr-4 py-2.5 text-sm"
                    placeholder="Cari nama, email, username...">
            </div>
            <select wire:model.live="tingkatanFilter"
                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all rounded-xl px-4 py-2.5 text-sm w-full md:w-44">
                <option value="">Semua Tingkatan</option>
                @foreach($availableTingkatan as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm text-left">
                <thead class="text-[11px] text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3">Nama & Kontak</th>
                        <th class="px-5 py-3">Username</th>
                        <th class="px-5 py-3">Tingkatan</th>
                        <th class="px-5 py-3">Asal Kantor</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-primary-50/30 transition-colors group">
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                <div class="text-[11px] text-gray-500 mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-700 font-mono text-xs">
                                {{ $user->username }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                    {{ $user->tingkatan === 'DPN' ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}
                                    {{ $user->tingkatan === 'DPD' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : '' }}
                                    {{ $user->tingkatan === 'DPC' ? 'bg-blue-100 text-blue-700 border-blue-200' : '' }}
                                    {{ $user->tingkatan === 'PR' ? 'bg-purple-100 text-purple-700 border-purple-200' : '' }}
                                    {{ $user->tingkatan === 'PAR' ? 'bg-red-100 text-red-700 border-red-200' : '' }}">
                                    {{ $user->tingkatan }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 text-xs">
                                {{ $user->kantor ? $user->kantor->nama_kantor : 'Pusat Nasional' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="editAdmin({{ $user->id }})"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all cursor-pointer"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                        title="Hapus" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 {{ $user->id === auth()->id() ? 'opacity-30' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                <span class="text-sm">Data admin cabang tidak ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create / Edit Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                <form wire:submit.prevent="saveAdmin">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">{{ $user_id ? 'Edit Admin' : 'Tambah Admin Baru' }}</h3>
                        <button type="button" wire:click="closeModal"
                            class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto flex-1 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Tingkatan Organisasi <span class="text-red-500">*</span></label>
                                <select wire:model.live="tingkatan"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                    <option value="">Pilih Tingkatan</option>
                                    @foreach($availableTingkatan as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                                @error('tingkatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($tingkatan && $tingkatan !== 'DPN')
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Asal Kantor <span class="text-red-500">*</span></label>
                                <select wire:model="kantor_id"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                    <option value="">Pilih Kantor {{ $tingkatan }}</option>
                                    @foreach($kantorOptions as $kantor)
                                        <option value="{{ $kantor->id }}">{{ $kantor->nama_kantor }}</option>
                                    @endforeach
                                </select>
                                @error('kantor_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input wire:model="name" type="text"
                                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Email Utama <span class="text-red-500">*</span></label>
                                <input wire:model="email" type="email"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Username Login <span class="text-red-500">*</span></label>
                                <input wire:model="username" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">
                                Password {{ $user_id ? '(Abaikan jika tidak diubah)' : '*' }}
                            </label>
                            <input wire:model="password" type="text" placeholder="Masukkan password"
                                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all cursor-pointer">
                            Simpan Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation -->
    @if($isDeleteModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden text-center">
                <div class="p-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2">Hapus Akun Admin</h3>
                    <p class="text-sm text-gray-500">Anda yakin ingin menghapus akun admin ini? Akses mereka akan dicabut seketika.</p>
                </div>
                <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-center gap-3">
                    <button wire:click="closeModal"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer focus:outline-none">
                        Batal
                    </button>
                    <button wire:click="deleteAdmin"
                        class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all cursor-pointer focus:outline-none">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
