<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Action Handler Hapus Pengumuman (Secured)
 * ============================================================================
 * SECURITY:
 * - Requires POST method (no GET delete)
 * - CSRF token validation
 * - Auth guard with session timeout
 * ============================================================================
 */

require_once __DIR__ . '/../includes/functions.php';

requireAuth();

// Hanya terima POST request — cegah CSRF via GET
requirePost('pengumuman.php');

// Validasi CSRF token
csrfGuard('pengumuman.php');

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    setFlash('error', 'ID Pengumuman tidak valid.');
    header('Location: pengumuman.php');
    exit;
}

try {
    $stmt_check = $pdo->prepare("SELECT nomor_tiket FROM pengumuman WHERE id = ? LIMIT 1");
    $stmt_check->execute([$id]);
    $row = $stmt_check->fetch();

    if ($row) {
        $stmt_del = $pdo->prepare("DELETE FROM pengumuman WHERE id = ?");
        $stmt_del->execute([$id]);
        setFlash('success', "Pengumuman tiket #{$row['nomor_tiket']} berhasil dihapus dari sistem.");
    } else {
        setFlash('error', 'Data pengumuman tidak ditemukan.');
    }
} catch (PDOException $e) {
    // Jangan bocorkan detail error ke user
    setFlash('error', 'Gagal menghapus data pengumuman. Silakan coba lagi.');
    // Log error secara internal jika diperlukan
    error_log('SIGANA hapus.php error: ' . $e->getMessage());
}

header('Location: pengumuman.php');
exit;
