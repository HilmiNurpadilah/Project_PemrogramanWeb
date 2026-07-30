# Tahap 1 - Analisis Kebutuhan Sistem

## Tujuan
Menetapkan ruang lingkup, aktor, hak akses, alur proses, dan kebutuhan sistem untuk Aplikasi Sistem Informasi Akademik.

## Aktor
- Admin
- Dosen
- Mahasiswa

## Use Case Utama
- Login dan logout
- Pengelolaan data master oleh admin
- Input nilai oleh dosen
- Pengisian KRS dan melihat KHS/IPK oleh mahasiswa

## Kebutuhan Fungsional
- Autentikasi multi-role
- Pembatasan akses berdasarkan role
- CRUD data akademik
- Pengisian KRS
- Input dan perhitungan nilai
- Perhitungan IPS dan IPK

## Kebutuhan Nonfungsional
- Aman dari SQL Injection
- Password disimpan dengan hashing
- Antarmuka responsif
- Kompatibel dengan PHP 8 dan MySQL
