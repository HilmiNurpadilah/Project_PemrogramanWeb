# Panduan Instalasi

1. Install XAMPP atau Laragon.
2. Aktifkan Apache dan MySQL.
3. Salin folder proyek ke `htdocs` atau folder web root Laragon.
4. Import database dari [database/siakad.sql](../database/siakad.sql) melalui phpMyAdmin.
5. Sesuaikan koneksi database pada [config/database.php](../config/database.php) bila username atau password MySQL berbeda.
6. Jalankan aplikasi melalui browser.

## Catatan

- Pastikan PHP 8 tersedia.
- Pastikan ekstensi `mysqli` aktif.
- Gunakan browser modern untuk hasil tampilan responsif yang lebih baik.
