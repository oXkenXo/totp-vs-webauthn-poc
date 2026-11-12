<x-layout title="Keamanan Akun">
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:underline">&larr; Kembali ke Dashboard</a>
    </div>

    <h1 class="mb-6 text-lg font-semibold text-red-700">Keamanan Akun</h1>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="mb-1 font-semibold text-gray-700">TOTP (Authenticator App)</h2>
            @if (auth()->user()->totp_enabled)
                <p class="mb-4 text-sm text-green-600">Aktif &mdash; login akan meminta kode TOTP setelah password.</p>
                <form method="POST" action="{{ route('account.totp.disable') }}" class="space-y-2">
                    @csrf @method('DELETE')
                    <label class="block text-sm font-medium">Konfirmasi password untuk menonaktifkan</label>
                    <input type="password" name="password" required class="w-full rounded border border-gray-300">
                    @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <button class="rounded border border-red-700 px-4 py-1.5 text-sm text-red-700 hover:bg-red-50">Nonaktifkan TOTP</button>
                </form>
            @else
                <p class="mb-4 text-sm text-gray-500">Belum aktif. Aktifkan untuk menambah langkah verifikasi kedua saat login password.</p>
                <a href="{{ route('account.totp.setup') }}" class="inline-block rounded bg-red-700 px-4 py-1.5 text-sm text-white hover:bg-red-800">Aktifkan TOTP</a>
            @endif
        </div>

        <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="mb-1 font-semibold text-gray-700">Passkey (FIDO2/WebAuthn)</h2>
            <p class="mb-4 text-sm text-gray-500">Login tanpa password sama sekali, pakai sidik jari/Face ID/PIN perangkat atau security key.</p>

            @if ($passkeys->isEmpty())
                <p class="mb-3 text-sm text-gray-400">Belum ada passkey terdaftar.</p>
            @else
                <ul class="mb-3 divide-y text-sm">
                    @foreach ($passkeys as $passkey)
                        <li class="flex items-center justify-between py-2">
                            <span>
                                {{ $passkey->alias ?? 'Passkey' }}
                                <span class="text-xs text-gray-400">&middot; dibuat {{ $passkey->created_at->diffForHumans() }}</span>
                            </span>
                            <form method="POST" action="{{ route('account.passkeys.destroy', $passkey->id) }}" onsubmit="return confirm('Hapus passkey ini?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Hapus</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif

            <button id="passkey-register" type="button" class="rounded bg-red-700 px-4 py-1.5 text-sm text-white hover:bg-red-800">
                + Daftarkan Passkey Baru
            </button>
            <p id="passkey-register-status" class="mt-2 text-xs text-gray-400"></p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border bg-white p-6 shadow-sm">
        <h2 class="mb-3 font-semibold text-gray-700">Log Waktu &amp; Hasil Autentikasi</h2>
        @include('partials.auth-log-table', ['logs' => $authLogs])
    </div>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        const regBtn = document.getElementById('passkey-register');
        const regStatus = document.getElementById('passkey-register-status');

        regBtn.addEventListener('click', async () => {
            regStatus.textContent = 'Ikuti instruksi perangkat kamu...';
            const webauthn = new WebAuthn({
                registerOptions: "{{ route('webauthn.register.options') }}",
                register: "{{ route('webauthn.register') }}",
            });

            try {
                await webauthn.register();
                regStatus.textContent = 'Passkey berhasil ditambahkan, memuat ulang...';
                window.location.reload();
            } catch (e) {
                console.error(e);
                regStatus.textContent = 'Gagal mendaftarkan passkey atau dibatalkan.';
            }
        });
    </script>
</x-layout>
