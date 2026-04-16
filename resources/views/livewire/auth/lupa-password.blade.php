<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg shadow-primary-500/30 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Lupa Password</h1>
            <p class="text-sm text-white/60 mt-1">Masukkan email Anda untuk menerima link reset.</p>
        </div>

        <div class="glass rounded-2xl p-8">
            @if (session('status'))
                <div class="p-3 mb-6 text-sm text-emerald-200 rounded-xl bg-emerald-500/20 border border-emerald-400/30">
                    {{ session('status') }}
                </div>
            @endif

            @if(!$emailSent)
                <form wire:submit.prevent="sendResetLink" class="space-y-5">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-white/80">Email</label>
                        <input type="email" wire:model="email" id="email" class="input-glass w-full rounded-xl px-4 py-3 text-sm" placeholder="nama@email.com" required>
                        @error('email') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="btn-gradient w-full rounded-xl px-5 py-3 text-sm cursor-pointer">
                        <span wire:loading.remove wire:target="sendResetLink">Kirim Link Reset</span>
                        <span wire:loading wire:target="sendResetLink">Mengirim...</span>
                    </button>
                </form>
            @else
                <div class="text-center py-4">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border border-emerald-400/30 mb-4">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-white/80">Email reset password telah dikirim. Silakan periksa kotak masuk Anda.</p>
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-primary-300 hover:text-primary-200 transition-colors">← Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>
