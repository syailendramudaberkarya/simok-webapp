<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Card -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl overflow-hidden">
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
                @elseif($anggota->status === 'menunggu')
                    <span class="mt-2 sm:mt-0 text-xs font-bold px-3 py-1.5 rounded-full bg-amber-100 text-amber-700">⏳ Menunggu Verifikasi</span>
                @else
                    <span class="mt-2 sm:mt-0 text-xs font-bold px-3 py-1.5 rounded-full bg-red-100 text-red-700">✗ Pendaftaran Ditolak</span>
                @endif
            </div>

            @if($anggota->status === 'ditolak' && $anggota->alasan_tolak)
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h4 class="text-sm font-bold text-red-800">Alasan Penolakan:</h4>
                            <p class="text-sm text-red-700 mt-1 italic">"{{ $anggota->alasan_tolak }}"</p>
                            <p class="text-[11px] text-red-600 mt-2 font-medium">Silakan perbaiki data Anda melalui formulir di bawah ini dan ajukan ulang.</p>
                        </div>
                    </div>
                </div>
            @endif

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
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gender, Agama & Goldar</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->jenis_kelamin }} · {{ $anggota->agama }} · {{ $anggota->golongan_darah ?? '-' }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status & Warganegara</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->status_perkawinan }} · {{ $anggota->kewarganegaraan }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pekerjaan</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->pekerjaan }}</span>
                </div>
                <div class="bg-gray-50/80 rounded-xl p-3 col-span-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Alamat</span>
                    <span class="block text-gray-800 mt-0.5">{{ $anggota->alamat }}, RT/RW {{ $anggota->rt_rw }}, {{ $anggota->kelurahan }}, {{ $anggota->kecamatan }}</span>
                </div>
            </div>

            @if($anggota->status !== 'ditolak')
                <div class="mt-4 p-3 rounded-xl bg-amber-50/50 border border-amber-100 text-xs text-amber-700">
                    <span class="font-bold">Catatan:</span> Data KTP (Nama, NIK, TTL) tidak dapat diubah oleh anggota. Hubungi administrator jika ada kesalahan data.
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Form / Resubmit Form -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6">
        @if($anggota->status === 'ditolak')
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-5">Perbaiki Data & Ajukan Ulang</h3>
            
            <form wire:submit.prevent="ajukanUlang" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Username</label>
                        <input type="text" wire:model="username" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Email Utama (Digunakan untuk Login)</label>
                        <input type="email" wire:model="email" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">NIK</label>
                        <input type="text" wire:model="nik" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('nik') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" wire:model="tempat_lahir" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('tempat_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" wire:model="tanggal_lahir" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('tanggal_lahir') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select wire:model="jenis_kelamin" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih Gender</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                        <select wire:model="agama" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih Agama</option>
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
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Tingkatan Organisasi</label>
                        <select wire:model="tingkatan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih Tingkatan</option>
                            <option value="DPN">DPN (Nasional)</option>
                            <option value="DPD">DPD (Daerah)</option>
                            <option value="PR">PR (Ranting)</option>
                            <option value="PAR">PAR (Anak Ranting)</option>
                        </select>
                        @error('tingkatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Status Perkawinan</label>
                        <select wire:model="status_perkawinan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">Pilih Status</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Cerai Hidup">Cerai Hidup</option>
                            <option value="Cerai Mati">Cerai Mati</option>
                        </select>
                        @error('status_perkawinan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Golongan Darah</label>
                        <select wire:model="golongan_darah" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                            <option value="">-</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                        @error('golongan_darah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Pekerjaan</label>
                        <input type="text" wire:model="pekerjaan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: KARYAWAN SWASTA">
                        @error('pekerjaan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kewarganegaraan</label>
                        <input type="text" wire:model="kewarganegaraan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="WNI">
                        @error('kewarganegaraan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-full">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Alamat Lengkap (Jalan/Gang, Blok, No. Rumah)</label>
                        <textarea wire:model="alamat" rows="2" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: Jl. Mawar No. 123, RT 01 RW 02"></textarea>
                        @error('alamat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Provinsi</label>
                        <input type="text" wire:model.live="propinsi" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: Jawa Timur">
                        @error('propinsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kabupaten/Kota</label>
                        <input type="text" wire:model.live="kabupaten" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: KAB. SAMPANG">
                        @error('kabupaten') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kecamatan</label>
                        <input type="text" wire:model.live="kecamatan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: SAMPANG">
                        @error('kecamatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-700">Kelurahan/Desa</label>
                        <input type="text" wire:model.live="kelurahan" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm" placeholder="Contoh: Dalpenang">
                        @error('kelurahan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Perbarui Foto KTP (Jika perlu)</label>
                        <div class="flex gap-2">
                             <input wire:model="foto_ktp_baru" type="file" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                             
                             @if($foto_ktp_baru)
                                <button type="button" wire:click="scanKtp" wire:loading.attr="disabled" class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-100">
                                    <span wire:loading.remove wire:target="scanKtp">🔍 Scan KTP</span>
                                    <span wire:loading wire:target="scanKtp">...</span>
                                </button>
                             @endif
                        </div>
                        @error('foto_ktp_baru') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        @if($scanError) <span class="text-red-500 text-xs mt-1 block font-bold">⚠️ {{ $scanError }}</span> @endif
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Perbarui Foto Wajah (Jika perlu)</label>
                        <input wire:model="foto_wajah_baru" type="file" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        @error('foto_wajah_baru') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="bg-gradient-to-br from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-white font-semibold shadow-lg shadow-amber-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-8 py-3 text-sm cursor-pointer">
                        <span wire:loading.remove wire:target="ajukanUlang">Ajukan Ulang Pendaftaran</span>
                        <span wire:loading wire:target="ajukanUlang">Memproses...</span>
                    </button>
                </div>
            </form>
        @else
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest mb-5">Ubah Data Kontak & Alamat</h3>
            
            <form wire:submit.prevent="updateProfil" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block mb-1.5 text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model="email" id="email" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="no_telepon" class="block mb-1.5 text-sm font-medium text-gray-700">No. Telepon/WA</label>
                        <input type="text" wire:model="no_telepon" id="no_telepon" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                        @error('no_telepon') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block mb-1.5 text-sm font-medium text-gray-700">Alamat Lengkap</label>
                    <textarea wire:model="alamat" id="alamat" rows="2" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm"></textarea>
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
                    <button type="submit" wire:loading.attr="disabled" class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 rounded-xl px-6 py-2.5 text-sm cursor-pointer">
                        <span wire:loading.remove wire:target="updateProfil">Simpan Perubahan</span>
                        <span wire:loading wire:target="updateProfil">Menyimpan...</span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
