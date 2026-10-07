<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Modul Konfigurasi & Koneksi Database (Pure Native PHP PDO)
 * Mendukung pembacaan konfigurasi dari .env atau default Laragon/MySQL.
 * ============================================================================
 */

// 1. Parser .env Mandiri (Zero Dependency / Tanpa Composer)
function loadEnvVariables($filePath = null) {
    if ($filePath === null) {
        $filePath = dirname(__DIR__) . '/.env';
    }

    if (!file_exists($filePath) || !is_readable($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Lewati komentar
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);

            // Bersihkan kutip ganda atau tunggal di sekitar value
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }

            // Simpan ke $_ENV dan $_SERVER jika belum diset dari server environment
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
            }
            if (!isset($_SERVER[$key])) {
                $_SERVER[$key] = $val;
            }
            putenv("{$key}={$val}");
        }
    }
}

// Muat .env jika tersedia
loadEnvVariables();

// 2. Helper env() Global (Native PHP)
if (!function_exists('env')) {
    function env($key, $default = null) {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }

        switch (strtolower((string)$val)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        return $val;
    }
}

// 3. Konfigurasi Parameter Database
$dbHost     = env('DB_HOST', '127.0.0.1');
$dbPort     = env('DB_PORT', '3306');
$dbDatabase = env('DB_DATABASE', 'db_sigana');
$dbUsername = env('DB_USERNAME', 'root');
$dbPassword = env('DB_PASSWORD', '');
$dbCharset  = 'utf8mb4';

// 4. Inisialisasi Koneksi PDO & Auto-Setup Database/Tabel
try {
    $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Coba koneksi langsung ke database target
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset={$dbCharset}";
        $pdo = new PDO($dsn, $dbUsername, $dbPassword, $pdoOptions);
    } catch (PDOException $e) {
        // Jika database belum ada, koneksi ke MySQL server dan buat databasenya
        $dsnServer = "mysql:host={$dbHost};port={$dbPort};charset={$dbCharset}";
        $pdoServer = new PDO($dsnServer, $dbUsername, $dbPassword, $pdoOptions);
        $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$dbDatabase}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdoServer->exec("USE `{$dbDatabase}`");
        $pdo = $pdoServer;
    }

    // Set variable PDO global
    $GLOBALS['pdo'] = $pdo;

    // 5. Cek kelengkapan tabel (Auto-Migrasi dari database.sql jika belum terisi)
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'pengumuman'");
    if ($tableCheck->rowCount() === 0) {
        $sqlFile = dirname(__DIR__) . '/database.sql';
        if (file_exists($sqlFile)) {
            $sqlContent = file_get_contents($sqlFile);
            if (!empty($sqlContent)) {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

                // Pastikan tipe users.id sinkron jika tabel users sempat dibuat sebelumnya
                $checkUsers = $pdo->query("SHOW TABLES LIKE 'users'");
                if ($checkUsers->rowCount() > 0) {
                    try {
                        $pdo->exec("ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED AUTO_INCREMENT");
                    } catch (Exception $ex) {}
                }

                $pdo->exec($sqlContent);
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
            }
        }
    }

} catch (PDOException $e) {
    // Tampilan troubleshooting yang ramah jika terjadi kegagalan database
    die("
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Koneksi Database Gagal - SIGANA</title>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'>
    </head>
    <body class='bg-light d-flex align-items-center min-vh-100 p-3'>
        <div class='container' style='max-width: 650px;'>
            <div class='card shadow-sm border-danger'>
                <div class='card-header bg-danger text-white py-3'>
                    <h5 class='mb-0 fw-bold'>⚠️ Gagal Terhubung ke Database MySQL</h5>
                </div>
                <div class='card-body p-4'>
                    <p class='mb-2'>Sistem <strong>SIGANA</strong> tidak dapat terhubung ke server MySQL dengan konfigurasi berikut:</p>
                    <table class='table table-sm table-bordered mb-3'>
                        <tr><th style='width: 35%;'>Host:Port</th><td><code>{$dbHost}:{$dbPort}</code></td></tr>
                        <tr><th>Database</th><td><code>{$dbDatabase}</code></td></tr>
                        <tr><th>Username</th><td><code>{$dbUsername}</code></td></tr>
                    </table>
                    <div class='alert alert-secondary py-2 px-3 small mb-3'>
                        <strong>Pesan Error:</strong><br>
                        <code>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</code>
                    </div>
                    <div class='card bg-light p-3 border mb-3'>
                        <h6 class='fw-bold mb-2'>Langkah Solusi Cepat:</h6>
                        <ol class='small mb-0 ps-3'>
                            <li>Pastikan service <strong>MySQL</strong> di Laragon / XAMPP dalam status <strong>Started / Running</strong>.</li>
                            <li>Periksa file <code>.env</code> di folder root proyek dan pastikan <code>DB_USERNAME</code> dan <code>DB_PASSWORD</code> sudah sesuai.</li>
                            <li>Buka phpMyAdmin dan pastikan hak akses user database sudah benar.</li>
                        </ol>
                    </div>
                    <div class='text-center'>
                        <button onclick='window.location.reload()' class='btn btn-primary fw-semibold px-4'>Muat Ulang Halaman</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ");
}

// Return instance PDO untuk fleksibilitas
return $pdo;
