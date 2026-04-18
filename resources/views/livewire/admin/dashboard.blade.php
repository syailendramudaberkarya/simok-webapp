<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Statistik</h2>
        <p class="text-gray-500 text-sm">Ringkasan pendaftaran dan status keanggotaan.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 18"><path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $totalAnggota }}</div>
            <div class="text-xs text-gray-500 font-medium">Total Anggota</div>
        </div>
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $menungguPersetujuan }}</div>
            <div class="text-xs text-gray-500 font-medium">Menunggu</div>
        </div>
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $disetujui }}</div>
            <div class="text-xs text-gray-500 font-medium">Disetujui</div>
        </div>
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center shadow-lg shadow-red-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $ditolak }}</div>
            <div class="text-xs text-gray-500 font-medium">Ditolak</div>
        </div>
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-400 to-violet-600 flex items-center justify-center shadow-lg shadow-violet-500/25">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-800">{{ $baruBulanIni }}</div>
            <div class="text-xs text-gray-500 font-medium">Baru Bulan Ini</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6">
            <h3 class="text-base font-bold text-gray-800 mb-4">Pertumbuhan 6 Bulan Terakhir</h3>

            <div class="h-64 w-full" wire:ignore 
                x-data="{
                    labels: @js($chartDataLabels),
                    values: @js($chartDataValues),
                    init() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
                        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)');
                        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
                        
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: this.labels,
                                datasets: [{
                                    label: 'Anggota Baru',
                                    data: this.values,
                                    borderColor: '#6366f1',
                                    backgroundColor: gradient,
                                    borderWidth: 2.5,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#6366f1',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                }">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        <!-- Recent List -->
        <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-800">Pendaftaran Terbaru</h3>
                <a href="{{ route('admin.manajemen') }}" class="text-xs font-semibold text-primary-600 hover:underline">Lihat Semua →</a>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse($pendaftaranTerbaru as $anggota)
                    <li class="py-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-300 to-primary-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                            {{ mb_substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $anggota->nama_lengkap }}</p>
                            <p class="text-xs text-gray-400">{{ $anggota->created_at->diffForHumans() }}</p>
                        </div>
                        @if($anggota->status === 'disetujui')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Disetujui</span>
                        @elseif($anggota->status === 'menunggu')
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Menunggu</span>
                        @else
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700">Ditolak</span>
                        @endif
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-gray-400">Belum ada pendaftaran.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
