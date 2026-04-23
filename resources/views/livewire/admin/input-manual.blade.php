<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.manajemen') }}"
            class="w-8 h-8 rounded-xl bg-white/80 border border-gray-200 flex items-center justify-center hover:bg-gray-100 transition-colors">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Input Manual Anggota</h2>
            <p class="text-sm text-gray-500">Pendaftaran langsung oleh admin — anggota otomatis disetujui.</p>
        </div>
    </div>

    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6 sm:p-8">
        <form wire:submit.prevent="openConfirm" class="space-y-8">
            <!-- Section 1: Nomor Anggota -->
            <!-- Section 1: Dokumen -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">1</span>
                    Dokumen
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Foto KTP -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Foto KTP</label>
                        <label for="fotoKtp"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-200 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors overflow-hidden relative">
                            @if ($this->getKtpPreviewUrl())
                                <img src="{{ $this->getKtpPreviewUrl() }}"
                                    class="w-full h-full object-contain p-2 rounded-2xl">
                            @else
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4-4m0 0l4 4m-4-4v12M20 8l-4 4m0 0l-4-4m4 4V0" />
                                    </svg>
                                    <p class="text-sm text-gray-500"><span class="font-semibold text-primary-600">Klik untuk upload KTP</span></p>
                                    <p class="text-xs text-gray-400 mt-1">Sistem akan memindai data secara otomatis</p>
                                </div>
                            @endif
                            <input wire:model="fotoKtp" id="fotoKtp" type="file" class="hidden"
                                accept="image/jpeg,image/png" />
                        </label>
                        
                        <div wire:loading.flex wire:target="fotoKtp, scanKtp"
                            class="text-[11px] w-full text-indigo-600 mt-2 items-center gap-2 font-medium bg-indigo-50/50 p-2 rounded-lg border border-indigo-100/50">
                            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Mengunggah & Memindai KTP Otomatis...</span>
                        </div>

                        @error('fotoKtp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @if($scanError) <span class="text-red-500 text-xs mt-1 block font-bold">⚠️ {{ $scanError }}</span> @endif
                    </div>

                    <!-- Foto Wajah -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Foto Wajah (Selfie)</label>
                        <label for="fotoWajah"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-200 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors overflow-hidden">
                            @if ($this->getWajahPreviewUrl())
                                <img src="{{ $this->getWajahPreviewUrl() }}"
                                    class="w-full h-full object-cover p-2 rounded-2xl">
                            @else
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="text-sm text-gray-500"><span class="font-semibold text-primary-600">Klik upload foto wajah</span></p>
                                    <p class="text-xs text-gray-400 mt-1">Pastikan wajah terlihat jelas</p>
                                </div>
                            @endif
                            <input wire:model="fotoWajah" id="fotoWajah" type="file" class="hidden"
                                accept="image/jpeg,image/png" />
                        </label>
                        <div wire:loading.flex wire:target="fotoWajah"
                            class="text-[11px] text-indigo-600 mt-2 items-center gap-2 font-medium bg-indigo-50/50 p-2 rounded-lg border border-indigo-100/50">
                            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Mengunggah...
                        </div>
                        @error('fotoWajah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Data Pribadi -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">2</span>
                    Data Pribadi
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" wire:model="nik"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('nik') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" wire:model="namaLengkap"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('namaLengkap') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" wire:model="tempatLahir"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('tempatLahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" wire:model="tanggalLahir"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('tanggalLahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select wire:model="jenisKelamin"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenisKelamin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Agama</label>
                        <select wire:model="agama"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                        @error('agama') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Golongan Darah</label>
                        <select wire:model="golonganDarah"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                        @error('golonganDarah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Status Perkawinan</label>
                        <select wire:model="statusPerkawinan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        @error('statusPerkawinan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Pekerjaan</label>
                        <input type="text" wire:model="pekerjaan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('pekerjaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kewarganegaraan</label>
                        <input type="text" wire:model="kewarganegaraan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('kewarganegaraan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Alamat</label>
                        <textarea wire:model="alamat" rows="2"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"></textarea>
                        @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">RT/RW</label>
                        <input type="text" wire:model="rtRw"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('rtRw') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" wire:model.live="propinsi"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: Jawa Timur">
                        @error('propinsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @error('idpropinsi') <span class="text-red-500 text-[10px] mt-1 block font-bold">⚠️ Provinsi tidak ditemukan di database</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kota / Kabupaten</label>
                        <input type="text" wire:model.live="kabupaten"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: SAMPANG atau Kota Surabaya">
                        @error('kabupaten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @error('idkabupaten') <span class="text-red-500 text-[10px] mt-1 block font-bold">⚠️ Kota/Kab tidak ditemukan di database</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kecamatan</label>
                        <input type="text" wire:model.live="kecamatan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: SAMPANG">
                        @error('kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @error('idkecamatan') <span class="text-red-500 text-[10px] mt-1 block font-bold">⚠️ Kecamatan tidak ditemukan di database</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kelurahan / Desa</label>
                        <input type="text" wire:model.live="kelurahan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: Dalpenang">
                        @error('kelurahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @error('idkelurahan') <span class="text-red-500 text-[10px] mt-1 block font-bold">⚠️ Kelurahan tidak ditemukan di database</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Akun -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">3</span>
                    Informasi Akun
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">No. Telepon</label>
                        <input type="text" wire:model="noTelepon"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('noTelepon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Email Utama</label>
                        <input type="email" wire:model="email"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Username</label>
                        <input type="text" wire:model="username"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Password Sementara</label>
                        <input type="text" wire:model="password"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-100">
                <button type="submit"
                    class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-6 py-2.5 text-sm cursor-pointer">Tinjau
                    & Simpan</button>
            </div>
        </form>
    </div>

    <!-- Confirm Modal -->
    @if($confirmModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Nomor anggota <strong
                        class="text-gray-900">{{ $nomorAnggotaType === 'manual' ? "[$manualNomorAnggota]" : '[Otomatis]' }}</strong>
                    akan ditetapkan untuk <strong class="text-gray-900">{{ $namaLengkap }}</strong>. Anggota otomatis
                    disetujui. Lanjutkan?
                </p>
                <div class="flex gap-2 justify-center">
                    <button wire:click="closeConfirm"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100 border border-gray-200 transition-colors cursor-pointer">Batal</button>
                    <button wire:click="simpan" wire:loading.attr="disabled"
                        class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-5 py-2 text-sm cursor-pointer">
                        <span wire:loading.remove wire:target="simpan">Ya, Simpan</span>
                        <span wire:loading wire:target="simpan">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>