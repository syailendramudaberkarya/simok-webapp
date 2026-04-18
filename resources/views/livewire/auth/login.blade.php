<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">

            <h1 class="text-2xl font-bold text-white">Masuk ke SiMOK</h1>
            <p class="text-sm text-white/60 mt-1">Sistem Informasi Manajemen Organisasi Keanggotaan</p>
        </div>

        <!-- Glass Card -->
        <div class="bg-white/10 backdrop-blur-2xl border border-white/10 shadow-xl rounded-2xl p-8">
            @if (session('status'))
                <div class="p-3 mb-6 text-sm text-emerald-200 rounded-xl bg-emerald-500/20 border border-emerald-400/30">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit.prevent="authenticate" class="space-y-5">
                <div>
                    <label for="login" class="block mb-2 text-sm font-medium text-white/80">Email / Username</label>
                    <input type="text" wire:model="login" id="login"
                        class="bg-white/5 border border-white/15 text-white placeholder-white/40 focus:bg-white/10 focus:border-primary-400 focus:ring-4 focus:ring-primary-400/25 outline-none transition-all w-full rounded-xl px-4 py-3 text-sm"
                        placeholder="Email atau username" required>
                    @error('login') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-white/80">Password</label>
                    <input type="password" wire:model="password" id="password" placeholder="••••••••"
                        class="bg-white/5 border border-white/15 text-white placeholder-white/40 focus:bg-white/10 focus:border-primary-400 focus:ring-4 focus:ring-primary-400/25 outline-none transition-all w-full rounded-xl px-4 py-3 text-sm"
                        required>
                    @error('password') <span class="text-sm text-red-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <!-- <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="remember" id="remember" type="checkbox"
                            class="w-4 h-4 rounded border-white/20 bg-white/10 text-primary-500 focus:ring-primary-400/30">
                        <span class="text-sm text-white/60">Ingat saya</span>
                    </label> -->
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-medium text-primary-300 hover:text-primary-200 transition-colors">Lupa
                        password?</a>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="bg-gradient-to-br from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white font-semibold shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0 w-full rounded-xl px-5 py-3 text-sm cursor-pointer">
                    <span wire:loading.remove wire:target="authenticate">Masuk Dashboard</span>
                    <span wire:loading wire:target="authenticate">Memproses...</span>
                </button>

                <p class="text-sm text-white/50 text-center">
                    Belum punya akun? <a href="{{ route('pendaftaran') }}"
                        class="font-medium text-primary-300 hover:text-primary-200 transition-colors">Daftar
                        sekarang</a>
                </p>
            </form>
        </div>
    </div>
</div>