<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'iGRACIAS' }} | TOTP vs WebAuthn POC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-red-700 text-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-2 font-bold tracking-tight">
            <span class="text-xl">i<span class="text-white/90">GRACIAS</span></span>
            <span class="hidden text-xs font-normal text-white/70 sm:inline">TOTP vs WebAuthn POC</span>
        </a>
        @auth
            <div class="flex items-center gap-3 text-sm">
                <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded bg-red-900/60 px-3 py-1.5 hover:bg-red-900">Keluar</button>
                </form>
            </div>
        @endauth
    </div>
</header>

<main class="mx-auto max-w-6xl px-4 py-8">
    @if (session('status'))
        <div class="mb-6 rounded border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    {{ $slot }}
</main>

<footer class="mt-16 border-t bg-white py-4 text-center text-xs text-gray-400">
    totp-vs-webauthn-poc &mdash; dibangun untuk riset perbandingan metode login, tampilan terinspirasi i-Gracias Telkom University.
</footer>

</body>
</html>
