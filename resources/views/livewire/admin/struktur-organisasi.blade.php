<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Struktur Organisasi</h2>
            <p class="text-sm text-gray-500">Pantau dan kelola jaringan cabang di bawah wilayah kewenangan Anda.</p>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $colors = [
                'DPN' => 'from-slate-500 to-slate-700 shadow-slate-500/30 text-white',
                'DPD' => 'from-indigo-500 to-indigo-700 shadow-indigo-500/30 text-white',
                'DPC' => 'from-blue-500 to-blue-700 shadow-blue-500/30 text-white',
                'PR' => 'from-purple-500 to-purple-700 shadow-purple-500/30 text-white',
                'PAR' => 'from-red-500 to-red-700 shadow-red-500/30 text-white',
            ];
            $labels = [
                'DPN' => 'Dewan Pimpinan Nasional',
                'DPD' => 'Dewan Pimpinan Daerah',
                'DPC' => 'Dewan Pimpinan Cabang',
                'PR' => 'Pimpinan Ranting',
                'PAR' => 'Pimpinan Anak Ranting',
            ];
        @endphp

        @foreach($levels as $lvl)
            <div wire:click="selectLevel('{{ $lvl }}')"
                class="relative overflow-hidden cursor-pointer rounded-2xl bg-gradient-to-br {{ $colors[$lvl] ?? 'from-gray-500 to-gray-700' }} shadow-xl p-6 transition-all duration-300 {{ $selectedLevel === $lvl ? 'ring-4 ring-white/50 scale-[1.02]' : 'hover:-translate-y-1 hover:shadow-2xl' }}">
                
                <!-- Background decoration -->
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-4 -bottom-4 w-16 h-16 bg-black/10 rounded-full blur-xl"></div>
                
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-black tracking-tight">{{ $lvl }}</h3>
                        <p class="text-white/80 text-xs font-medium mt-1 uppercase tracking-wider">{{ $labels[$lvl] ?? 'Cabang' }}</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md rounded-xl p-2">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                
                <div class="relative z-10 mt-6 flex items-end justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ number_format($stats[$lvl]) }}</div>
                        <div class="text-white/70 text-xs">Total Kantor Aktif</div>
                    </div>
                    <div class="text-white/80 transition-transform duration-300 {{ $selectedLevel === $lvl ? 'translate-y-1' : '' }}">
                        <svg class="w-5 h-5 {{ $selectedLevel === $lvl ? 'rotate-180' : '' }} transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Active Level Detail View -->
    @if($selectedLevel && $listKantor)
        <div class="mt-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Cabang {{ $selectedLevel }}</h3>
                        <p class="text-sm text-gray-500">Menampilkan detail data untuk {{ $labels[$selectedLevel] ?? 'Cabang' }}</p>
                    </div>
                    <div class="w-full sm:w-64 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl pl-10 pr-4 py-2.5 text-sm"
                            placeholder="Cari nama cabang...">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse($listKantor as $kantor)
                        <div class="border border-gray-100 rounded-xl p-4 hover:border-primary-200 hover:shadow-md transition-all bg-white group">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-bold text-gray-800 group-hover:text-primary-600 transition-colors">{{ $kantor->nama_kantor }}</h4>
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">
                                    {{ $kantor->status }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 text-xs text-gray-600">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <span class="line-clamp-2">
                                        {{ $kantor->alamat ?? '-' }} 
                                        {{ implode(', ', array_filter([$kantor->kelurahan, $kantor->kecamatan, $kantor->kabupaten, $kantor->provinsi])) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    <span>{{ $kantor->email ?? 'Tidak ada email' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    <span>{{ $kantor->telepon ?? 'Tidak ada No. Telp' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center border-2 border-dashed border-gray-200 rounded-2xl">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <p class="text-gray-500 text-sm">Tidak ada data cabang {{ $selectedLevel }} yang ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $listKantor->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
