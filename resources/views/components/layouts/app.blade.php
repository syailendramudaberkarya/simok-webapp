<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SiMOK</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased"
    style="background: linear-gradient(135deg, #f0f4ff 0%, #e8eeff 50%, #f5f0ff 100%);">

    <!-- Topbar -->
    <nav class="fixed top-0 z-50 w-full bg-white/70 backdrop-blur-xl border-b border-white/40 shadow-sm">
        <div class="px-4 py-3 lg:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-gray-500 rounded-lg sm:hidden hover:bg-white/50 focus:outline-none focus:ring-2 focus:ring-primary-300">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="#" class="flex items-center gap-2">
                        <div
                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-primary-500/30">
                            S</div>
                        <span
                            class="text-xl font-bold bg-gradient-to-r from-primary-700 to-primary-500 bg-clip-text text-transparent hidden sm:block">SiMOK</span>
                    </a>
                </div>
                <!-- Right side -->
                <div class="flex items-center gap-3">
                    @if(auth()->check() && auth()->user()->isAnggota())
                        @if(auth()->user()->anggota?->status === 'disetujui')
                            <span
                                class="bg-emerald-100/80 text-emerald-700 text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">✓
                                Disetujui</span>
                        @elseif(auth()->user()->anggota?->status === 'menunggu')
                            <span
                                class="bg-amber-100/80 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">⏳
                                Menunggu</span>
                        @else
                            <span
                                class="bg-red-100/80 text-red-700 text-xs font-semibold px-3 py-1 rounded-full backdrop-blur-sm">✗
                                Ditolak</span>
                        @endif
                    @endif
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/50 transition-colors">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="hidden md:flex md:flex-col md:items-start md:justify-center">
                                <span
                                    class="text-sm font-medium text-gray-700 leading-none">{{ auth()->user()->name ?? '' }}</span>
                                @if(auth()->user()->isAdmin())
                                    <span
                                        class="text-[9px] font-bold text-primary-700 bg-primary-100 px-1.5 py-0.5 rounded-md mt-1 tracking-wider uppercase border border-primary-200">
                                        Admin {{ auth()->user()->tingkatan }}
                                        @if(auth()->user()->tingkatan !== 'DPN' && auth()->user()->kantor)
                                            - {{ auth()->user()->kantor->nama_kantor }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white/80 backdrop-blur-xl border border-white/20 rounded-xl shadow-xl py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">Keluar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-12 transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar">
        <div class="h-full px-4 pb-4 overflow-y-auto bg-white/70 backdrop-blur-xl border-r border-white/40">
            <ul class="space-y-1 font-medium pt-4">
                @if(auth()->check() && auth()->user()->isAnggota())
                    <li class="pb-2"><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3">Akun
                            Anggota</span></li>
                    <li>
                        <a href="{{ route('anggota.profil') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('anggota.profil') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                            </svg>
                            Profil
                        </a>
                    </li>
                    @if(auth()->user()->anggota?->status === 'disetujui')
                        <li>
                            <a href="{{ route('anggota.kartu') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('anggota.kartu') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 16">
                                    <path
                                        d="M18 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM6.5 3a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3.014 13.021a.05.05 0 0 1-.053-.05c0-1.423 1.146-2.576 2.558-2.576h1.362c1.41 0 2.558 1.153 2.558 2.576a.052.052 0 0 1-.053.05H3.014Zm11.986-3h-2.5a.5.5 0 0 1 0-1H15a.5.5 0 0 1 0 1Zm0-2h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1Zm0-2h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1Z" />
                                </svg>
                                Kartu Digital
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('anggota.password') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('anggota.password') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M8 10V7a4 4 0 1 1 8 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1Zm2-3a2 2 0 1 1 4 0v3h-4V7Zm2 6a1 1 0 0 1 1 1v3a1 1 0 1 1-2 0v-3a1 1 0 0 1 1-1Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Ubah Password
                        </a>
                    </li>
                @endif
                @if(auth()->check() && auth()->user()->isAdmin())
                    <li class="pt-4 pb-2"><span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3">Administrator</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 22 21">
                                <path
                                    d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                <path
                                    d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.struktur') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.struktur') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Struktur Organisasi
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kantor') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.kantor') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Manajemen Kantor
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.manajemen') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.manajemen') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 18">
                                <path
                                    d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z" />
                            </svg>
                            Manajemen Anggota
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.admin') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.admin') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Manajemen Admin
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.input-manual') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all duration-200 {{ request()->routeIs('admin.input-manual') ? 'bg-primary-500/10 text-primary-700 font-semibold shadow-sm' : 'text-gray-600 hover:bg-white/60 hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.546.5a9.5 9.5 0 1 0 9.5 9.5 9.51 9.51 0 0 0-9.5-9.5ZM13.788 11h-3.242v3.242a1 1 0 1 1-2 0V11H5.304a1 1 0 0 1 0-2h3.242V5.758a1 1 0 0 1 2 0V9h3.242a1 1 0 1 1 0 2Z" />
                            </svg>
                            Input Manual
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </aside>

    <!-- Content -->
    <div class="p-4 sm:ml-64">
        <div class="p-2 sm:p-4 mt-16">
            {{ $slot }}
        </div>
    </div>

    @persist('notifications')
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pt-16"></div>
    @endpersist

    @livewireScripts
</body>

</html>