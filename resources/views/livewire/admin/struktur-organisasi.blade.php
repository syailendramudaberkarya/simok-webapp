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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Daftar Cabang {{ $selectedLevel }}</h3>
                        <p class="text-sm text-gray-500">Klik pada cabang untuk melihat detail pengurus dan informasi kantor.</p>
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
                        <div wire:click="viewDetail({{ $kantor->id }})" 
                            class="border border-gray-100 rounded-xl p-4 hover:border-primary-300 hover:shadow-lg cursor-pointer transition-all bg-white group relative overflow-hidden">
                            <div class="absolute inset-0 bg-primary-50/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            <div class="relative z-10">
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
                                        <span>{{ $kantor->email ?? '-' }}</span>
                                    </div>
                                </div>

                                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Klik Detail</span>
                                    <svg class="w-4 h-4 text-primary-400 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
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

    <!-- Detail Modal -->
    @if($isDetailModalOpen && $detailKantor)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeDetailModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-200 relative z-10">
                    
                    <!-- Header -->
                    <div class="bg-gray-50 px-6 py-5 border-b border-gray-100 flex justify-between items-center text-gray-900">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-primary-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">{{ $detailKantor->jenjang }}</span>
                                <span class="bg-{{ $detailKantor->status === 'Aktif' ? 'green' : 'red' }}-100 text-{{ $detailKantor->status === 'Aktif' ? 'green' : 'red' }}-700 text-[10px] font-bold px-2 py-0.5 rounded">{{ $detailKantor->status }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $detailKantor->nama_kantor }}</h3>
                        </div>
                        <button type="button" wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-xl p-2 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="bg-white p-6 h-[70vh] overflow-y-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left: Kantor Info -->
                            <div class="lg:col-span-1 space-y-6">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Informasi Lokasi</h4>
                                    <div class="space-y-4">
                                        <div class="flex gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" /></svg>
                                            </div>
                                            <p class="text-sm text-gray-600 leading-relaxed">
                                                {{ $detailKantor->alamat ?? '-' }}<br/>
                                                {{ $detailKantor->kelurahan }}, {{ $detailKantor->kecamatan }}<br/>
                                                {{ $detailKantor->kabupaten }}, {{ $detailKantor->provinsi }}<br/>
                                                <span class="text-gray-400 text-xs">Kode Pos: {{ $detailKantor->kode_pos ?? '-' }}</span>
                                            </p>
                                        </div>
                                        @if($detailKantor->latitude && $detailKantor->longitude)
                                        <div class="flex gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.106-1.789L9 2l5.447 2.724A2 2 0 0115 6.618v8.764a2 2 0 01-1.106 1.789L9 20z" /></svg>
                                            </div>
                                            <div class="text-sm font-mono text-gray-500">
                                                {{ $detailKantor->latitude }}, {{ $detailKantor->longitude }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Kontak Utama</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $detailKantor->telepon ?? '-' }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $detailKantor->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Pengurus List -->
                            <div class="lg:col-span-2">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Struktur Kepengurusan</h4>
                                <div class="bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-100/50 text-gray-500 text-[10px] uppercase font-bold border-b border-gray-100">
                                            <tr>
                                                <th class="px-4 py-3">Nama Pengurus</th>
                                                <th class="px-4 py-3">Jabatan</th>
                                                <th class="px-4 py-3">Kategori</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse($pengurusList as $p)
                                                <tr class="bg-white hover:bg-primary-50/30 transition-colors">
                                                    <td class="px-4 py-3">
                                                        <div class="font-bold text-gray-900">{{ $p->nama }}</div>
                                                        <div class="text-[10px] text-gray-400 font-mono">{{ $p->noanggota }}</div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="bg-primary-50 text-primary-700 text-[10px] font-bold px-2 py-0.5 rounded border border-primary-100">
                                                            {{ $p->jabatan }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="text-[11px] text-gray-600">{{ $p->kategorijabatan }}</div>
                                                        <div class="text-[9px] text-gray-400 italic">{{ $p->subkategorijabatan }}</div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">
                                                        Belum ada data pengurus yang tercatat untuk kantor ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                        <button type="button" wire:click="closeDetailModal" class="rounded-xl bg-white border border-gray-200 px-6 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all focus:outline-none">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
