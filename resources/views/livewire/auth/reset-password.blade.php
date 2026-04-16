<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg shadow-primary-500/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z" clip-rule="evenodd"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-sm text-white/60 mt-1">Masukkan password baru Anda.</p>
        </div>

        <div class="glass rounded-2xl p-8">
            <form wire:submit.prevent="resetPassword" class="space-y-5">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-white/80">Email</label>
                    <input type="email" wire:model="email" id="email" class="input-glass w-full rounded-xl px-4 py-3 text-sm" required>
                    @error('email') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-white/80">Password Baru</label>
                    <input type="password" wire:model="password" id="password" class="input-glass w-full rounded-xl px-4 py-3 text-sm" required>
                    <p class="text-xs text-white/40 mt-1">Min 8 karakter, 1 huruf kapital, 1 angka.</p>
                    @error('password') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-white/80">Konfirmasi Password</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation" class="input-glass w-full rounded-xl px-4 py-3 text-sm" required>
                </div>

                <button type="submit" wire:loading.attr="disabled" class="btn-gradient w-full rounded-xl px-5 py-3 text-sm cursor-pointer">
                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                    <span wire:loading wire:target="resetPassword">Memproses...</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-primary-300 hover:text-primary-200 transition-colors">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
