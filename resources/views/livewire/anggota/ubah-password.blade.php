<div class="max-w-lg mx-auto">
    <div class="bg-white/70 backdrop-blur-xl border border-black/5 shadow-md rounded-2xl p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ubah Password</h2>
                <p class="text-xs text-gray-500">Perbarui kata sandi akun Anda.</p>
            </div>
        </div>
        
        @if (session()->has('message'))
            <div class="p-3 mb-5 text-sm text-emerald-700 rounded-xl bg-emerald-50 border border-emerald-200">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-5">
            <div>
                <label for="current_password" class="block mb-1.5 text-sm font-medium text-gray-700">Password Saat Ini</label>
                <input type="password" wire:model="current_password" id="current_password" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                @error('current_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block mb-1.5 text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" wire:model="password" id="password" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
                <span class="text-xs text-gray-400 mt-1 block">Min 8 karakter, 1 huruf kapital, 1 angka.</span>
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block mb-1.5 text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" wire:model="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-black/10 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/15 outline-none transition-all w-full rounded-xl px-4 py-2.5 text-sm">
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" wire:loading.attr="disabled" class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 w-full rounded-xl px-5 py-2.5 text-sm cursor-pointer">
                    <span wire:loading.remove wire:target="updatePassword">Ubah Password</span>
                    <span wire:loading wire:target="updatePassword">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>
