# Keamanan Aplikasi

## Proteksi yang Digunakan

- `password_hash()` dan `password_verify()` untuk autentikasi.
- Prepared statement untuk seluruh query data dinamis.
- `htmlspecialchars()` melalui helper `e()` untuk output.
- `session_regenerate_id(true)` setelah login berhasil.
- CSRF token untuk form penting.

## Pembatasan Akses

- Pengguna yang belum login tidak bisa membuka dashboard.
- Admin, dosen, dan mahasiswa hanya bisa mengakses halaman role masing-masing.
- Dosen hanya bisa mengakses jadwal yang diampu.
- Mahasiswa hanya bisa mengubah KRS miliknya sendiri.
