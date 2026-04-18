<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SiMOK — Sistem Informasi Manajemen Organisasi Keanggotaan">

    <title>{{ $title ?? 'SiMOK' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#0f172a] bg-[radial-gradient(ellipse_at_20%_50%,oklch(0.4_0.2_250_/_0.3)_0%,transparent_50%),radial-gradient(ellipse_at_80%_20%,oklch(0.35_0.15_300_/_0.25)_0%,transparent_50%),radial-gradient(ellipse_at_60%_80%,oklch(0.3_0.18_220_/_0.2)_0%,transparent_50%)] font-sans antialiased">
    <div class="relative z-10">
        {{ $slot }}
    </div>

    @persist('notifications')
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
    @endpersist

    @livewireScripts
</body>
</html>
