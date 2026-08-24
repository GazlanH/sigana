<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAuth();

$id = (int)($_GET['id'] ?? 0);

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
        setFlash('success', "Pengumuman #{$row['nomor_tiket']} berhasil dihapus dari sistem.");
    } else {
        setFlash('error', 'Data pengumuman tidak ditemukan.');
    }
} catch (PDOException $e) {
    setFlash('error', 'Gagal menghapus data: ' . $e->getMessage());
}

header('Location: pengumuman.php');
exit;
