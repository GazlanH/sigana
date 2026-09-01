<?php
require_once __DIR__ . '/functions.php';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' - ' : '' ?>Papan Pengumuman Gangguan Air | Perumda Tirta Intan Garut</title>
    <meta name="description" content="Informasi gangguan dan pemeliharaan aliran air Perumda Tirta Intan Kabupaten Garut">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom Theme & Component Overlay -->
    <link rel="stylesheet" href="assets/css/style.css?v=1.2">
</head>
<body>

    <!-- Header Instansi Resmi -->
    <header class="bg-white border-bottom shadow-sm py-2">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <a href="index.php" class="d-flex align-items-center gap-3 text-decoration-none">
                    <img src="assets/img/logo.png" alt="Logo Perumda Tirta Intan Garut" width="44" height="44" style="width: 44px; height: 44px; max-width: 44px; max-height: 44px; object-fit: contain; flex-shrink: 0;" class="brand-logo-img">
                    <div>
                        <h1 class="h6 mb-0 fw-bold text-dark">PERUMDA AIR MINUM TIRTA INTAN</h1>
                        <p class="small text-muted mb-0 fw-semibold">KABUPATEN GARUT &bull; JAWA BARAT</p>
                    </div>
                </a>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-light text-dark border p-2 fw-medium">
                        <?= getIcon('phone') ?> Call Center: <strong>(0262) 232450</strong>
                    </span>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle p-2 fw-medium">
                        <?= getIcon('whatsapp') ?> Pengaduan WA: <strong>0811-2345-6789</strong>
                    </span>
                </div>
            </div>
        </div>
    </header>
