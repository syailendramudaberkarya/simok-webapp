<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Anggota</h2>
            <p class="text-sm text-gray-500">Kelola pendaftaran dan status keanggotaan.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openImportModal"
                class="bg-gray-800 hover:bg-gray-900 text-white font-semibold shadow-lg shadow-gray-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-5 py-2.5 text-sm inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Excel
            </button>
            <a href="{{ route('admin.input-manual') }}"
                class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-5 py-2.5 text-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Manual
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-3 text-sm text-emerald-700 rounded-xl bg-emerald-50 border border-emerald-200">
            {{ session('message') }}</div>
    @endif
    @if (session()->has('warning'))
        <div class="p-3 text-sm text-amber-700 rounded-xl bg-amber-50 border border-amber-200">
            {{ session('warning') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-3 text-sm text-red-700 rounded-xl bg-red-50 border border-red-200">
            {{ session('error') }}
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
                    placeholder="Cari Nama, NIK, atau Nomor Anggota...">
            </div>
            <select wire:model.live="statusFilter"
                class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all rounded-xl px-4 py-2.5 text-sm w-full md:w-44">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-sm text-left">
                <thead class="text-[11px] text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100">
                    <tr>
                        <th class="px-5 py-3">Pendaftar</th>
                        <th class="px-5 py-3">NIK</th>
                        <th class="px-5 py-3">No. Anggota</th>
                        <th class="px-5 py-3">Tgl Daftar</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($anggotas as $anggota)
                        <tr class="hover:bg-primary-50/30 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-300 to-primary-500 flex items-center justify-center text-white font-bold text-xs shrink-0">
                                        {{ mb_substr($anggota->nama_lengkap, 0, 1) }}</div>
                                    <div>
                                        <span class="font-semibold text-gray-800">{{ $anggota->nama_lengkap }}</span>
                                        <span class="block text-xs text-gray-400">{{ $anggota->user->email ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-gray-600 text-xs">{{ $anggota->nik }}</td>
                            <td class="px-5 py-3.5 font-mono text-gray-600">{{ $anggota->nomor_anggota ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $anggota->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                @if($anggota->status === 'disetujui')
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>
                                @elseif($anggota->status === 'menunggu')
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">Menunggu</span>
                                @else
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-700">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button wire:click="viewDetail({{ $anggota->id }})"
                                    class="text-xs font-semibold text-primary-600 hover:text-primary-800 transition-colors cursor-pointer">Review
                                    →</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400">Tidak ada data anggota ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $anggotas->links() }}</div>
    </div>

    <!-- Detail Modal -->
    @if($viewModalOpen && $detailAnggota)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
            wire:click.self="closeViewModal">
            <div
                class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Review Detail Pendaftaran</h3>
                    <button wire:click="closeViewModal"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Body -->
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="grid grid-cols-3 items-start py-2 border-b border-gray-50"><span
                                    class="text-xs font-medium text-gray-400 uppercase">Nama</span><span
                                    class="col-span-2 text-sm font-semibold text-gray-800">{{ $detailAnggota->nama_lengkap }}</span>
                            </div>
                            <div class="grid grid-cols-3 items-start py-2 border-b border-gray-50"><span
                                    class="text-xs font-medium text-gray-400 uppercase">NIK</span><span
                                    class="col-span-2 text-sm font-mono text-gray-700">{{ $detailAnggota->nik }}</span>
                            </div>
                            <div class="grid grid-cols-3 items-start py-2 border-b border-gray-50"><span
                                    class="text-xs font-medium text-gray-400 uppercase">TTL</span><span
                                    class="col-span-2 text-sm text-gray-700">{{ $detailAnggota->tempat_lahir }},
                                    {{ $detailAnggota->tanggal_lahir?->format('d/m/Y') }}</span></div>
                            <div class="grid grid-cols-3 items-start py-2 border-b border-gray-50"><span
                                    class="text-xs font-medium text-gray-400 uppercase">Gender</span><span
                                    class="col-span-2 text-sm text-gray-700">{{ $detailAnggota->jenis_kelamin }}</span>
                            </div>
                            <div class="grid grid-cols-3 items-start py-2 border-b border-gray-50"><span
                                    class="text-xs font-medium text-gray-400 uppercase">Alamat</span><span
                                    class="col-span-2 text-sm text-gray-700">{{ $detailAnggota->alamat }}, RT/RW
                                    {{ $detailAnggota->rt_rw }}, {{ $detailAnggota->kelurahan }},
                                    {{ $detailAnggota->kecamatan }}</span></div>
                            <div class="grid grid-cols-3 items-start py-2"><span
                                    class="text-xs font-medium text-gray-400 uppercase">Kontak</span><span
                                    class="col-span-2 text-sm text-gray-700">{{ $detailAnggota->no_telepon }}<br>{{ $detailAnggota->user->email ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex-1 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Foto Wajah</p>
                                @if($detailAnggota->foto_wajah_path)
                                    <button wire:click="openZoomModal('{{ $detailAnggota->foto_wajah_path }}')"
                                        class="w-full group relative cursor-zoom-in">
                                        <img src="{{ route('file.private', ['path' => $detailAnggota->foto_wajah_path]) }}"
                                            class="w-full h-auto object-contain rounded-xl border border-gray-100 shadow-sm group-hover:opacity-90 transition-opacity"
                                            alt="Foto Wajah"
                                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($detailAnggota->nama_lengkap) }}&color=7F9CF5&background=EBF4FF';">
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 rounded-xl">
                                            <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </div>
                                    </button>
                                @else
                                    <div
                                        class="w-full h-32 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex items-center justify-center">
                                        <span class="text-xs text-gray-400 font-medium">Tidak ada unggahan</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen KTP</p>
                                @if($detailAnggota->foto_ktp_path)
                                    <button wire:click="openZoomModal('{{ $detailAnggota->foto_ktp_path }}')"
                                        class="w-full group relative cursor-zoom-in">
                                        <img src="{{ route('file.private', ['path' => $detailAnggota->foto_ktp_path]) }}"
                                            class="w-full h-auto object-contain rounded-xl border border-gray-100 shadow-sm group-hover:opacity-90 transition-opacity"
                                            alt="Foto KTP"
                                            onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%\' height=\'100%\'><rect width=\'100%\' height=\'100%\' fill=\'%23f3f4f6\'/><text x=\'50%\' y=\'50%\' font-family=\'sans-serif\' font-size=\'14\' font-weight=\'bold\' fill=\'%239ca3af\' dominant-baseline=\'middle\' text-anchor=\'middle\'>KTP Hilang/404</text></svg>';">
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10 rounded-xl">
                                            <svg class="w-8 h-8 text-white drop-shadow-md" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </div>
                                    </button>
                                @else
                                    <div
                                        class="w-full h-32 bg-gray-50 border border-dashed border-gray-200 rounded-xl flex items-center justify-center">
                                        <span class="text-xs text-gray-400 font-medium">Tidak ada dokumen</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div>
                        @if($detailAnggota->status !== 'menunggu')
                            <button wire:click="tunda({{ $detailAnggota->id }})"
                                class="text-sm font-medium text-gray-500 hover:text-gray-800 underline underline-offset-4 cursor-pointer">Set
                                ke Menunggu</button>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        @if($detailAnggota->status !== 'ditolak')
                            <button wire:click="openRejectModal({{ $detailAnggota->id }})"
                                class="px-4 py-2 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer">Tolak</button>
                        @endif
                        @if($detailAnggota->status !== 'disetujui')
                            <button wire:click="setujui({{ $detailAnggota->id }})"
                                class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 shadow-lg shadow-emerald-500/25 transition-all cursor-pointer">Setujui
                                & Generate Kartu</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($rejectModalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-red-600">Tolak Pendaftaran</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-4">Alasan penolakan akan dikirimkan ke pemohon melalui email.</p>
                    <textarea wire:model="rejectReason" rows="4"
                        class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-3 text-sm"
                        placeholder="Alasan penolakan (misal: Foto KTP tidak jelas...)"></textarea>
                    @error('rejectReason') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button wire:click="closeRejectModal"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">Batal</button>
                    <button wire:click="tolak"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-500/25 transition-all cursor-pointer">Konfirmasi
                        Penolakan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Zoom Image Modal -->
    @if($zoomModalOpen)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
            wire:click.self="closeZoomModal">
            <div class="relative max-w-[95vw] max-h-[95vh] flex flex-col items-center">
                <button wire:click="closeZoomModal"
                    class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors cursor-pointer">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img src="{{ $zoomImageUrl }}" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl"
                    alt="Zoomed Image">
                <div class="mt-4 text-white text-sm font-medium bg-black/20 px-4 py-2 rounded-full backdrop-blur-sm">
                    Gunakan scroll untuk memperbesar / klik di luar gambar untuk menutup
                </div>
            </div>
        </div>
    @endif

    <!-- Import Excel Modal -->
    @if($importModalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            wire:click.self="closeImportModal">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">Import Data Anggota</h3>
                    <button wire:click="closeImportModal"
                        class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center transition-colors cursor-pointer text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="import">
                    <div class="p-6">
                        <div
                            class="text-sm text-gray-500 mb-6 bg-blue-50 text-blue-800 p-4 rounded-xl border border-blue-100">
                            <strong>Format Wajib:</strong> Pastikan Excel sesuai urutan berikut: No, No Anggota, Nama, NIK,
                            Tempat Lahir, Tgl Lahir, Telepon, Email, Alamat, RT, RW, Jenis Kelamin, Agama, Status Kawin,
                            Pekerjaan, Kewarganegaraan, Gol Darah, Provinsi, Kabupaten, Kecamatan, Kelurahan, Username,
                            Password, Status, Tgl Daftar.
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">Pilih File Excel (.xlsx, .csv)
                                <span class="text-red-500">*</span></label>
                            <input type="file" wire:model="importFile" accept=".xlsx,.csv"
                                class="w-full bg-gray-50 border border-black/10 rounded-xl px-4 py-2 text-sm">
                            <div wire:loading wire:target="importFile" class="text-xs text-primary-600 mt-2 font-medium">
                                Unggah proses...</div>
                            @error('importFile') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                        <button type="button" wire:click="closeImportModal"
                            class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-200 transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/25 transition-all cursor-pointer flex items-center gap-2">
                            <span wire:loading wire:target="import">Memproses...</span>
                            <span wire:loading.remove wire:target="import">Unggah & Proses</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>