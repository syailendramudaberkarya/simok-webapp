<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Card -->
    <div class="glass-light rounded-2xl overflow-hidden">
        <!-- Gradient Header -->
        <div class="h-28 bg-gradient-to-r from-primary-600 to-primary-400 relative">
            <div class="absolute -bottom-10 left-6">
                @if($anggota->foto_wajah_path)
                    <img src="{{ route('file.private', ['path' => $anggota->foto_wajah_path]) }}" alt="Foto" class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-lg">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-300 to-primary-500 border-4 border-white shadow-lg flex items-center justify-center text-white font-bold text-2xl">
                        {{ mb_substr($anggota->nama_lengkap, 0, 1) }}
                    </div>
                @endif
            </div>
        </div>
        <div class="pt-14 px-6 pb-6">
            @if (session()->has('message'))
                <div class="p-3 mb-4 text-sm text-emerald-700 rounded-xl bg-emerald-50 border border-emerald-200">{{ session('message') }}</div>
            @endif

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $anggota->nama_lengkap }}</h2>
                    <p class="text-sm text-gray-500">{{ $anggota->user->email ?? '' }}</p>
                </div>
                @if($anggota->nomor_anggota)
                    <div class="mt-2 sm:mt-0 px-4 py-2 rounded-xl bg-primary-50 border border-primary-200">
                        <span class="text-[10px] text-primary-500 font-bold uppercase tracking-widest block">Nomor Anggota</span>
                        <span class="text-lg font-bold font-mono text-primary-700 tracking-wider">{{ $anggota->nomor_anggota }}</span>
                    </div>
                @else
                    <span class="mt-2 sm:mt-0 text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">⏳ Menunggu Verifikasi</span>
                @endif
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">NIK</span>
                    <span class="block text-gray-800 font-mono mt-0.5">{{ $anggota->nik }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tempat Lahir</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->tempat_lahir }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Lahir</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->tanggal_lahir?->translatedFormat('d F Y') }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gender & Agama</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->jenis_kelamin }} · {{ $anggota->agama }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3 col-span-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Alamat</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->alamat }}, RT/RW {{ $anggota->rt_rw }}, {{ $anggota->kelurahan }}, {{ $anggota->kecamatan }}</span>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-xl bg-amber-50/50 border border-amber-100 text-xs text-amber-700">
                <span class="font-bold">Catatan:</span> Data KTP (Nama, NIK, TTL) tidak dapat diubah oleh anggota. Hubungi administrator jika ada kesalahan data.
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="glass-light rounded-2xl p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-5">Ubah Data Kontak & Alamat</h3>
        
        <form wire:submit.prevent="updateProfil" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" id="email" class="input-light w-full rounded-xl px-4 py-2.5 text-sm">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="no_telepon" class="block mb-1.5 text-sm font-medium text-gray-700">No. Telepon/WA</label>
                    <input type="text" wire:model="no_telepon" id="no_telepon" class="input-light w-full rounded-xl px-4 py-2.5 text-sm">
                    @error('no_telepon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label for="alamat" class="block mb-1.5 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea wire:model="alamat" id="alamat" rows="2" class="input-light w-full rounded-xl px-4 py-2.5 text-sm"></textarea>
                @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700">Perbarui Foto Wajah</label>
                <input wire:model="foto_wajah_baru" type="file" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                <span class="text-xs text-gray-400 mt-1 block">JPG/PNG (Maks 5MB)</span>
                @error('foto_wajah_baru') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                @if ($this->getFotoWajahPreviewUrl())
                    <img src="{{ $this->getFotoWajahPreviewUrl() }}" class="w-20 h-24 object-cover rounded-xl border mt-3">
                @endif
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" wire:loading.attr="disabled" class="btn-gradient rounded-xl px-6 py-2.5 text-sm cursor-pointer">
                    <span wire:loading.remove wire:target="updateProfil">Simpan Perubahan</span>
                    <span wire:loading wire:target="updateProfil">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
