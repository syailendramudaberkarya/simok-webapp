<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">

            <div class="inline-block px-3 py-1 rounded-full bg-red-500/20 border border-red-400/30 mb-3">
                <span class="text-red-300 text-xs font-semibold uppercase tracking-widest">Administrator</span>
            </div>
            <h1 class="text-2xl font-bold text-white">Portal Admin SiMOK</h1>
            <p class="text-sm text-white/60 mt-1">Masuk untuk mengelola sistem keanggotaan.</p>
        </div>

        <div class="glass rounded-2xl p-8">
            <form wire:submit.prevent="login" class="space-y-5">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-white/80">Email</label>
                    <input type="email" wire:model.defer="email" id="email"
                        class="input-glass w-full rounded-xl px-4 py-3 text-sm" placeholder="admin@simok.id" required>
                    @error('email') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-white/80">Password</label>
                    <input type="password" wire:model.defer="password" id="password" placeholder="••••••••"
                        class="input-glass w-full rounded-xl px-4 py-3 text-sm" required>
                    @error('password') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="remember" id="remember" type="checkbox"
                        class="w-4 h-4 rounded border-white/20 bg-white/10 text-red-500 focus:ring-red-400/30">
                    <span class="text-sm text-white/60">Ingat saya</span>
                </label>

                <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-xl px-5 py-3 text-sm font-semibold text-white cursor-pointer transition-all duration-300 hover:-translate-y-0.5"
                    style="background: linear-gradient(135deg, #dc2626, #991b1b); box-shadow: 0 4px 15px rgba(220,38,38,0.4);">
                    <span wire:loading.remove wire:target="login">Login Administrasi</span>
                    <span wire:loading wire:target="login">Otentikasi...</span>
                </button>
            </form>
        </div>
    </div>
</div>