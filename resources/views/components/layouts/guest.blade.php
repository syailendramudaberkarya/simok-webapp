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
<body class="min-h-screen bg-mesh font-sans antialiased">
    <div class="relative z-10">
        {{ $slot }}
    </div>

    @persist('notifications')
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>
    @endpersist

    @livewireScripts
</body>
</html>
