<x-layout title="Aktifkan TOTP">
    <div class="mx-auto max-w-sm rounded-lg border bg-white p-6 shadow-sm text-center">
        <h1 class="mb-1 text-lg font-semibold text-red-700">Aktifkan TOTP</h1>
        <p class="mb-4 text-sm text-gray-500">Scan QR ini pakai Google Authenticator / Authy, lalu masukkan kode 6 digit yang muncul.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-left text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mx-auto mb-4 w-fit rounded border p-3">
            {!! $qr !!}
        </div>

        <details class="mb-4 text-left text-xs text-gray-400">
            <summary class="cursor-pointer">Tidak bisa scan? Masukkan manual</summary>
            <code class="mt-1 block break-all rounded bg-gray-50 p-2">{{ $secret }}</code>
        </details>

        <form method="POST" action="{{ route('account.totp.enable') }}" class="space-y-3">
            @csrf
            <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required
                placeholder="123456"
                class="w-full rounded border border-gray-300 text-center text-2xl tracking-[0.5em] focus:border-red-500 focus:ring-red-500">
            <button class="w-full rounded bg-red-700 py-2 font-medium text-white hover:bg-red-800">Konfirmasi &amp; Aktifkan</button>
        </form>

        <a href="{{ route('account.security') }}" class="mt-4 block text-sm text-gray-400 hover:underline">Batal</a>
    </div>
</x-layout>
