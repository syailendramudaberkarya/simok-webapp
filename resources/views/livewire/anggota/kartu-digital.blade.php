<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kartu Anggota Digital</h2>
        <p class="text-sm text-gray-500">Lihat dan unduh kartu keanggotaan digital Anda.</p>
    </div>

    @if(!$kartu)
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Kartu Sedang Diproses</h3>
            <p class="text-sm text-gray-500">Kartu anggota digital Anda sedang dalam proses pembuatan. Silakan cek kembali
                nanti.</p>
        </div>
    @else
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6 sm:p-8">
            <div class="flex flex-col items-center py-6">
                <!-- Card Preview (Landscape) -->
                <div class="relative w-full max-w-[480px] aspect-[1.58/1] rounded-2xl shadow-2xl overflow-hidden text-white"
                    style="background: {{ $kartu->template->warna_utama ?? '#1e40af' }};">

                    <!-- Decorative patterns -->
                    <div class="absolute inset-0 opacity-[0.05]"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                    </div>
                    <div
                        class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white/10 -translate-y-1/2 translate-x-1/4 blur-3xl">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-32 h-32 rounded-full bg-black/10 translate-y-1/2 -translate-x-1/4 blur-2xl">
                    </div>

                    <!-- Header -->
                    <div class="relative z-10 p-5 flex justify-between items-start bg-black/10">
                        <div>
                            <h3 class="font-bold text-lg tracking-widest uppercase">SiMOK</h3>
                            <p class="text-[8px] uppercase tracking-[0.3em] opacity-80">Sistem Manajemen Keanggotaan</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 px-6 py-4 flex gap-6">
                        <!-- Photo -->
                        <div
                            class="w-24 h-32 bg-white/20 backdrop-blur-md border-2 border-white/50 rounded-lg overflow-hidden shadow-xl flex-shrink-0">
                            <img src="{{ route('file.private', ['path' => $anggota->foto_wajah_path]) }}" alt="Foto"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Data -->
                        <div class="flex-1 space-y-4 pt-1">
                            <div>
                                <p class="text-[8px] uppercase tracking-wider opacity-60 mb-0.5">Nama Lengkap</p>
                                <h4 class="font-bold text-xl leading-tight uppercase">{{ $anggota->nama_lengkap }}</h4>
                            </div>
                            <div>
                                <p class="text-[8px] uppercase tracking-wider opacity-60 mb-0.5">Nomor Anggota</p>
                                <p
                                    class="font-mono text-2xl font-bold tracking-widest bg-black/20 inline-block px-3 py-1 rounded-md">
                                    {{ $anggota->nomor_anggota }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="absolute bottom-0 left-0 right-0 z-10 px-6 py-4 flex justify-between items-end bg-transparent">
                        <div class="flex gap-6">
                            <div>
                                <p class="text-[7px] uppercase tracking-wider opacity-60 mb-0.5">Berlaku Sejak</p>
                                <p class="text-[10px] font-bold">
                                    {{ $anggota->approved_at ? $anggota->approved_at->translatedFormat('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[7px] uppercase tracking-wider opacity-60 mb-0.5">Berlaku Hingga</p>
                                <p class="text-[10px] font-bold">
                                    {{ $anggota->expired_at ? $anggota->expired_at->translatedFormat('d/m/Y') : '-' }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-1 rounded shadow-lg">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->margin(0)->generate($verifyUrl) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Download Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('anggota.kartu.pdf') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-red-500 to-red-600 shadow-lg shadow-red-500/25 hover:shadow-red-500/40 transition-shadow">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z"
                            clip-rule="evenodd" />
                    </svg>
                    Unduh
                </a>

            </div>
        </div>
    @endif
</div>