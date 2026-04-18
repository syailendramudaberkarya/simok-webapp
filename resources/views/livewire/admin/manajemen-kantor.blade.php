<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Kantor</h2>
            <p class="text-sm text-gray-500">Kelola data dewan pimpinan dan kantor tingkat daerah hingga ranting.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openImportModal"
                class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-primary-600 font-semibold shadow-sm transition-all rounded-xl px-4 py-2.5 text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Excel
            </button>
            <button wire:click="createKantor"
                class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-4 py-2.5 text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kantor
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
    @if (session()->has('warning'))
        <div class="p-3 text-sm text-amber-700 rounded-xl bg-amber-50 border border-amber-200">{{ session('warning') }}
        </div>
    @endif

    <!-- Filters + Table -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            @php
                $tingkatan = auth()->user()->tingkatan ?? 'DPN';
                $hierarchy = ['DPN' => 1, 'DPD' => 2, 'DPC' => 3, 'PR' => 4, 'PAR' => 5];
                $currentHierarchy = $hierarchy[$tingkatan] ?? 1;
            @endphp
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl pl-10 pr-4 py-2.5 text-sm"
                    placeholder="Cari nama kantor, alamat, atau kabupaten...">
            </div>
            <select wire:model.live="jenjangFilter"
                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all rounded-xl px-4 py-2.5 text-sm w-full md:w-44">
                <option value="">Semua Jenjang</option>
                @if($currentHierarchy <= 1) <option value="DPN">DPN (Nasional)</option> @endif
                @if($currentHierarchy <= 1) <option value="DPD">DPD (Provinsi)</option> @endif
                @if($currentHierarchy <= 2) <option value="DPC">DPC (Kabupaten/Kota)</option> @endif
                @if($currentHierarchy <= 3) <option value="PR">PR (Kecamatan)</option> @endif
                @if($currentHierarchy <= 4) <option value="PAR">PAR (Kelurahan/Desa)</option> @endif
            </select>
            <select wire:model.live="statusFilter"
                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all rounded-xl px-4 py-2.5 text-sm w-full md:w-44">
                <option value="">Semua Status</option>
                <option value="Aktif">Aktif</option>
                <option value="Non-Aktif">Non-Aktif</option>
            </select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead class="text-[11px] text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Kantor</th>
                        <th class="px-4 py-3">Jenjang</th>
                        <th class="px-4 py-3">Telepon</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3">Kode Pos</th>
                        <th class="px-4 py-3">Provinsi</th>
                        <th class="px-4 py-3">Kabupaten</th>
                        <th class="px-4 py-3">Kecamatan</th>
                        <th class="px-4 py-3">Kelurahan</th>
                        <th class="px-4 py-3">Latitude</th>
                        <th class="px-4 py-3">Longitude</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($kantors as $index => $kantor)
                        <tr class="hover:bg-primary-50/30 transition-colors group">
                            <td class="px-4 py-3.5 text-gray-500">{{ $kantors->firstItem() + $index }}</td>
                            <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $kantor->nama_kantor }}</td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border
                                    {{ $kantor->jenjang === 'DPN' ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}
                                    {{ $kantor->jenjang === 'DPD' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : '' }}
                                    {{ $kantor->jenjang === 'DPC' ? 'bg-blue-100 text-blue-700 border-blue-200' : '' }}
                                    {{ $kantor->jenjang === 'PR' ? 'bg-purple-100 text-purple-700 border-purple-200' : '' }}
                                    {{ $kantor->jenjang === 'PAR' ? 'bg-red-100 text-red-700 border-red-200' : '' }}
                                ">
                                    {{ $kantor->jenjang }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->telepon ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-500">{{ $kantor->email ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700 truncate max-w-[200px]" title="{{ $kantor->alamat }}">{{ $kantor->alamat ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->kode_pos ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->provinsi ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->kabupaten ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->kecamatan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $kantor->kelurahan ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-500">{{ $kantor->latitude ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-500">{{ $kantor->longitude ?? '-' }}</td>
                            <td class="px-4 py-3.5 text-center">
                                <button wire:click="toggleStatus({{ $kantor->id }})"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all cursor-pointer inline-flex
                                        {{ $kantor->status === 'Aktif' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $kantor->status }}
                                </button>
                            </td>
                            <td class="px-4 py-3.5 text-right sticky right-0 bg-white group-hover:bg-primary-50/50">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="editKantor({{ $kantor->id }})"
                                        class="p-1.5 text-gray-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all cursor-pointer"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $kantor->id }})"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all cursor-pointer"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-5 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-200 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-sm">Data kantor tidak ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $kantors->links() }}
        </div>
    </div>

    <!-- Create / Edit Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            wire:click.self="closeModal">
            <div
                class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                <form wire:submit.prevent="saveKantor">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">{{ $kantor_id ? 'Edit Kantor' : 'Tambah Kantor Baru' }}
                        </h3>
                        <button type="button" wire:click="closeModal"
                            class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto flex-1 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Nama Kantor <span
                                        class="text-red-500">*</span></label>
                                <input wire:model="nama_kantor" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('nama_kantor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Jenjang <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.live="jenjang"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                    <option value="">Pilih Jenjang</option>
                                    @if($currentHierarchy <= 1) <option value="DPN">DPN (Nasional)</option> @endif
                                    @if($currentHierarchy <= 1) <option value="DPD">DPD (Provinsi)</option> @endif
                                    @if($currentHierarchy <= 2) <option value="DPC">DPC (Kabupaten/Kota)</option> @endif
                                    @if($currentHierarchy <= 3) <option value="PR">PR (Kecamatan)</option> @endif
                                    @if($currentHierarchy <= 4) <option value="PAR">PAR (Kelurahan/Desa)</option> @endif
                                </select>
                                @error('jenjang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            @if(in_array($jenjang, ['DPD', 'DPC', 'PR', 'PAR']) && $currentHierarchy < 2)
                                <div>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Provinsi <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model.live="idpropinsi"
                                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($propinsis as $prop)
                                            <option value="{{ $prop->id }}">{{ Str::title(strtolower($prop->propinsi)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('idpropinsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if(in_array($jenjang, ['DPC', 'PR', 'PAR']) && $currentHierarchy < 3)
                                <div>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Kabupaten/Kota <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model.live="idkabupaten"
                                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                                        {{ (!$idpropinsi && $currentHierarchy < 2) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                        @foreach($kabupatens as $kab)
                                            <option value="{{ $kab->id }}">{{ Str::title(strtolower($kab->kabupaten)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('idkabupaten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if(in_array($jenjang, ['PR', 'PAR']) && $currentHierarchy < 4)
                                <div>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Kecamatan <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model.live="idkecamatan"
                                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                                        {{ (!$idkabupaten && $currentHierarchy < 3) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kec)
                                            <option value="{{ $kec->id }}">{{ strtoupper($kec->kecamatan) }}</option>
                                        @endforeach
                                    </select>
                                    @error('idkecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if($jenjang === 'PAR' && $currentHierarchy < 5)
                                <div>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Kelurahan/Desa <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model.live="idkelurahan"
                                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                                        {{ (!$idkecamatan && $currentHierarchy < 4) ? 'disabled' : '' }}>
                                        <option value="">Pilih Kelurahan/Desa</option>
                                        @foreach($kelurahans as $kel)
                                            <option value="{{ $kel->id }}">{{ Str::title(strtolower($kel->kelurahan)) }}</option>
                                        @endforeach
                                    </select>
                                    @error('idkelurahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            @if($jenjang && !in_array($jenjang, ['DPN', 'DPD']))
                                <div class="{{ count($parentOptions) === 1 ? 'hidden' : '' }}">
                                    <label class="block mb-1.5 text-sm font-medium text-gray-700">Induk Kantor <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="parent_id"
                                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                        <option value="">Pilih Induk Kantor</option>
                                        @foreach($parentOptions as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->nama_kantor }}</option>
                                        @endforeach
                                    </select>
                                    @error('parent_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                    @if(empty($parentOptions))
                                        <p class="text-xs text-amber-600 mt-1">Pilih wilayah terlebih dahulu untuk memuat daftar
                                            Induk Kantor.</p>
                                    @endif
                                </div>
                            @endif

                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">No. Telepon</label>
                                <input wire:model="telepon" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('telepon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Email</label>
                                <input wire:model="email" type="email"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                            <textarea wire:model="alamat" rows="2"
                                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"></textarea>
                            @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Kode Pos</label>
                                <input wire:model="kode_pos" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('kode_pos') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Latitude <span class="text-xs text-gray-400 font-normal">(opsional)</span></label>
                                <input wire:model="latitude" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('latitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700">Longitude <span class="text-xs text-gray-400 font-normal">(opsional)</span></label>
                                <input wire:model="longitude" type="text"
                                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                                @error('longitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all cursor-pointer">
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
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
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2">Hapus Data Kantor</h3>
                    <p class="text-sm text-gray-500">
                        Anda yakin ingin menghapus data kantor ini? Semua data yang terkait tidak dapat dikembalikan.
                    </p>
                </div>
                <div class="bg-gray-50/80 px-6 py-4 flex items-center justify-center gap-3">
                    <button wire:click="closeModal"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 transition-colors cursor-pointer focus:outline-none">
                        Batal
                    </button>
                    <button wire:click="deleteKantor"
                        class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-600/20 transition-all cursor-pointer focus:outline-none">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Import Modal -->
    @if($isImportModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closeImportModal">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                <form wire:submit.prevent="import">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Import Data Kantor (Excel)</h3>
                        <button type="button" wire:click="closeImportModal" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-blue-800 text-sm">
                            <p class="font-semibold mb-2">Pastikan file memiliki kolom dengan urutan/nama berikut:</p>
                            <p class="text-xs font-mono bg-white p-2 rounded border border-blue-200">No, Nama Kantor, Jenjang, Telepon, Email, Alamat, Kode Pos, Provinsi, Kabupaten, Kecamatan, Kelurahan, Latitude, Longitude, Status</p>
                            <p class="mt-2 text-xs text-blue-600">Simpan sebagai .xlsx atau .csv untuk memproses</p>
                        </div>

                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-700">Pilih File Excel / CSV <span class="text-red-500">*</span></label>
                            <input wire:model="importFile" type="file" accept=".xlsx, .xls, .csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                            @error('importFile') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div wire:loading wire:target="importFile" class="mt-2 text-sm text-primary-600">
                            Sedang mengunggah file...
                        </div>
                        <div wire:loading wire:target="import" class="mt-2 text-sm text-primary-600 font-medium">
                            Sedang memproses dan mengimpor data, mohon tunggu...
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <button type="button" wire:click="closeImportModal" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 shadow-lg shadow-primary-500/25 transition-all cursor-pointer disabled:opacity-50">
                            Proses Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>