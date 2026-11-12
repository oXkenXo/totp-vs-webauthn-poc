<x-layout title="Verifikasi TOTP">
    <div class="mx-auto max-w-sm rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-1 text-lg font-semibold text-red-700">Verifikasi TOTP</h1>
        <p class="mb-6 text-sm text-gray-500">Masukkan 6 digit kode dari aplikasi authenticator kamu.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.totp.verify') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" autofocus required
                placeholder="123456"
                class="w-full rounded border border-gray-300 text-center text-2xl tracking-[0.5em] focus:border-red-500 focus:ring-red-500">
            <button class="w-full rounded bg-red-700 py-2 font-medium text-white hover:bg-red-800">Verifikasi</button>
        </form>

        <p class="mt-4 text-center text-sm">
            <a href="{{ route('login') }}" class="text-gray-400 hover:underline">Batal, kembali ke login</a>
        </p>
    </div>
</x-layout>
