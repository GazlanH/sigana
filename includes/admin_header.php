<?php
require_once __DIR__ . '/functions.php';
requireAuth();

$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' - ' : '' ?>Panel Admin SIGANA | Tirta Intan Garut</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="bg-light">

<!-- Navbar Utama Admin (Sleek, Rapi, Simetris di Desktop & Mobile) -->
<header class="admin-topbar bg-white border-bottom shadow-sm sticky-top">
    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center py-2">
            <!-- Brand Section -->
            <a class="d-flex align-items-center gap-2 text-decoration-none" href="index.php">
                <span class="brand-logo-circle" style="width:36px;height:36px;font-size:0.95rem;">TI</span>
                <div>
                    <div class="fw-bold text-dark fs-6 lh-1">SIGANA ADMIN</div>
                    <div class="small text-muted fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.02em;">PERUMDA TIRTA INTAN GARUT</div>
                </div>
            </a>

            <!-- Desktop Nav Menu (Tengah & Rapi) -->
            <nav class="d-none d-lg-flex align-items-center gap-1 admin-desktop-nav">
                <a href="index.php" class="admin-nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                    <?= getIcon('clock') ?> <span>Dashboard</span>
                </a>
                <a href="pengumuman.php" class="admin-nav-link <?= in_array($currentPage, ['pengumuman.php', 'edit.php']) ? 'active' : '' ?>">
                    <?= getIcon('warning') ?> <span>Data Pengumuman</span>
                </a>
                <a href="tambah.php" class="admin-nav-link <?= $currentPage === 'tambah.php' ? 'active' : '' ?>">
                    <?= getIcon('tool') ?> <span>Buat Pengumuman</span>
                </a>
                <a href="kecamatan.php" class="admin-nav-link <?= $currentPage === 'kecamatan.php' ? 'active' : '' ?>">
                    <?= getIcon('pin') ?> <span>Wilayah Kecamatan</span>
                </a>
            </nav>

            <!-- User Info & Right Action Buttons -->
            <div class="d-flex align-items-center gap-2">
                <div class="d-none d-sm-block text-end me-1">
                    <div class="fw-bold text-dark small lh-1"><?= clean($user['nama_lengkap']) ?></div>
                    <small class="text-muted" style="font-size: 0.72rem;"><?= clean($user['jabatan']) ?></small>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                    Lihat Web
                </a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm d-none d-sm-inline-flex align-items-center gap-1" onclick="return confirm('Keluar dari panel admin?')">
                    Keluar
                </a>

                <!-- Mobile Offcanvas / Toggler Button -->
                <button class="btn btn-light border d-lg-none p-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileDrawer" aria-controls="adminMobileDrawer" aria-label="Menu">
                    <span class="d-flex flex-column gap-1" style="width: 18px;">
                        <span style="height: 2px; background: #334155; border-radius: 2px;"></span>
                        <span style="height: 2px; background: #334155; border-radius: 2px;"></span>
                        <span style="height: 2px; background: #334155; border-radius: 2px;"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Drawer Menu Khusus Layar Mobile (Offcanvas Super Mulus & Rapi) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileDrawer" aria-labelledby="adminMobileDrawerLabel">
    <div class="offcanvas-header border-bottom bg-light py-3">
        <div class="d-flex align-items-center gap-2" id="adminMobileDrawerLabel">
            <span class="brand-logo-circle" style="width:34px;height:34px;font-size:0.9rem;">TI</span>
            <div>
                <div class="fw-bold text-dark fs-6 lh-1">SIGANA ADMIN</div>
                <small class="text-muted" style="font-size: 0.7rem;">PERUMDA TIRTA INTAN</small>
            </div>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
        <div>
            <div class="small text-muted text-uppercase fw-bold mb-2 ps-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Menu Navigasi</div>
            <div class="nav flex-column gap-1">
                <a href="index.php" class="admin-drawer-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                    <?= getIcon('clock') ?> <span>Dashboard</span>
                </a>
                <a href="pengumuman.php" class="admin-drawer-link <?= in_array($currentPage, ['pengumuman.php', 'edit.php']) ? 'active' : '' ?>">
                    <?= getIcon('warning') ?> <span>Data Pengumuman</span>
                </a>
                <a href="tambah.php" class="admin-drawer-link <?= $currentPage === 'tambah.php' ? 'active' : '' ?>">
                    <?= getIcon('tool') ?> <span>Buat Pengumuman</span>
                </a>
                <a href="kecamatan.php" class="admin-drawer-link <?= $currentPage === 'kecamatan.php' ? 'active' : '' ?>">
                    <?= getIcon('pin') ?> <span>Wilayah Kecamatan</span>
                </a>
            </div>
        </div>

        <div class="pt-3 border-top">
            <div class="p-2 bg-light rounded mb-3">
                <small class="text-muted d-block" style="font-size: 0.72rem;">Petugas Login:</small>
                <strong class="text-dark small"><?= clean($user['nama_lengkap']) ?></strong>
            </div>
            <div class="d-flex gap-2">
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm flex-fill">
                    Lihat Web
                </a>
                <a href="logout.php" class="btn btn-danger btn-sm flex-fill" onclick="return confirm('Keluar dari panel admin?')">
                    Keluar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Container Konten Admin -->
<main class="container-fluid px-3 px-md-4 py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : clean($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= clean($flash['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
