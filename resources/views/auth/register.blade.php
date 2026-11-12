<x-layout title="Daftar Akun">
    <div class="mx-auto max-w-md rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-1 text-lg font-semibold text-red-700">Daftar Akun Mahasiswa</h1>
        <p class="mb-6 text-sm text-gray-500">Akun ini nantinya bisa login pakai password biasa, TOTP, atau Passkey (FIDO2/WebAuthn) &mdash; diaktifkan dari menu Keamanan Akun setelah masuk.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" required
                        class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" required placeholder="IF-48-04"
                        class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Program Studi</label>
                <input type="text" name="prodi" value="{{ old('prodi') }}" required placeholder="S1 Informatika"
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required minlength="8"
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>

            <button class="w-full rounded bg-red-700 py-2 font-medium text-white hover:bg-red-800">Daftar</button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-500">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-red-700 hover:underline">Masuk</a>
        </p>
    </div>
</x-layout>
