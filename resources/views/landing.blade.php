<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Manajemen Organisasi Keanggotaan (SiMOK)</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:200,300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body
    class="font-sans antialiased text-gray-800 bg-[#f8fafc] selection:bg-primary-500 selection:text-white relative min-h-screen">

    <!-- Abstract Ambient Backgrounds -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <!-- Top Left Blob -->
        <div
            class="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] rounded-full bg-gradient-to-br from-primary-200/60 to-purple-200/60 mix-blend-multiply filter blur-[100px] opacity-70 animate-blob">
        </div>
        <!-- Top Right Blob -->
        <div
            class="absolute top-[10%] -right-[10%] w-[40%] h-[60%] rounded-full bg-gradient-to-bl from-blue-200/60 to-cyan-200/60 mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-2000">
        </div>
        <!-- Bottom Left Blob -->
        <div
            class="absolute -bottom-[20%] -left-[10%] w-[60%] h-[60%] rounded-full bg-gradient-to-tr from-pink-200/60 to-orange-100/60 mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-4000">
        </div>
    </div>

    <!-- Minimal Header -->
    <header class="fixed top-0 z-50 w-full glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3 group cursor-default">
                    <div
                        class="w-10 h-10 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-base shadow-[0_8px_16px_rgba(var(--color-primary-500),0.3)] transition-transform group-hover:scale-105">
                        S
                    </div>
                    <span
                        class="text-2xl font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">SiMOK</span>
                </div>

                <!-- Navigation Actions -->
                <div class="flex items-center gap-3 sm:gap-6">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Dashboard
                                Admin</a>
                        @else
                            <a href="{{ route('anggota.profil') }}"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Profil
                                Saya</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors hidden sm:block">Masuk</a>
                        <a href="{{ route('pendaftaran') }}"
                            class="relative inline-flex items-center justify-center h-11 px-6 sm:px-8 text-sm font-semibold text-white transition-all bg-gray-900 rounded-full cursor-pointer hover:bg-gray-800 hover:-translate-y-0.5 hover:shadow-[0_8px_20px_rgba(0,0,0,0.15)] active:translate-y-0 overflow-hidden group">
                            <span
                                class="absolute inset-0 w-full h-full -ml-16 bg-white/[0.15] rounded-full filter blur-md transform translate-x-full group-hover:translate-x-0 transition-transform duration-500 ease-out"></span>
                            <span>Daftar Sekarang</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Hero Content -->
    <main
        class="relative pt-40 pb-24 lg:pt-52 lg:pb-32 flex flex-col items-center justify-center min-h-screen px-4 text-center">
        <!-- Pill Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 mb-8 rounded-full glass-card border border-white/80">
            <span class="flex w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-xs font-semibold tracking-wider text-gray-600 uppercase">Portal Pendaftaran Resmi</span>
        </div>

        <!-- Headline -->
        <h1
            class="max-w-4xl mx-auto text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-gray-900 mb-8 leading-[1.1]">
            Kelola Organisasi <br />
            <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 via-purple-600 to-blue-600">Tanpa
                Batas</span>
        </h1>

        <!-- Subheadline -->
        <p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-500 mb-12 font-light leading-relaxed">
            Platform modern untuk manajemen anggota. Nikmati pengalaman pendaftaran instan dengan <strong
                class="font-medium text-gray-800">Teknologi OCR</strong>, validasi hierarki canggih, dan Kartu Tanda
            Anggota Digital.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
            <a href="{{ route('pendaftaran') }}"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-primary-600 to-blue-600 hover:from-primary-500 hover:to-blue-500 text-white font-semibold px-8 py-4 rounded-2xl shadow-[0_12px_24px_rgba(var(--color-primary-500),0.25)] transition-all hover:-translate-y-1 hover:shadow-[0_16px_32px_rgba(var(--color-primary-500),0.35)] active:translate-y-0 text-base">
                Mulai Pendaftaran
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
            <a href="{{ route('login') }}"
                class="w-full sm:w-auto flex items-center justify-center gap-2 glass-card hover:bg-white/80 text-gray-800 font-semibold px-8 py-4 rounded-2xl transition-all hover:-translate-y-1 text-base">
                Masuk Akun
            </a>
        </div>

        <!-- Fast Feature Cards (Glassmorphism) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mt-28 text-left w-full">
            <div
                class="glass-card p-8 rounded-3xl transition-transform hover:-translate-y-1 hover:bg-white/80 content-center relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-primary-100/50 rounded-full blur-xl group-hover:bg-primary-200/50 transition-colors">
                </div>
                <div
                    class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-6 border border-gray-100 relative z-10">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3 relative z-10">Instan & Cerdas</h3>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10">Unggah KTP dan biarkan teknologi
                    kecerdasan buatan (AI) kami yang membaca dan mengisi formulir Anda dalam hitungan detik.</p>
            </div>

            <div
                class="glass-card p-8 rounded-3xl transition-transform hover:-translate-y-1 hover:bg-white/80 content-center relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-purple-100/50 rounded-full blur-xl group-hover:bg-purple-200/50 transition-colors">
                </div>
                <div
                    class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-6 border border-gray-100 relative z-10">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3 relative z-10">Terstruktur & Aman</h3>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10">Setiap keanggotaan dikelompokkan secara
                    spesifik sesuai tingkat wilayah otoritas. Data terenkripsi dengan aman di awan.</p>
            </div>

            <div
                class="glass-card p-8 rounded-3xl transition-transform hover:-translate-y-1 hover:bg-white/80 content-center relative overflow-hidden group">
                <div
                    class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-100/50 rounded-full blur-xl group-hover:bg-emerald-200/50 transition-colors">
                </div>
                <div
                    class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-6 border border-gray-100 relative z-10">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-3 relative z-10">Kartu Anggota Digital</h3>
                <p class="text-sm text-gray-500 leading-relaxed relative z-10">Setiap anggota yang disetujui otomatis
                    mendapatkan KTA elegan berbasis PDF maupun PNG yang interaktif dan dapat diunduh.</p>
            </div>
        </div>
    </main>

    <!-- Minimal Footer -->
    <footer class="py-8 mt-auto border-t border-gray-200/50 glass-nav">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-xs sm:text-sm text-gray-500 font-medium">
                &copy; {{ date('Y') }} SiMOK — Sistem Informasi Manajemen Organisasi Keanggotaan.
            </p>
        </div>
    </footer>

</body>

</html>