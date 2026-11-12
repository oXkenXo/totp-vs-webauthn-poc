<x-layout title="Dashboard">
    <div class="grid gap-6 md:grid-cols-[260px_1fr]">
        <aside class="rounded-lg border bg-white p-4 shadow-sm">
            <div class="mb-4 flex flex-col items-center text-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-xl font-bold text-red-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <p class="mt-2 font-semibold">{{ $user->name }}</p>
                <p class="text-xs text-gray-400">{{ $user->nim }}</p>
            </div>
            <nav class="space-y-1 text-sm">
                <a href="{{ route('dashboard') }}" class="block rounded bg-red-700 px-3 py-2 font-medium text-white">Beranda</a>
                <a href="{{ route('account.security') }}" class="block rounded px-3 py-2 text-gray-600 hover:bg-gray-100">Keamanan Akun</a>
                <span class="block cursor-not-allowed rounded px-3 py-2 text-gray-300" title="Stub tampilan saja">Nilai (KHS)</span>
                <span class="block cursor-not-allowed rounded px-3 py-2 text-gray-300" title="Stub tampilan saja">Perwalian</span>
                <span class="block cursor-not-allowed rounded px-3 py-2 text-gray-300" title="Stub tampilan saja">Pembayaran</span>
            </nav>
        </aside>

        <div class="space-y-6">
            <div class="rounded-lg border bg-white p-6 shadow-sm">
                <h1 class="text-lg font-semibold text-red-700">Selamat datang, {{ $user->name }}</h1>
                <dl class="mt-4 grid grid-cols-2 gap-y-2 text-sm sm:grid-cols-4">
                    <dt class="text-gray-400">NIM</dt><dd class="col-span-1">{{ $user->nim }}</dd>
                    <dt class="text-gray-400">Kelas</dt><dd>{{ $user->kelas }}</dd>
                    <dt class="text-gray-400">Program Studi</dt><dd class="col-span-3 sm:col-span-1">{{ $user->prodi }}</dd>
                    <dt class="text-gray-400">Email</dt><dd class="col-span-3 sm:col-span-1">{{ $user->email }}</dd>
                </dl>
            </div>

            <div class="rounded-lg border bg-white p-6 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-700">Riwayat Login Terakhir</h2>
                    <a href="{{ route('account.security') }}" class="text-sm text-red-700 hover:underline">Lihat semua &rarr;</a>
                </div>
                @include('partials.auth-log-table', ['logs' => $recentLogs])
            </div>
        </div>
    </div>
</x-layout>
