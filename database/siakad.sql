CREATE DATABASE IF NOT EXISTS siakad_php_native;
USE siakad_php_native;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS nilai;
DROP TABLE IF EXISTS detail_krs;
DROP TABLE IF EXISTS krs;
DROP TABLE IF EXISTS jadwal_kuliah;
DROP TABLE IF EXISTS tahun_akademik;
DROP TABLE IF EXISTS mata_kuliah;
DROP TABLE IF EXISTS mahasiswa;
DROP TABLE IF EXISTS dosen;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dosen', 'mahasiswa') NOT NULL,
    status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mahasiswa (
    id_mahasiswa INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama_mahasiswa VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tanggal_lahir DATE NULL,
    alamat TEXT NULL,
    email VARCHAR(100) NULL UNIQUE,
    no_telepon VARCHAR(20) NULL,
    program_studi VARCHAR(100) NOT NULL,
    angkatan YEAR NOT NULL,
    CONSTRAINT fk_mahasiswa_users FOREIGN KEY (id_user) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dosen (
    id_dosen INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    nidn VARCHAR(30) NOT NULL UNIQUE,
    nama_dosen VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL UNIQUE,
    no_telepon VARCHAR(20) NULL,
    CONSTRAINT fk_dosen_users FOREIGN KEY (id_user) REFERENCES users (id_user) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mata_kuliah (
    id_mata_kuliah INT AUTO_INCREMENT PRIMARY KEY,
    kode_mata_kuliah VARCHAR(20) NOT NULL UNIQUE,
    nama_mata_kuliah VARCHAR(100) NOT NULL,
    sks TINYINT UNSIGNED NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tahun_akademik (
    id_tahun_akademik INT AUTO_INCREMENT PRIMARY KEY,
    tahun_akademik VARCHAR(20) NOT NULL UNIQUE,
    semester ENUM('ganjil', 'genap') NOT NULL,
    status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jadwal_kuliah (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    id_mata_kuliah INT NOT NULL,
    id_dosen INT NOT NULL,
    id_tahun_akademik INT NOT NULL,
    kelas VARCHAR(20) NOT NULL,
    hari VARCHAR(20) NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    ruangan VARCHAR(50) NOT NULL,
    kuota INT UNSIGNED NOT NULL DEFAULT 30,
    CONSTRAINT fk_jadwal_mata_kuliah FOREIGN KEY (id_mata_kuliah) REFERENCES mata_kuliah (id_mata_kuliah) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_jadwal_dosen FOREIGN KEY (id_dosen) REFERENCES dosen (id_dosen) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_jadwal_tahun_akademik FOREIGN KEY (id_tahun_akademik) REFERENCES tahun_akademik (id_tahun_akademik) ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_jadwal (id_mata_kuliah, id_dosen, id_tahun_akademik, kelas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE krs (
    id_krs INT AUTO_INCREMENT PRIMARY KEY,
    id_mahasiswa INT NOT NULL,
    id_tahun_akademik INT NOT NULL,
    tanggal_pengisian DATE NOT NULL,
    status_krs ENUM('draft', 'diajukan', 'disetujui', 'dikunci') NOT NULL DEFAULT 'draft',
    CONSTRAINT fk_krs_mahasiswa FOREIGN KEY (id_mahasiswa) REFERENCES mahasiswa (id_mahasiswa) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_krs_tahun_akademik FOREIGN KEY (id_tahun_akademik) REFERENCES tahun_akademik (id_tahun_akademik) ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_krs (id_mahasiswa, id_tahun_akademik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detail_krs (
    id_detail_krs INT AUTO_INCREMENT PRIMARY KEY,
    id_krs INT NOT NULL,
    id_jadwal INT NOT NULL,
    status ENUM('aktif', 'batal') NOT NULL DEFAULT 'aktif',
    CONSTRAINT fk_detail_krs_krs FOREIGN KEY (id_krs) REFERENCES krs (id_krs) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detail_krs_jadwal FOREIGN KEY (id_jadwal) REFERENCES jadwal_kuliah (id_jadwal) ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_detail_krs (id_krs, id_jadwal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE nilai (
    id_nilai INT AUTO_INCREMENT PRIMARY KEY,
    id_detail_krs INT NOT NULL UNIQUE,
    nilai_tugas DECIMAL(5,2) NOT NULL DEFAULT 0,
    nilai_uts DECIMAL(5,2) NOT NULL DEFAULT 0,
    nilai_uas DECIMAL(5,2) NOT NULL DEFAULT 0,
    nilai_akhir DECIMAL(5,2) NOT NULL DEFAULT 0,
    nilai_huruf VARCHAR(3) NOT NULL DEFAULT 'E',
    bobot DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_nilai_detail_krs FOREIGN KEY (id_detail_krs) REFERENCES detail_krs (id_detail_krs) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password, role, status) VALUES
('admin', '$2b$12$BIDPaXtVSSGB6yGHupYw0.DyjGf.Kz45VYM/3TYGJUxyvjjUMKp.2', 'admin', 'aktif'),
('dosen001', '$2b$12$40rtIUkVzqI2EZbVy7BwP.gZlbORy6oEf8z1XnH..JXK6oxuKWqAK', 'dosen', 'aktif'),
('dosen002', '$2b$12$40rtIUkVzqI2EZbVy7BwP.gZlbORy6oEf8z1XnH..JXK6oxuKWqAK', 'dosen', 'aktif'),
('mahasiswa001', '$2b$12$aMz7tsPG/ICp77JCLKTGpOF8ETD5R4IBo5vuIE5Kdrobkl9u9ryIe', 'mahasiswa', 'aktif'),
('mahasiswa002', '$2b$12$aMz7tsPG/ICp77JCLKTGpOF8ETD5R4IBo5vuIE5Kdrobkl9u9ryIe', 'mahasiswa', 'aktif'),
('mahasiswa003', '$2b$12$aMz7tsPG/ICp77JCLKTGpOF8ETD5R4IBo5vuIE5Kdrobkl9u9ryIe', 'mahasiswa', 'aktif'),
('mahasiswa004', '$2b$12$aMz7tsPG/ICp77JCLKTGpOF8ETD5R4IBo5vuIE5Kdrobkl9u9ryIe', 'mahasiswa', 'aktif'),
('mahasiswa005', '$2b$12$aMz7tsPG/ICp77JCLKTGpOF8ETD5R4IBo5vuIE5Kdrobkl9u9ryIe', 'mahasiswa', 'aktif');

INSERT INTO mahasiswa (id_user, nim, nama_mahasiswa, jenis_kelamin, tanggal_lahir, alamat, email, no_telepon, program_studi, angkatan) VALUES
(4, '2023001', 'Mahasiswa Satu', 'L', '2004-01-10', 'Bandung', 'mahasiswa001@example.com', '081111111111', 'Teknik Informatika', 2023),
(5, '2023002', 'Mahasiswa Dua', 'P', '2004-02-12', 'Jakarta', 'mahasiswa002@example.com', '081111111112', 'Sistem Informasi', 2023),
(6, '2023003', 'Mahasiswa Tiga', 'L', '2004-03-14', 'Bekasi', 'mahasiswa003@example.com', '081111111113', 'Teknik Informatika', 2023),
(7, '2023004', 'Mahasiswa Empat', 'P', '2004-04-16', 'Bogor', 'mahasiswa004@example.com', '081111111114', 'Sistem Informasi', 2023),
(8, '2023005', 'Mahasiswa Lima', 'L', '2004-05-18', 'Depok', 'mahasiswa005@example.com', '081111111115', 'Teknik Informatika', 2023);

INSERT INTO dosen (id_user, nidn, nama_dosen, email, no_telepon) VALUES
(2, '0001001', 'Dosen Satu', 'dosen001@example.com', '082211111111'),
(3, '0001002', 'Dosen Dua', 'dosen002@example.com', '082211111112');

INSERT INTO mata_kuliah (kode_mata_kuliah, nama_mata_kuliah, sks, semester) VALUES
('IF101', 'Pemrograman Web', 3, 1),
('IF102', 'Basis Data', 3, 2),
('IF103', 'Struktur Data', 3, 3),
('IF104', 'Analisis Sistem', 3, 4),
('IF105', 'Jaringan Komputer', 3, 5),
('IF106', 'Kecerdasan Buatan', 3, 6);

INSERT INTO tahun_akademik (tahun_akademik, semester, status) VALUES
('2024/2025', 'ganjil', 'aktif'),
('2024/2025', 'genap', 'nonaktif');

INSERT INTO jadwal_kuliah (id_mata_kuliah, id_dosen, id_tahun_akademik, kelas, hari, jam_mulai, jam_selesai, ruangan, kuota) VALUES
(1, 1, 1, 'A', 'Senin', '08:00:00', '10:30:00', 'R101', 30),
(2, 2, 1, 'A', 'Selasa', '10:30:00', '13:00:00', 'R102', 30),
(3, 1, 1, 'A', 'Rabu', '08:00:00', '10:30:00', 'R103', 30),
(4, 2, 1, 'A', 'Kamis', '13:00:00', '15:30:00', 'R104', 30),
(5, 1, 1, 'A', 'Jumat', '08:00:00', '10:30:00', 'R105', 30),
(6, 2, 1, 'A', 'Sabtu', '10:30:00', '13:00:00', 'R106', 30);

INSERT INTO krs (id_mahasiswa, id_tahun_akademik, tanggal_pengisian, status_krs) VALUES
(1, 1, '2024-09-01', 'disetujui'),
(2, 1, '2024-09-01', 'disetujui');

INSERT INTO detail_krs (id_krs, id_jadwal, status) VALUES
(1, 1, 'aktif'),
(1, 2, 'aktif'),
(1, 3, 'aktif'),
(2, 2, 'aktif'),
(2, 4, 'aktif'),
(2, 5, 'aktif');

INSERT INTO nilai (id_detail_krs, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, nilai_huruf, bobot) VALUES
(1, 85, 80, 90, 86.50, 'A', 4.00),
(2, 78, 82, 80, 80.00, 'A-', 3.75),
(3, 70, 75, 72, 72.40, 'B', 3.00),
(4, 88, 90, 92, 90.80, 'A', 4.00),
(5, 65, 68, 70, 68.10, 'B-', 2.75),
(6, 55, 60, 58, 58.10, 'C', 2.00);
