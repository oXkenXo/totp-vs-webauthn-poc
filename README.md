# totp-vs-webauthn-poc

Proof-of-concept perbandingan dua metode login modern: **TOTP (Authenticator App)** vs **WebAuthn/Passkey (FIDO2)**, dibungkus dalam tampilan dashboard mirip **i-Gracias Telkom University** (hanya skin visual + data profil akademik untuk konteks; bukan clone fungsional sistem i-Gracias yang sebenarnya).

Setiap percobaan login (berhasil maupun gagal) dicatat ke tabel `auth_logs` (waktu, metode, status, durasi dalam ms, IP, user agent) sehingga bisa dipakai untuk analisis/skripsi perbandingan UX & kecepatan kedua metode.

## Fitur

- **Register akun** (nama, NIM, prodi, kelas, email, password)
- **Login password** biasa
- **Login password + TOTP** (two-factor, kode 6 digit dari Google Authenticator/Authy, setup via scan QR)
- **Login dengan Passkey (FIDO2/WebAuthn)** — tanpa password sama sekali, pakai biometrik/PIN perangkat atau security key
- **Dashboard akun** bergaya i-Gracias (header merah, profil NIM/prodi/kelas)
- **Logout**
- **Log waktu & hasil autentikasi** per metode, terlihat di dashboard & halaman Keamanan Akun
- Kelola TOTP (aktifkan/nonaktifkan) dan Passkey (tambah/hapus) dari halaman **Keamanan Akun**

## Stack & alasan pemilihan

| Bagian | Pilihan | Alasan |
|---|---|---|
| Backend | **Laravel 13** (PHP 8.3) | Sesuai request tim (pakai middleware Laravel), ekosistem auth paling matang di PHP |
| TOTP | `pragmarx/google2fa` + `bacon/bacon-qr-code` | Library TOTP standar RFC 6238, ringan, QR di-render sendiri jadi SVG (tanpa API eksternal) |
| WebAuthn/Passkey | `laragear/webauthn` | Implementasi FIDO2/WebAuthn server-side paling lengkap untuk Laravel, sudah sediakan trait model + request helper |
| Frontend | Blade + Tailwind CSS v4 (Vite) | Cukup untuk SSR form-based app, tidak perlu SPA untuk POC ini |
| WebAuthn client JS | `webauthn.js` bawaan paket (vanilla JS, di `public/js/`) | Tidak perlu bundel npm tambahan, cukup satu `<script>` tag |
| Database | **SQLite** (`database/database.sqlite`) | Paling gampang dijalankan semua anggota tim tanpa setup server DB; tinggal ganti `DB_CONNECTION` di `.env` kalau mau pakai MySQL (Laragon) |
| Auth logging | Tabel `auth_logs` custom + `App\Models\AuthLog::record()` | Supaya data waktu/metode/status bisa dipakai analisis kuantitatif (bahan riset), bukan cuma log teks |

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk development frontend dengan hot-reload, jalankan `npm run dev` di terminal terpisah (ganti `npm run build`).

## Alur yang bisa dicoba

1. **Daftar** di `/register`.
2. Login pakai **password saja** dulu di `/login`.
3. Ke **Keamanan Akun** → aktifkan **TOTP** (scan QR pakai Google Authenticator), lalu logout & login lagi — akan diminta kode TOTP setelah password.
4. Di **Keamanan Akun**, klik **+ Daftarkan Passkey Baru** (butuh browser & perangkat yang mendukung WebAuthn — Windows Hello, Touch ID, atau security key USB).
5. Logout, lalu di halaman login klik **Masuk dengan Passkey** — tanpa isi password sama sekali.
6. Lihat **Log Waktu & Hasil Autentikasi** di dashboard / halaman Keamanan Akun untuk bandingkan durasi & tingkat keberhasilan tiap metode.

## Catatan & keterbatasan

- WebAuthn butuh konteks aman: `localhost` otomatis dianggap *secure context* oleh browser, tapi untuk deploy ke domain lain wajib HTTPS. Set `WEBAUTHN_ID` di `.env` ke domain produksi kalau sudah deploy.
- Pendaftaran passkey (`webauthn/register/options`, `webauthn/register`) sengaja diletakkan di belakang middleware `auth` — hanya user yang sudah login (lewat password/TOTP) yang boleh menambahkan passkey baru. Login passkey sendiri (`webauthn/login*`) terbuka untuk guest, sesuai sifatnya yang *passwordless*.
- Kolom sensitif (`totp_secret`, `password`) disembunyikan dari serialisasi model (`#[Hidden(...)]` di `App\Models\User`).
- Ini POC riset, bukan untuk produksi langsung — belum ada rate limiting pada percobaan login, verifikasi email, atau recovery codes untuk TOTP.
