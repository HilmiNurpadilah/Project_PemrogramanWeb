# Sistem Informasi Akademik Berbasis Web Menggunakan PHP Native dan MySQL

Proyek ini adalah aplikasi Sistem Informasi Akademik sederhana untuk Admin, Dosen, dan Mahasiswa. Aplikasi dibangun menggunakan PHP native, MySQL, HTML, CSS, JavaScript, dan Bootstrap tanpa framework backend.

## Tujuan Proyek

Membangun aplikasi akademik sederhana yang dapat digunakan untuk mengelola data master, KRS, nilai, KHS, IPS, dan IPK.

## Teknologi

- Backend: PHP native
- Database: MySQL
- Frontend: HTML, CSS, JavaScript, Bootstrap
- Environment: XAMPP atau Laragon
- Version control: Git dan GitHub

## Role Pengguna

- Admin
- Dosen
- Mahasiswa

## Fitur Utama

- Login multi-role dan logout
- Session login dan pembatasan akses berdasarkan role
- Manajemen data mahasiswa, dosen, mata kuliah, tahun akademik, dan jadwal kuliah
- Dosen melihat mata kuliah yang diampu, daftar mahasiswa, dan input nilai
- Mahasiswa mengisi KRS, menghapus item KRS sebelum dikunci, melihat KRS, KHS, IPS, dan IPK
- Perhitungan nilai akhir, nilai huruf, bobot, mutu, IPS, dan IPK otomatis

## Struktur Folder

```text
siakad-php-native/
├── admin/
├── config/
├── database/
├── docs/
├── dosen/
├── includes/
├── mahasiswa/
├── assets/
├── login.php
├── logout.php
├── index.php
└── README.md
```

## Instalasi XAMPP

1. Install dan jalankan XAMPP.
2. Aktifkan `Apache` dan `MySQL`.
3. Salin folder proyek ke `htdocs`.
4. Buka phpMyAdmin melalui `http://localhost/phpmyadmin`.
5. Import file [database/siakad.sql](database/siakad.sql) ke MySQL.
6. Sesuaikan koneksi database jika username/password MySQL berbeda dari default.
7. Buka aplikasi melalui browser.

## Pengaturan Database

File koneksi database ada di [config/database.php](config/database.php). Jika MySQL Anda tidak memakai `root` tanpa password, ubah nilai berikut:

```php
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = '';
$dbName = 'siakad_php_native';
```

## Cara Import Database

1. Buka phpMyAdmin.
2. Buat database `siakad_php_native` jika belum ada.
3. Pilih database tersebut.
4. Jalankan menu import.
5. Pilih file [database/siakad.sql](database/siakad.sql).
6. Import dan pastikan tabel berhasil dibuat.
7. Jika database sudah terlanjur terisi sebagian dan import lama pernah gagal, drop database lalu import ulang supaya skema lama tidak tersisa.

## Akun Uji

### Admin
- Username: `admin`
- Password: `admin123`

### Dosen
- Username: `dosen001`
- Password: `dosen123`
- Username: `dosen002`
- Password: `dosen123`

### Mahasiswa
- Username: `mahasiswa001`
- Password: `mahasiswa123`
- Username: `mahasiswa002`
- Password: `mahasiswa123`
- Username: `mahasiswa003`
- Password: `mahasiswa123`
- Username: `mahasiswa004`
- Password: `mahasiswa123`
- Username: `mahasiswa005`
- Password: `mahasiswa123`

## Dokumentasi Fitur

### Admin
- Dashboard admin
- CRUD mahasiswa
- CRUD dosen
- CRUD mata kuliah
- CRUD tahun akademik
- CRUD jadwal kuliah

### Dosen
- Dashboard dosen
- Melihat mata kuliah yang diampu
- Melihat daftar mahasiswa per mata kuliah
- Input dan edit nilai mahasiswa
- Perhitungan nilai akhir, huruf, dan bobot otomatis

### Mahasiswa
- Dashboard mahasiswa
- Isi KRS
- Hapus mata kuliah dari KRS sebelum dikunci
- Kunci KRS
- Lihat KRS
- Lihat KHS
- Lihat IPS
- Lihat IPK

## Rumus Perhitungan

- Nilai Akhir = 30% Nilai Tugas + 30% Nilai UTS + 40% Nilai UAS
- Mutu Mata Kuliah = Bobot Nilai × Jumlah SKS
- IPS = Total Mutu Semester / Total SKS Semester
- IPK = Total Mutu Kumulatif / Total SKS Kumulatif

## Panduan Git dari Awal

```bash
git init
git branch -M main
git remote add origin https://github.com/HilmiNurpadilah/Project_PemrogramanWeb.git
git add .
git commit -m "Initial project structure"
git push -u origin main
```

## Rencana Commit GitHub

Riwayat pengembangan proyek dikerjakan bertahap dan sudah ditargetkan lebih dari 25 commit agar terlihat proses pengerjaannya. Contoh alur commit yang digunakan:

1. Initial project structure
2. Add README and project documentation
3. Add database connection configuration
4. Create database schema
5. Add initial seed data
6. Create login page interface
7. Implement authentication process
8. Add logout functionality
9. Add role-based access control
10. Create admin dashboard layout
11. Add mahasiswa data listing
12. Add mahasiswa create functionality
13. Add mahasiswa edit functionality
14. Add mahasiswa delete functionality
15. Add dosen management feature
16. Add mata kuliah management feature
17. Add tahun akademik management feature
18. Add jadwal kuliah management feature
19. Create dosen dashboard
20. Add dosen course listing
21. Add student list for lecturer
22. Implement nilai input feature
23. Implement nilai calculation
24. Create mahasiswa dashboard
25. Add mata kuliah selection page
26. Implement KRS submission
27. Add KRS credit validation
28. Add KRS history page
29. Add KHS and nilai page
30. Implement IPS calculation
31. Implement IPK calculation
32. Improve form validation
33. Add application security improvements
34. Improve responsive interface
35. Fix bugs and finalize documentation

## Catatan Keamanan

- Password disimpan dengan `password_hash()`.
- Login diverifikasi dengan `password_verify()`.
- Query menggunakan prepared statement.
- Output disanitasi dengan `htmlspecialchars()`.
- Form penting menggunakan CSRF token.
- Akses halaman dibatasi berdasarkan role.

## Referensi Dokumentasi Tahap

- [Tahap 1 - Analisis Kebutuhan](docs/tahap-1-analisis-kebutuhan.md)
- [Tahap 2 - Perancangan Database](docs/tahap-2-perancangan-database.md)
- [ERD Mermaid](docs/erd-mermaid.md)

