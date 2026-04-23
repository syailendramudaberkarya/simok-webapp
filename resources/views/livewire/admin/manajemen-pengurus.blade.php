<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2
                class="text-2xl font-bold bg-gradient-to-r from-primary-700 to-primary-500 bg-clip-text text-transparent">
                Manajemen Pengurus</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data kepengurusan organisasi</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openForm"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-primary-500/30 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pengurus
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div
            class="mb-4 bg-emerald-50/50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 backdrop-blur-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd" />
            </svg>
            <p class="text-sm font-medium">{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div
            class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
            <div class="relative w-full sm:max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="search"
                    class="bg-white border text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 border-gray-200 shadow-sm transition-all"
                    placeholder="Cari nama, no anggota, atau kantor...">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4">Pengurus</th>
                        <th scope="col" class="px-6 py-4">Jabatan</th>
                        <th scope="col" class="px-6 py-4">Wilayah / Kantor</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengurus as $item)
                        <tr class="hover:bg-primary-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="font-bold text-gray-900 group-hover:text-primary-700 transition-colors">{{ $item->nama }}</span>
                                    <span class="text-[11px] text-gray-500 font-mono mt-0.5">{{ $item->noanggota }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="bg-primary-100 text-primary-700 text-[10px] font-bold px-2 py-0.5 rounded border border-primary-200 uppercase tracking-tighter">
                                            {{ $item->jabatan }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-gray-600 font-medium">
                                        {{ $item->kategorijabatan }} &raquo; {{ $item->subkategorijabatan }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-700 font-semibold">{{ $item->kantor ?: '-' }}</span>
                                    @if($item->keterangan)
                                        <span class="text-[11px] text-gray-400 mt-1 italic">{{ $item->keterangan }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 transition-opacity">
                                    <button wire:click="openForm({{ $item->id }})"
                                        class="p-2 text-amber-500 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors border border-amber-200/50"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="p-2 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors border border-rose-200/50"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100 shadow-inner">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada data pengurus yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengurus->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $pengurus->links() }}
            </div>
        @endif
    </div>

    <!-- Form Modal -->
    @if($isFormOpen)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeForm">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 relative z-10">
                    <form wire:submit.prevent="save">
                        <div
                            class="bg-gray-50 px-6 py-5 border-b border-gray-100 flex justify-between items-center text-gray-900">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                                    {{ $pengurus_id ? 'Detail Pengurus' : 'Tambah Pengurus' }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Lengkapi informasi kepengurusan di bawah ini.</p>
                            </div>
                            <button type="button" wire:click="closeForm"
                                class="text-gray-400 hover:text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-xl p-2 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="bg-white px-6 py-6 max-h-[70vh] overflow-y-auto">
                            <div class="space-y-5">
                                <!-- Member Selection -->
                                <div class="relative" x-data="{ open: @entangle('memberSearch').live }">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="memberSearch">Cari
                                        & Pilih Anggota <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" wire:model.live.debounce.300ms="memberSearch" id="memberSearch"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-3 transition-all"
                                            placeholder="Ketik minimal 3 karakter nama atau no anggota...">
                                    </div>

                                    @if(!empty($searchResults))
                                        <div
                                            class="absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-gray-100 py-2 max-h-60 overflow-y-auto">
                                            @foreach($searchResults as $result)
                                                <button type="button"
                                                    wire:click="selectMember({{ $result['id'] }}, '{{ $result['nama_lengkap'] }}', '{{ $result['nomor_anggota'] }}')"
                                                    class="w-full text-left px-4 py-3 hover:bg-primary-50 flex flex-col transition-colors border-b border-gray-50 last:border-0 relative">
                                                    <span
                                                        class="text-sm font-bold text-gray-900">{{ $result['nama_lengkap'] }}</span>
                                                    <span
                                                        class="text-[11px] text-gray-500 font-mono">{{ $result['nomor_anggota'] }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($nama)
                                        <div
                                            class="mt-3 bg-primary-50 border border-primary-200 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-primary-500 text-white flex items-center justify-center font-bold shadow-md shadow-primary-500/30">
                                                    {{ substr($nama, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-primary-900">{{ $nama }}</p>
                                                    <p class="text-[11px] text-primary-700 font-mono">{{ $noanggota }}</p>
                                                </div>
                                            </div>
                                            <button type="button" wire:click="$set('nama', '')"
                                                class="text-primary-400 hover:text-primary-600 p-1.5 bg-white rounded-lg shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                    @error('noanggota') <span
                                    class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="kategorijabatan">Kategori Jabatan <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model.live="kategorijabatan" id="kategorijabatan"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all">
                                            <option value="">Pilih Kategori</option>
                                            @foreach(array_keys($categories) as $cat)
                                                <option value="{{ $cat }}">{{ $cat }}</option>
                                            @endforeach
                                        </select>
                                        @error('kategorijabatan') <span
                                        class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="subkategorijabatan">Sub-Kategori <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model="subkategorijabatan" id="subkategorijabatan"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all"
                                            {{ !$kategorijabatan ? 'disabled' : '' }}>
                                            <option value="">Pilih Sub-Kategori</option>
                                            @if($kategorijabatan && isset($categories[$kategorijabatan]))
                                                @foreach($categories[$kategorijabatan] as $sub)
                                                    <option value="{{ $sub }}">{{ $sub }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('subkategorijabatan') <span
                                        class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="jabatan">Jabatan Struktural <span class="text-red-500">*</span></label>
                                        <select wire:model="jabatan" id="jabatan"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all">
                                            <option value="">Pilih Jabatan</option>
                                            @foreach($jabatans as $jab)
                                                <option value="{{ $jab }}">{{ $jab }}</option>
                                            @endforeach
                                        </select>
                                        @error('jabatan') <span
                                        class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="kantor_id">Lokasi Kantor / Cabang <span
                                                class="text-red-500">*</span></label>
                                        <select wire:model="kantor_id" id="kantor_id"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all">
                                            <option value="">Pilih Kantor</option>
                                            @foreach($kantors as $k)
                                                <option value="{{ $k->id }}">{{ $k->jenjang }} - {{ $k->nama_kantor }}</option>
                                            @endforeach
                                        </select>
                                        @error('kantor_id') <span
                                        class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="periode_mulai">Periode Mulai</label>
                                        <input type="date" wire:model="periode_mulai" id="periode_mulai"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all">
                                        @error('periode_mulai') <span class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                            for="periode_selesai">Periode Selesai</label>
                                        <input type="date" wire:model="periode_selesai" id="periode_selesai"
                                            class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all">
                                        @error('periode_selesai') <span class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 border border-gray-200 rounded-2xl transition-all hover:bg-gray-100">
                                        <input type="checkbox" wire:model="status_aktif" class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                        <span class="text-sm font-semibold text-gray-700">Status Aktif (Kepengurusan masih berjalan)</span>
                                    </label>
                                    @error('status_aktif') <span class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5"
                                        for="keterangan">Keterangan Tambahan</label>
                                    <textarea wire:model="keterangan" id="keterangan" rows="3"
                                        class="bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-2xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition-all"
                                        placeholder="Catatan opsional mengenai jabatan ini..."></textarea>
                                    @error('keterangan') <span
                                    class="text-red-500 text-xs mt-1.5 block px-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-5 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex justify-center items-center rounded-2xl bg-gradient-to-r from-primary-600 to-primary-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-primary-500/30 hover:shadow-primary-500/40 hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Data
                            </button>
                            <button type="button" wire:click="closeForm"
                                class="inline-flex justify-center items-center rounded-2xl bg-white border border-gray-200 px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($isDeleteModalOpen)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeDeleteModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-200 relative z-10">
                    <div class="bg-white px-6 pt-6 pb-5">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-50 sm:mx-0 sm:h-10 sm:w-10 border border-rose-100 shadow-sm">
                                <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Hapus Pengurus</h3>
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>Apakah Anda yakin ingin menghapus data kepengurusan ini? Data akan dihapus secara
                                        permanen.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="button" wire:click="delete"
                            class="inline-flex justify-center items-center rounded-2xl bg-rose-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-rose-600/20 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 transition-all">
                            Ya, Hapus
                        </button>
                        <button type="button" wire:click="closeDeleteModal"
                            class="inline-flex justify-center items-center rounded-2xl bg-white border border-gray-200 px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all focus:outline-none">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>