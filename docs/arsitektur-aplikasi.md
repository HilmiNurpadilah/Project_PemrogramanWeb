# Arsitektur Aplikasi

## Lapisan Utama

- **Presentasi**: halaman PHP, Bootstrap, HTML, CSS, dan JavaScript.
- **Logika Aplikasi**: file di `config/` dan folder role seperti `admin/`, `dosen/`, dan `mahasiswa/`.
- **Database**: MySQL dengan skema pada `database/siakad.sql`.

## Alur Umum

1. Pengguna login melalui `login.php`.
2. Sistem memverifikasi akun dan role.
3. Pengguna diarahkan ke dashboard sesuai role.
4. Aktivitas CRUD dan transaksi akademik dilakukan melalui prepared statement.

## Fokus Keamanan

- Session login
- Pembatasan role
- CSRF token pada form penting
- Sanitasi output
