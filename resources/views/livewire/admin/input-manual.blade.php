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
            <div>
                <!-- <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Metode Penomoran</label>
                        <div class="flex items-center gap-4 py-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="nomorAnggotaType" type="radio" value="auto"
                                    class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-700">Otomatis</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input wire:model.live="nomorAnggotaType" type="radio" value="manual"
                                    class="w-4 h-4 text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-gray-700">Manual</span>
                            </label>
                        </div>
                    </div> -->
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">1</span>
                    Tingkatan Organisasi
                </h3> <select wire:model="tingkatan"
                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                    <option value="DPN">DPN (Nasional)</option>
                    <option value="DPD">DPD (Provinsi)</option>
                    <option value="DPC">DPC (Kabupaten/Kota)</option>
                    <option value="PR">PR (Kecamatan)</option>
                    <option value="PAR">PAR (Kelurahan/Desa)</option>
                </select>
                @error('tingkatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
                @if($nomorAnggotaType === 'manual')
                    <div>
                        <label for="manualNomorAnggota" class="block mb-2 text-sm font-medium text-gray-700">Nomor
                            Custom (5 Digit)</label>
                        <input type="text" wire:model.live.debounce.500ms="manualNomorAnggota" id="manualNomorAnggota"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm font-mono"
                            placeholder="00123">
                        @error('manualNomorAnggota') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Section 2: Dokumen -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">2</span>
                    Dokumen
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Foto KTP</label>
                        <input wire:model="fotoKtp" type="file" accept="image/jpeg,image/png"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        @error('fotoKtp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @if($this->getKtpPreviewUrl())<img src="{{ $this->getKtpPreviewUrl() }}"
                        class="h-24 object-contain rounded-xl border mt-2">@endif
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Foto Wajah</label>
                        <input wire:model="fotoWajah" type="file" accept="image/jpeg,image/png"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        @error('fotoWajah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                        @if($this->getWajahPreviewUrl())<img src="{{ $this->getWajahPreviewUrl() }}"
                        class="h-24 w-20 object-cover rounded-xl border mt-2">@endif
                    </div>
                </div>
            </div>

            <!-- Section 3: Data Pribadi -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">3</span>
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
                        <select wire:model.live="idpropinsi"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}">{{ $province->propinsi }}</option>
                            @endforeach
                        </select>
                        @error('idpropinsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kota / Kabupaten</label>
                        <select wire:model.live="idkabupaten"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                            {{ empty($cities) ? 'disabled' : '' }}>
                            <option value="">Pilih Kota/Kab</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->kabupaten }}</option>
                            @endforeach
                        </select>
                        @error('idkabupaten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kecamatan</label>
                        <select wire:model.live="idkecamatan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                            {{ empty($districts) ? 'disabled' : '' }}>
                            <option value="">Pilih Kecamatan</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->kecamatan }}</option>
                            @endforeach
                        </select>
                        @error('idkecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kelurahan / Desa</label>
                        <select wire:model.live="idkelurahan"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"
                            {{ empty($villages) ? 'disabled' : '' }}>
                            <option value="">Pilih Kelurahan/Desa</option>
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}">{{ $village->kelurahan }}</option>
                            @endforeach
                        </select>
                        @error('idkelurahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 4: Akun -->
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span
                        class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex items-center justify-center">4</span>
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