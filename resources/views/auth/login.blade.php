<x-layout title="Login">
    <div class="mx-auto max-w-md rounded-lg border bg-white p-6 shadow-sm">
        <h1 class="mb-1 text-lg font-semibold text-red-700">Masuk ke iGRACIAS</h1>
        <p class="mb-6 text-sm text-gray-500">Pilih metode login: password (+TOTP bila aktif), atau Passkey (FIDO2/WebAuthn) tanpa password sama sekali.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" required
                    class="mt-1 w-full rounded border border-gray-300 focus:border-red-500 focus:ring-red-500">
            </div>
            <button class="w-full rounded bg-red-700 py-2 font-medium text-white hover:bg-red-800">Masuk dengan Password</button>
        </form>

        <div class="my-5 flex items-center gap-3 text-xs uppercase text-gray-400">
            <span class="h-px flex-1 bg-gray-200"></span> atau <span class="h-px flex-1 bg-gray-200"></span>
        </div>

        <button id="passkey-login" type="button"
            class="w-full rounded border border-red-700 py-2 font-medium text-red-700 hover:bg-red-50">
            Masuk dengan Passkey
        </button>
        <p id="passkey-status" class="mt-2 text-center text-xs text-gray-400"></p>

        <p class="mt-4 text-center text-sm text-gray-500">
            Belum punya akun? <a href="{{ route('register') }}" class="text-red-700 hover:underline">Daftar</a>
        </p>
    </div>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        const statusEl = document.getElementById('passkey-status');
        const btn = document.getElementById('passkey-login');

        if (WebAuthn.doesntSupportWebAuthn()) {
            btn.disabled = true;
            statusEl.textContent = 'Browser ini tidak mendukung Passkey/WebAuthn.';
        }

        btn.addEventListener('click', async () => {
            statusEl.textContent = 'Menunggu perangkat/passkey kamu...';
            const email = document.getElementById('email').value;
            const webauthn = new WebAuthn({
                loginOptions: "{{ route('webauthn.login.options') }}",
                login: "{{ route('webauthn.login') }}",
            });

            try {
                await webauthn.login(email ? { email } : {});
                statusEl.textContent = 'Berhasil, mengarahkan ke dashboard...';
                window.location.href = "{{ route('dashboard') }}";
            } catch (e) {
                console.error(e);
                statusEl.textContent = 'Login dengan passkey gagal atau dibatalkan.';
            }
        });
    </script>
</x-layout>
