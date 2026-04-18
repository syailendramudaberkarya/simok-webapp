<x-layouts.guest>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg shadow-primary-500/30 mb-4">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Verifikasi Keanggotaan</h1>
                <p class="text-sm text-white/60 mt-1">Sistem Informasi Manajemen Organisasi Keanggotaan</p>
            </div>

            <div class="bg-white/10 backdrop-blur-2xl border border-white/10 shadow-xl rounded-2xl overflow-hidden">
                @if($anggota)
                    @if($anggota->status === 'disetujui')
                        <div class="bg-gradient-to-r from-emerald-500/30 to-emerald-600/30 p-6 text-center border-b border-emerald-400/20">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border border-emerald-400/30 mb-3">
                                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-emerald-200">Anggota Terverifikasi</h3>
                        </div>
                    @else
                        <div class="bg-gradient-to-r from-amber-500/30 to-amber-600/30 p-6 text-center border-b border-amber-400/20">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/20 border border-amber-400/30 mb-3">
                                <svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-amber-200">Status Tidak Aktif</h3>
                        </div>
                    @endif
                    
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span class="text-sm text-white/50">Nomor Anggota</span>
                            <span class="text-lg font-bold font-mono text-white">{{ $anggota->nomor_anggota }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span class="text-sm text-white/50">Nama Lengkap</span>
                            <span class="text-sm font-semibold text-white">{{ $anggota->nama_lengkap }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/10 pb-3">
                            <span class="text-sm text-white/50">Status</span>
                            <span class="text-sm font-semibold text-white uppercase">{{ $anggota->status }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-white/50">Tanggal Bergabung</span>
                            <span class="text-sm font-semibold text-white">{{ $anggota->approved_at ? $anggota->approved_at->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-red-500/30 to-red-600/30 p-6 text-center border-b border-red-400/20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-500/20 border border-red-400/30 mb-3">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-red-200">Data Tidak Ditemukan</h3>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-sm text-white/60">Nomor Anggota <strong class="text-white">{{ $nomor }}</strong> tidak terdaftar dalam sistem.</p>
                    </div>
                @endif
            </div>

            <div class="text-center mt-8 text-xs text-white/30">
                &copy; {{ date('Y') }} SiMOK — Sistem Informasi Manajemen Organisasi Keanggotaan
            </div>
        </div>
    </div>
</x-layouts.guest>
