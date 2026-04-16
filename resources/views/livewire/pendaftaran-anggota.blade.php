<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-3xl">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white">Pendaftaran Anggota</h1>
            <p class="text-sm text-white/60 mt-1">Sistem Informasi Manajemen Organisasi Keanggotaan</p>
        </div>

        <!-- Card -->
        <div class="glass rounded-2xl p-6 sm:p-8">
            @if($registrationComplete)
                <div class="text-center py-10">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/20 border border-emerald-400/30 mb-6">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Pendaftaran Berhasil!</h3>
                    <p class="text-white/60 mb-8 max-w-md mx-auto">Data pendaftaran Anda telah kami terima dan sedang dalam
                        proses verifikasi. Email konfirmasi telah dikirim ke <strong
                            class="text-white">{{ $email }}</strong>.</p>
                    <a href="/" class="btn-gradient inline-block rounded-xl px-8 py-3 text-sm">Kembali ke Beranda</a>
                </div>
            @else
                <form wire:submit.prevent="daftar">
                    {{-- Upload Dokumen --}}
                    <h3 class="text-sm font-bold text-white/90 uppercase tracking-widest mb-4">Upload Dokumen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Foto KTP -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white/80">Foto KTP</label>
                            <label for="fotoKtp"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-white/15 border-dashed rounded-2xl cursor-pointer bg-white/5 hover:bg-white/10 transition-colors overflow-hidden">
                                @if ($this->getKtpPreviewUrl())
                                    <img src="{{ $this->getKtpPreviewUrl() }}"
                                        class="w-full h-full object-contain p-2 rounded-2xl">
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-white/30" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4-4m0 0l4 4m-4-4v12M20 8l-4 4m0 0l-4-4m4 4V0" />
                                        </svg>
                                        <p class="text-sm text-white/50"><span class="font-semibold text-primary-300">Klik
                                                upload</span> atau drag & drop</p>
                                        <p class="text-xs text-white/30 mt-1">JPG, PNG (Maks. 5MB)</p>
                                    </div>
                                @endif
                                <input wire:model="fotoKtp" id="fotoKtp" type="file" class="hidden"
                                    accept="image/jpeg,image/png" />
                            </label>
                            <div wire:loading.flex wire:target="fotoKtp"
                                class="text-xs w-full text-blue-300 mt-1.5 items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>
                                    Mengunggah & Memindai KTP...
                                </span>
                            </div>
                            @error('fotoKtp') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Foto Wajah -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-white/80">Foto Wajah (Selfie)</label>
                            <label for="fotoWajah"
                                class="flex flex-col items-center justify-center w-full h-48 border-2 border-white/15 border-dashed rounded-2xl cursor-pointer bg-white/5 hover:bg-white/10 transition-colors overflow-hidden">
                                @if ($this->getWajahPreviewUrl())
                                    <img src="{{ $this->getWajahPreviewUrl() }}"
                                        class="w-full h-full object-cover p-2 rounded-2xl">
                                @else
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-white/30" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <p class="text-sm text-white/50"><span class="font-semibold text-primary-300">Klik
                                                upload</span> atau drag & drop</p>
                                        <p class="text-xs text-white/30 mt-1">JPG, PNG (Maks. 5MB)</p>
                                    </div>
                                @endif
                                <input wire:model="fotoWajah" id="fotoWajah" type="file" class="hidden"
                                    accept="image/jpeg,image/png" />
                            </label>
                            <div wire:loading.flex wire:target="fotoWajah"
                                class="text-xs text-blue-300 mt-1.5 items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mengunggah...
                            </div>
                            @error('fotoWajah') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Status Scan --}}
                    @if($scanMessage)
                        <div
                            class="p-4 mb-6 text-sm rounded-xl border {{ $scanSuccess ? 'text-emerald-200 bg-emerald-500/15 border-emerald-400/20' : 'text-amber-200 bg-amber-500/15 border-amber-400/20' }}">
                            {{ $scanMessage }}
                        </div>
                    @endif

                    {{-- Data Pribadi --}}
                    <h3 class="text-sm font-bold text-white/90 uppercase tracking-widest mb-4">Data Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="nik" class="block mb-1.5 text-sm font-medium text-white/70">NIK</label>
                            <input type="text" wire:model="nik" id="nik"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="16 Digit NIK">
                            @error('nik') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="namaLengkap" class="block mb-1.5 text-sm font-medium text-white/70">Nama
                                Lengkap</label>
                            <input type="text" wire:model="namaLengkap" id="namaLengkap"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Nama Sesuai KTP">
                            @error('namaLengkap') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tempatLahir" class="block mb-1.5 text-sm font-medium text-white/70">Tempat
                                Lahir</label>
                            <input type="text" wire:model="tempatLahir" id="tempatLahir"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm">
                            @error('tempatLahir') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggalLahir" class="block mb-1.5 text-sm font-medium text-white/70">Tanggal
                                Lahir</label>
                            <input type="date" wire:model="tanggalLahir" id="tanggalLahir"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm">
                            @error('tanggalLahir') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="jenisKelamin" class="block mb-1.5 text-sm font-medium text-white/70">Jenis
                                Kelamin</label>
                            <select wire:model="jenisKelamin" id="jenisKelamin"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm">
                                <option value="">Pilih</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenisKelamin') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="agama" class="block mb-1.5 text-sm font-medium text-white/70">Agama</label>
                            <select wire:model="agama" id="agama" class="input-glass w-full rounded-xl px-4 py-2.5 text-sm">
                                <option value="">Pilih</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                            @error('agama') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <h3 class="text-sm font-bold text-white/90 uppercase tracking-widest mb-4 mt-6">Alamat Tinggal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label for="alamat" class="block mb-1.5 text-sm font-medium text-white/70">Jalan/Dusun</label>
                            <textarea wire:model="alamat" id="alamat" rows="2"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm"></textarea>
                            @error('alamat') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="rtRw" class="block mb-1.5 text-sm font-medium text-white/70">RT / RW</label>
                            <input type="text" wire:model="rtRw" id="rtRw"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="001/002">
                            @error('rtRw') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label for="idpropinsi" class="block mb-1.5 text-sm font-medium text-white/70">Provinsi</label>
                            <select wire:model.live="idpropinsi" id="idpropinsi"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm">
                                <option value="">Pilih Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->propinsi }}</option>
                                @endforeach
                            </select>
                            @error('idpropinsi') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label for="idkabupaten" class="block mb-1.5 text-sm font-medium text-white/70">Kota /
                                Kabupaten</label>
                            <select wire:model.live="idkabupaten" id="idkabupaten"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" {{ empty($cities) ? 'disabled' : '' }}>
                                <option value="">Pilih Kota/Kab</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->kabupaten }}</option>
                                @endforeach
                            </select>
                            @error('idkabupaten') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label for="idkecamatan"
                                class="block mb-1.5 text-sm font-medium text-white/70">Kecamatan</label>
                            <select wire:model.live="idkecamatan" id="idkecamatan"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" {{ empty($districts) ? 'disabled' : '' }}>
                                <option value="">Pilih Kecamatan</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->kecamatan }}</option>
                                @endforeach
                            </select>
                            @error('idkecamatan') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="md:col-span-1">
                            <label for="idkelurahan" class="block mb-1.5 text-sm font-medium text-white/70">Kelurahan /
                                Desa</label>
                            <select wire:model.live="idkelurahan" id="idkelurahan"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" {{ empty($villages) ? 'disabled' : '' }}>
                                <option value="">Pilih Kelurahan/Desa</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}">{{ $village->kelurahan }}</option>
                                @endforeach
                            </select>
                            @error('idkelurahan') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Informasi Akun --}}
                    <h3 class="text-sm font-bold text-white/90 uppercase tracking-widest mb-4 mt-6">Informasi Akun</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="noTelepon" class="block mb-1.5 text-sm font-medium text-white/70">No.
                                Telepon/WA</label>
                            <input type="text" wire:model="noTelepon" id="noTelepon"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="08xxxxxxxxxx">
                            @error('noTelepon') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block mb-1.5 text-sm font-medium text-white/70">Email</label>
                            <input type="email" wire:model="email" id="email"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="email@contoh.com">
                            @error('email') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="password" class="block mb-1.5 text-sm font-medium text-white/70">Password</label>
                            <input type="password" wire:model="password" id="password"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="••••••••">
                            @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="passwordConfirmation"
                                class="block mb-1.5 text-sm font-medium text-white/70">Konfirmasi Password</label>
                            <input type="password" wire:model="passwordConfirmation" id="passwordConfirmation"
                                class="input-glass w-full rounded-xl px-4 py-2.5 text-sm" placeholder="••••••••">
                            @error('passwordConfirmation') <span
                            class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end mt-8">
                        <button type="submit" wire:loading.attr="disabled"
                            class="btn-gradient rounded-xl px-8 py-3 flex gap-1.5 text-sm cursor-pointer disabled:opacity-50">
                            <span wire:loading.remove wire:target="daftar">Daftar Sekarang</span>
                            <span wire:loading wire:target="daftar" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Mendaftarkan...
                            </span>
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <!-- Footer Link -->
        <div class="text-center mt-6">
            <p class="text-sm text-white/40">Sudah punya akun? <a href="{{ route('login') }}"
                    class="font-medium text-primary-300 hover:text-primary-200 transition-colors">Masuk di sini</a></p>
        </div>
    </div>
</div>