<?php
/**
 * ==========================================================
 * KONEKSI DATABASE & AUTO-INSTALLER SIGANA
 * PERUMDA TIRTA INTAN KABUPATEN GARUT
 * ==========================================================
 */

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = ''; // Default Laragon password kosong
$db_name = 'db_sigana';
$db_port = 3306;

try {
    // 1. Coba koneksi langsung ke database db_sigana
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

} catch (PDOException $e) {
    // 2. Jika database belum ada (Error 1049 Unknown database), lakukan Auto-Setup Otomatis
    try {
        $root_dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
        $temp_pdo = new PDO($root_dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Buat database
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $temp_pdo->exec("USE `{$db_name}`");

        // Skema Tabel
        $schema = "
        CREATE TABLE IF NOT EXISTS `users` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `username` VARCHAR(50) NOT NULL UNIQUE,
          `password` VARCHAR(255) NOT NULL,
          `nama_lengkap` VARCHAR(100) NOT NULL,
          `jabatan` VARCHAR(100) DEFAULT 'Petugas Humas & Teknis',
          `role` ENUM('admin', 'petugas') DEFAULT 'admin',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `kecamatan` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `nama_kecamatan` VARCHAR(100) NOT NULL UNIQUE,
          `cabang_pelayanan` VARCHAR(100) NOT NULL,
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
        ";
        $temp_pdo->exec($schema);

        // Seeder Akun Admin Default (Password: admin123)
        $hashed_pwd = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt_admin = $temp_pdo->prepare("INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `jabatan`, `role`) VALUES (1, 'admin', ?, 'Gazlan Pratama (Admin PKL)', 'Staff Humas & Pengaduan Pelanggan', 'admin') ON DUPLICATE KEY UPDATE `username`=`username`");
        $stmt_admin->execute([$hashed_pwd]);

        // Seeder Kecamatan Garut
        $temp_pdo->exec("
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
        ");

        // Seeder Contoh Pengumuman
        $temp_pdo->exec("
        INSERT INTO `pengumuman` (`id`, `nomor_tiket`, `judul`, `kecamatan_id`, `wilayah_terdampak`, `penyebab`, `tindakan`, `dampak_aliran`, `status`, `waktu_mulai`, `estimasi_selesai`, `kontak_posko`, `created_by`) VALUES
        (1, 'GNG-202608-001', 'Perbaikan Kebocoran Pipa Distribusi Utama HDPE 250mm', 2, 'Jl. Patriot, Perumahan Gordah, Perumahan Pemda, dan sekitarnya', 'Pipa distribusi transmisi utama mengalami kebocoran akibat tingginya tekanan air dan pergeseran tanah.', 'Tim Transmisi dan Distribusi (Trandis) sedang melakukan penggalian, pemotongan segmen pipa rusak, dan penyambungan mechanical joint.', 'mati_total', 'perbaikan', DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 4 HOUR), '0811-2000-1122', 1),
        (2, 'GNG-202608-002', 'Pemeliharaan Rutin & Pengurasan Bak Sedimentasi IPA Cikembar', 1, 'Kecamatan Garut Kota (Kel. Paminggir, Kel. Pakuwon, Kel. Muarasanding, Jl. Cimanuk sebagian)', 'Pemeliharaan berkala unit sedimentasi dan filtrasi guna menjaga kualitas kejernihan air bersih menjelang musim penghujan.', 'Petugas Water Treatment Plant (WTP) melakukan flushing lumpur dan kalibrasi klorinasi.', 'aliran_kecil', 'investigasi', DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 5 HOUR), '0811-2000-1123', 1),
        (3, 'GNG-202608-003', 'Gangguan Pompa Intake Akibat Fluktuasi Aliran Listrik PLN', 4, 'Desa Cimurah, Desa Situgede, Perumahan Graha Mandala Karangpawitan', 'Gangguan pasokan tegangan listrik di gardu penyuplai intake mata air cipancar sehingga pompa otomatis mati.', 'Koordinasi dengan tim teknis PLN Rayon Garut Kota dan pengoperasian genset darurat Perumda.', 'bertekanan_rendah', 'normalisasi', DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_ADD(NOW(), INTERVAL 1 HOUR), '0811-2000-1124', 1),
        (4, 'GNG-202608-004', 'Pembersihan Sumbatan Sampah di Intake Sungai Cimanuk', 3, 'Kp. Rancabango, Pasawahan, dan Jl. Otista Tarogong Kaler', 'Peningkatan volume sampah ranting pohon di kisi saringan intake pasca hujan deras.', 'Pembersihan manual kisi saringan (trash screen) dan penggelontoran lumpur.', 'mati_total', 'selesai', DATE_SUB(NOW(), INTERVAL 24 HOUR), DATE_SUB(NOW(), INTERVAL 18 HOUR), '0811-2000-1122', 1)
        ON DUPLICATE KEY UPDATE `nomor_tiket`=`nomor_tiket`;
        ");

        // Buka koneksi resmi
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    } catch (PDOException $fatal) {
        die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:10px;margin:30px auto;max-width:600px;'>
            <h2>⚠️ Gagal Terhubung ke MySQL Laragon</h2>
            <p>Pastikan servis <strong>MySQL</strong> di Laragon sudah di-klik <strong>Start All</strong>.</p>
            <p><strong>Pesan Error:</strong> " . htmlspecialchars($fatal->getMessage()) . "</p>
        </div>");
    }
}

// Mulai session global jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
