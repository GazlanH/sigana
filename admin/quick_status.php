<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Action Handler: Quick Update Status Pengumuman (Secured)
 * ============================================================================
 */

require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Hanya terima method POST
requirePost('pengumuman.php');

// Validasi CSRF
csrfGuard('pengumuman.php');

$id = (int) ($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$redirect = trim($_POST['redirect'] ?? 'pengumuman.php');

// Whitelist status valid
$allowed_statuses = ['investigasi', 'perbaikan', 'normalisasi', 'selesai'];

if ($id <= 0 || !in_array($status, $allowed_statuses)) {
    setFlash('error', 'Parameter perubahan status tidak valid.');
    header('Location: ' . $redirect);
    exit;
}

try {
    // Ambil data tiket saat ini
    $stmt_check = $pdo->prepare("SELECT nomor_tiket, status FROM pengumuman WHERE id = ? LIMIT 1");
    $stmt_check->execute([$id]);
    $current = $stmt_check->fetch();

    if (!$current) {
        setFlash('error', 'Data pengumuman tidak ditemukan.');
        header('Location: ' . $redirect);
        exit;
    }

    if ($current['status'] === $status) {
        setFlash('info', "Pengumuman #{$current['nomor_tiket']} sudah berstatus " . ucfirst($status) . ".");
        header('Location: ' . $redirect);
        exit;
    }

    // Update status & waktu_selesai_aktual
    $update_sql = "
        UPDATE pengumuman SET
            status = ?,
            waktu_selesai_aktual = CASE 
                WHEN ? = 'selesai' AND waktu_selesai_aktual IS NULL THEN NOW() 
                WHEN ? != 'selesai' THEN NULL
                ELSE waktu_selesai_aktual 
            END
        WHERE id = ?
    ";
    $stmt_up = $pdo->prepare($update_sql);
    $stmt_up->execute([$status, $status, $status, $id]);

    $statusLabels = [
        'investigasi' => 'Investigasi',
        'perbaikan'   => 'Dalam Perbaikan',
        'normalisasi' => 'Tahap Normalisasi Aliran',
        'selesai'     => 'Selesai'
    ];
    $labelBaru = $statusLabels[$status] ?? ucfirst($status);

    setFlash('success', "Status tiket #{$current['nomor_tiket']} berhasil diperbarui menjadi: {$labelBaru}!");
} catch (PDOException $e) {
    error_log('SIGANA quick-status error: ' . $e->getMessage());
    setFlash('error', 'Gagal memperbarui status pengumuman. Terjadi kesalahan pada database.');
}

header('Location: ' . $redirect);
exit;
