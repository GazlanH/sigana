-- ==========================================================
-- DATABASE: db_sigana
-- SISTEM INFORMASI GANGGUAN AIR (SIGANA)
-- PERUMDA TIRTA INTAN KABUPATEN GARUT
-- Dibuat oleh: Gazlan (SMK RPL - PKL Tirta Intan Garut)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `db_sigana` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_sigana`;

-- ----------------------------------------------------------
-- 1. TABEL USERS (Petugas / Admin)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `jabatan` VARCHAR(100) DEFAULT 'Petugas Humas & Teknis',
  `role` ENUM('admin', 'petugas') DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 2. TABEL KECAMATAN (Wilayah Pelayanan Perumda Tirta Intan Garut)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kecamatan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kecamatan` VARCHAR(100) NOT NULL UNIQUE,
  `cabang_pelayanan` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 3. TABEL PENGUMUMAN (Data Gangguan & Perbaikan Air)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengumuman` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nomor_tiket` VARCHAR(30) NOT NULL UNIQUE,
  `judul` VARCHAR(200) NOT NULL,
  `kecamatan_id` INT NOT NULL,
  `wilayah_terdampak` TEXT NOT NULL,
  `penyebab` TEXT NOT NULL,
  `tindakan` TEXT NOT NULL,
  `dampak_aliran` ENUM('mati_total', 'aliran_kecil', 'bertekanan_rendah') DEFAULT 'mati_total',
  `status` ENUM('investigasi', 'perbaikan', 'normalisasi', 'selesai') DEFAULT 'perbaikan',
  `waktu_mulai` DATETIME NOT NULL,
  `estimasi_selesai` DATETIME NULL,
  `waktu_selesai_aktual` DATETIME NULL,
  `kontak_posko` VARCHAR(50) DEFAULT '0811-2345-6789',
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pengumuman_kecamatan` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pengumuman_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- DATA AWAL / SEEDER
-- ==========================================================

-- Admin Default (Password: admin123 -> $2y$10$eE.lE2W4m/V1f2R5b.sX/OXe0P.. / dynamic generated)
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `jabatan`, `role`) VALUES
(1, 'admin', '$2y$10$B00qM4t7wFj7n04BqB0P0uM4F0Tsm2zLg5kI3qZJ5jXz9qKz7c162', 'Gazlan Pratama (Admin PKL)', 'Staff Humas & Pengaduan Pelanggan', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Daftar Kecamatan Wilayah Pelayanan Perumda Tirta Intan Garut
INSERT INTO `kecamatan` (`id`, `nama_kecamatan`, `cabang_pelayanan`) VALUES
(1, 'Garut Kota', 'Cabang Garut Kota'),
(2, 'Tarogong Kidul', 'Cabang Tarogong'),
(3, 'Tarogong Kaler', 'Cabang Tarogong'),
(4, 'Karangpawitan', 'Cabang Karangpawitan'),
(5, 'Cilawu', 'Cabang Cilawu'),
(6, 'Samarang', 'Cabang Samarang'),
(7, 'Banyuresmi', 'Cabang Banyuresmi'),
(8, 'Bayongbong', 'Cabang Bayongbong'),
(9, 'Leles', 'Cabang Leles'),
(10, 'Kadungora', 'Cabang Kadungora'),
(11, 'Wanaraja', 'Cabang Wanaraja'),
(12, 'Limbangan', 'Cabang Limbangan')
ON DUPLICATE KEY UPDATE `nama_kecamatan`=`nama_kecamatan`;

-- Contoh Data Pengumuman Realistis
INSERT INTO `pengumuman` (`id`, `nomor_tiket`, `judul`, `kecamatan_id`, `wilayah_terdampak`, `penyebab`, `tindakan`, `dampak_aliran`, `status`, `waktu_mulai`, `estimasi_selesai`, `kontak_posko`, `created_by`) VALUES
(1, 'GNG-202608-001', 'Perbaikan Kebocoran Pipa Distribusi Utama HDPE 250mm', 2, 'Jl. Patriot, Perumahan Gordah, Perumahan Pemda, dan sekitarnya', 'Pipa distribusi transmisi utama mengalami kebocoran akibat tingginya tekanan air dan pergeseran tanah.', 'Tim Transmisi dan Distribusi (Trandis) sedang melakukan penggalian, pemotongan segmen pipa rusak, dan penyambungan mechanical joint.', 'mati_total', 'perbaikan', DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 4 HOUR), '0811-2000-1122', 1),

(2, 'GNG-202608-002', 'Pemeliharaan Rutin & Pengurasan Bak Sedimentasi IPA Cikembar', 1, 'Kecamatan Garut Kota (Kel. Paminggir, Kel. Pakuwon, Kel. Muarasanding, Jl. Cimanuk sebagian)', 'Pemeliharaan berkala unit sedimentasi dan filtrasi guna menjaga kualitas kejernihan air bersih menjelang musim penghujan.', 'Petugas Water Treatment Plant (WTP) melakukan flushing lumpur dan kalibrasi klorinasi.', 'aliran_kecil', 'investigasi', DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 5 HOUR), '0811-2000-1123', 1),

(3, 'GNG-202608-003', 'Gangguan Pompa Intake Akibat Fluktuasi Aliran Listrik PLN', 4, 'Desa Cimurah, Desa Situgede, Perumahan Graha Mandala Karangpawitan', 'Gangguan pasokan tegangan listrik di gardu penyuplai intake mata air cipancar sehingga pompa otomatis mati.', 'Koordinasi dengan tim teknis PLN Rayon Garut Kota dan pengoperasian genset darurat Perumda.', 'bertekanan_rendah', 'normalisasi', DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_ADD(NOW(), INTERVAL 1 HOUR), '0811-2000-1124', 1),

(4, 'GNG-202608-004', 'Pembersihan Sumbatan Sampah di Intake Sungai Cimanuk', 3, 'Kp. Rancabango, Pasawahan, dan Jl. Otista Tarogong Kaler', 'Peningkatan volume sampah ranting pohon di kisi saringan intake pasca hujan deras.', 'Pembersihan manual kisi saringan (trash screen) dan penggelontoran lumpur.', 'mati_total', 'selesai', DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 18 HOUR), '0811-2000-1122', 1)
ON DUPLICATE KEY UPDATE `nomor_tiket`=`nomor_tiket`;
