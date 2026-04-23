<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Aktivitas</h2>
            <p class="text-gray-500 text-sm">Catatan log aktivitas administrator di sistem.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live="search"
                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl pl-10 pr-4 py-2 text-sm"
                    placeholder="Cari deskripsi atau nama admin...">
            </div>
            <div>
                <select wire:model.live="actionFilter"
                    class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2 text-sm text-gray-700">
                    <option value="">Semua Aktivitas</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}">{{ str_replace('_', ' ', ucwords($action, '_')) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-black/5">
                    <tr>
                        <th class="px-6 py-4 font-bold">Waktu</th>
                        <th class="px-6 py-4 font-bold">Admin</th>
                        <th class="px-6 py-4 font-bold">Aktivitas</th>
                        <th class="px-6 py-4 font-bold">Keterangan</th>
                        <th class="px-6 py-4 font-bold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/40 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-800">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $log->user->name ?? 'System' }}</div>
                                <div class="text-[10px] text-primary-600 font-bold uppercase tracking-tighter">
                                    {{ $log->user->tingkatan ?? '' }} 
                                    @if($log->user?->kantor)
                                        - {{ $log->user->kantor->nama_kantor }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-lg bg-gray-100 text-gray-700 text-[10px] font-bold uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="line-clamp-2 max-w-xs text-xs text-gray-700">{{ $log->description }}</p>
                            </td>
                            <td class="px-6 py-4 text-[10px] font-mono text-gray-400">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Belum ada catatan aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 bg-gray-50/50 border-t border-black/5">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
