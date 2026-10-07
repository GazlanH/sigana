<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Header Publik Resmi (Clean Corporate Design)
 * ============================================================================
 */
require_once __DIR__ . '/functions.php';
$flash = getFlash();
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
    
    <!-- Custom Corporate Theme -->
    <link rel="stylesheet" href="assets/css/style.css?v=2.5">
</head>
<body class="bg-corporate d-flex flex-column min-vh-100">

    <!-- Top Utility Bar (Kontak Resmi & Jam Operasional) -->
    <div class="top-utility-bar py-1 bg-dark-navy text-white-50 border-bottom border-secondary-subtle">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2 small">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex align-items-center gap-1">
                    <span class="pulse-indicator"></span>
                    <span class="text-white fw-semibold">Pusat Informasi Operasional</span>
                </span>
                <span class="d-none d-md-inline text-muted">|</span>
                <span class="d-none d-md-inline text-white-50">Wilayah Pelayanan Kabupaten Garut</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="tel:0262232450" class="text-white-50 text-decoration-none hover-white d-inline-flex align-items-center gap-1">
                    <?= getIcon('phone') ?> <span>Call Center: <strong>(0262) 232450</strong></span>
                </a>
                <span class="text-muted d-none d-sm-inline">|</span>
                <a href="https://wa.me/6281123456789" target="_blank" class="text-success text-decoration-none hover-white d-inline-flex align-items-center gap-1 fw-semibold">
                    <?= getIcon('whatsapp') ?> <span>WA: 0811-2345-6789</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Clean Corporate) -->
    <header class="main-header bg-white border-bottom sticky-top shadow-xs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-2 py-md-3">
                <!-- Brand Identity -->
                <a href="index.php" class="brand-container d-flex align-items-center gap-2 gap-md-3 text-decoration-none">
                    <img src="assets/img/logo.png" alt="Logo Perumda Tirta Intan Garut" width="46" height="46" class="corporate-brand-logo">
                    <div class="brand-text">
                        <div class="brand-title">PERUMDA AIR MINUM TIRTA INTAN</div>
                        <div class="brand-subtitle">SIGANA &bull; Papan Pengumuman Gangguan Aliran Air</div>
                    </div>
                </a>

                <!-- Right Quick Navigation -->
                <div class="d-flex align-items-center gap-2">
                    <a href="index.php" class="btn btn-nav-pill btn-sm d-none d-md-inline-flex align-items-center gap-1">
                        <?= getIcon('clock') ?> <span>Papan Pengumuman</span>
                    </a>
                    <a href="admin/login.php" class="btn btn-portal-login btn-sm d-inline-flex align-items-center gap-1">
                        <?= getIcon('user') ?> <span>Portal Petugas</span>
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
