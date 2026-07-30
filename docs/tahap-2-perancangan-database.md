# Tahap 2 - Perancangan Database

## Tujuan
Membuat struktur database yang ternormalisasi untuk mendukung autentikasi, pengelolaan data akademik, KRS, nilai, dan perhitungan IPS/IPK.

## Tabel dan Fungsi

### 1. users
Menyimpan akun login semua role pengguna.

### 2. mahasiswa
Menyimpan biodata mahasiswa dan relasi ke akun login.

### 3. dosen
Menyimpan biodata dosen dan relasi ke akun login.

### 4. mata_kuliah
Menyimpan master data mata kuliah.

### 5. tahun_akademik
Menyimpan tahun akademik aktif dan semester.

### 6. jadwal_kuliah
Menyimpan jadwal kuliah, dosen pengampu, kelas, dan kuota.

### 7. krs
Menyimpan header pengisian KRS mahasiswa per tahun akademik.

### 8. detail_krs
Menyimpan mata kuliah yang dipilih pada KRS.

### 9. nilai
Menyimpan nilai tugas, UTS, UAS, nilai akhir, huruf, dan bobot.

## Relasi Antar Tabel
- users 1:1 mahasiswa
- users 1:1 dosen
- mahasiswa 1:N krs
- tahun_akademik 1:N krs
- krs 1:N detail_krs
- jadwal_kuliah 1:N detail_krs
- detail_krs 1:1 nilai
- mata_kuliah 1:N jadwal_kuliah
- dosen 1:N jadwal_kuliah

## Catatan Normalisasi
- Data login dipisahkan dari data profil.
- Data mata kuliah dipisahkan dari jadwal.
- Data KRS dipisahkan antara header dan detail.
- Data nilai disimpan terpisah agar tidak menduplikasi informasi KRS.
- Tahun akademik dibatasi unik pada kombinasi `tahun_akademik` dan `semester`, bukan hanya tahun saja, karena satu tahun akademik memiliki dua semester.
