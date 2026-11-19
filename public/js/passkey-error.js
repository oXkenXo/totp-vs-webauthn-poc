async function describePasskeyError(error) {
    if (error instanceof Response) {
        if (error.status === 419) {
            return 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi. (HTTP 419)';
        }

        const body = await error.json().catch(() => null);
        const reason = body?.message ? `: ${body.message}` : '';

        return `Server menolak passkey${reason} (HTTP ${error.status})`;
    }

    const messages = {
        NotAllowedError: 'Dibatalkan atau waktu habis. Pastikan Windows Hello, sidik jari, atau PIN sudah aktif di perangkat ini, lalu coba lagi.',
        SecurityError: 'Alamat ini tidak bisa dipakai untuk passkey. Buka situs lewat alamat https dengan nama domain, bukan alamat IP atau http.',
        InvalidStateError: 'Passkey dari perangkat ini sudah terdaftar di akun kamu.',
        NotSupportedError: 'Browser atau perangkat ini tidak mendukung jenis passkey yang diminta.',
        AbortError: 'Proses passkey dihentikan sebelum selesai.',
    };

    const name = error?.name ?? 'Error';

    return `${messages[name] ?? error?.message ?? 'Terjadi kesalahan.'} (${name})`;
}
