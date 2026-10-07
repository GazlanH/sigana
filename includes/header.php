<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Header Publik Resmi (Clean Modern & Less Clutter)
 * ============================================================================
 */
require_once __DIR__ . '/functions.php';
$flash = getFlash();
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' - ' : '' ?>SIGANA | Perumda Air Minum Tirta Intan Garut</title>
    <meta name="description" content="Papan Pengumuman Resmi Pemeliharaan Pipa dan Gangguan Aliran Air Bersih Perumda Tirta Intan Garut.">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Custom Modern Theme -->
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body class="bg-corporate d-flex flex-column min-vh-100">

    <!-- Main Navigation Header (Clean & Uncluttered) -->
    <header class="main-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <!-- Brand Logo & Title -->
                <a href="index.php" class="brand-container">
                    <img src="assets/img/logo.png" alt="Logo Perumda Tirta Intan Garut" width="42" height="42" class="corporate-brand-logo">
                    <div class="brand-text d-none d-sm-block">
                        <div class="brand-title">PERUMDA TIRTA INTAN</div>
                        <div class="brand-subtitle">SIGANA &bull; KABUPATEN GARUT</div>
                    </div>
                </a>

                <!-- Desktop Center Navigation Links -->
                <nav class="main-nav-links d-none d-md-flex">
                    <a href="index.php" class="main-nav-item <?= in_array($currentScript, ['index.php', 'detail.php']) ? 'active' : '' ?>">
                        Gangguan Layanan
                    </a>
                    <a href="index.php#daftarWilayah" class="main-nav-item" onclick="if(document.getElementById('filterKecamatan')){ document.getElementById('filterKecamatan').focus(); return false; }">
                        Wilayah Pelayanan
                    </a>
                    <a href="tel:0262232450" class="main-nav-item">
                        Call Center
                    </a>
                </nav>

                <!-- Right Action Buttons (Hubungi Posko & Login Petugas) -->
                <div class="d-flex align-items-center gap-3">
                    <a href="https://wa.me/6281123456789?text=Halo%20Admin%20Tirta%20Intan%20Garut,%20saya%20ingin%20menanyakan%20informasi%20gangguan%20air" target="_blank" class="btn-hubungi-kami">
                        <span>Hubungi Posko</span>
                        <?= getIcon('arrow-up-right') ?>
                    </a>
                    <a href="admin/login.php" class="btn-user-avatar" title="Portal Petugas / Login Admin" aria-label="Portal Petugas">
                        <?= getIcon('user') ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <?php if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : clean($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= clean($flash['text']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
