<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Halaman Rincian Pengumuman Publik (Clean Modern & Structured)
 * ============================================================================
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$nomor_tiket = trim($_GET['tiket'] ?? '');

if (empty($nomor_tiket)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan, u.nama_lengkap as nama_petugas, u.jabatan as jabatan_petugas
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.nomor_tiket = ?
    LIMIT 1
");
$stmt->execute([$nomor_tiket]);
$detail = $stmt->fetch();

if (!$detail) {
    $pageTitle = "Pengumuman Tidak Ditemukan";
    require_once __DIR__ . '/includes/header.php';
    echo "
    <main class='container my-5 text-center'>
        <div class='card border-0 shadow-sm p-5 mx-auto rounded-4 bg-white' style='max-width: 500px;'>
            <div class='text-warning mb-3' style='font-size: 2.5rem;'>" . getIcon('warning') . "</div>
            <h3 class='h5 fw-bold text-dark mb-2'>Pengumuman Tidak Ditemukan</h3>
            <p class='text-muted small mb-4'>Nomor tiket <strong>#" . clean($nomor_tiket) . "</strong> tidak terdaftar dalam sistem informasi SIGANA.</p>
            <a href='index.php' class='btn btn-primary fw-semibold'>&larr; Kembali ke Papan Pengumuman</a>
        </div>
    </main>
    ";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = "Rincian Tiket #" . $detail['nomor_tiket'];
require_once __DIR__ . '/includes/header.php';

// Menentukan tahap progress timeline
$currentStatus = strtolower(clean($detail['status']));
$stages = ['investigasi', 'perbaikan', 'normalisasi', 'selesai'];
$currentIndex = array_search($currentStatus, $stages);
if ($currentIndex === false) $currentIndex = 1;
?>

<!-- Hero Header Banner -->
<section class="hero-header-banner">
    <div class="container">
        <h1 class="hero-title">Rincian Gangguan Layanan</h1>
        <div class="hero-breadcrumb">
            <a href="index.php">Home</a>
            <span>&rsaquo;</span>
            <a href="index.php">Gangguan Layanan</a>
            <span>&rsaquo;</span>
            <span>#<?= clean($detail['nomor_tiket']) ?></span>
        </div>
    </div>
</section>

<!-- Main Detail Content -->
<main class="container my-4 mb-5">
    <!-- Back button & Share Bar -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <a href="index.php" class="btn btn-outline-secondary btn-sm fw-semibold d-inline-flex align-items-center gap-1">
            &larr; <span>Kembali ke Daftar Pengumuman</span>
        </a>
        <div class="small text-muted">
            Tiket <strong>#<?= clean($detail['nomor_tiket']) ?></strong> &bull; Publikasi: <?= formatTanggalIndo($detail['created_at'] ?? $detail['waktu_mulai']) ?>
        </div>
    </div>

    <!-- Clean White Detail Card -->
    <div class="detail-card">
        <!-- Top Title & Badges -->
        <div class="mb-4">
            <div class="pam-badge-row mb-2">
                <?= renderStatusBadge($detail['status']) ?>
                <?= renderDampakBadge($detail['dampak_aliran']) ?>
                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-1" style="font-size: 0.75rem;">
                    Kec. <?= clean($detail['nama_kecamatan']) ?> (<?= clean($detail['cabang_pelayanan']) ?>)
                </span>
            </div>
            <h1 class="h3 fw-bold text-dark mb-2" style="letter-spacing: -0.01em; line-height: 1.35;">
                <?= clean($detail['judul']) ?>
            </h1>
        </div>

        <!-- Progres Penanganan (Interactive Timeline) -->
        <div class="bg-light p-3 p-md-4 rounded-4 border mb-4">
            <div class="small fw-bold text-uppercase text-muted mb-3 text-center" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                Tahapan Progres Penanganan Lapangan
            </div>
            <div class="row g-2 text-center">
                <?php 
                $stageLabels = [
                    'investigasi' => '1. Investigasi & Survei',
                    'perbaikan'   => '2. Pekerjaan Fisik',
                    'normalisasi' => '3. Normalisasi Aliran',
                    'selesai'     => '4. Aliran Air Normal'
                ];
                foreach ($stages as $idx => $stg): 
                    $isCompleted = ($idx < $currentIndex) || ($currentStatus === 'selesai');
                    $isActive = ($idx === $currentIndex) && ($currentStatus !== 'selesai');
                ?>
                    <div class="col-6 col-md-3">
                        <div class="p-2 rounded-3 h-100 <?= $isActive ? 'bg-primary text-white shadow-sm' : ($isCompleted ? 'bg-success-subtle text-success-emphasis' : 'bg-white text-muted border') ?>">
                            <div class="fw-bold small"><?= $isCompleted ? '✓ ' : '' ?><?= $stageLabels[$stg] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 1. Detail Wilayah Terdampak -->
        <div class="detail-box mb-4">
            <div class="detail-box-header">
                <span class="detail-box-icon text-primary"><?= getIcon('pin') ?></span>
                <span class="detail-box-title">Wilayah & Kawasan Terdampak</span>
            </div>
            <div class="detail-box-body">
                <div class="fw-semibold text-dark fs-6" style="line-height: 1.6;">
                    <?= nl2br(clean($detail['wilayah_terdampak'])) ?>
                </div>
            </div>
        </div>

        <!-- 2. Grid Penyebab & Tindakan -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 d-flex">
                <div class="detail-box flex-fill w-100">
                    <div class="detail-box-header">
                        <span class="detail-box-icon text-warning"><?= getIcon('warning') ?></span>
                        <span class="detail-box-title">Penyebab Gangguan</span>
                    </div>
                    <div class="detail-box-body text-secondary" style="line-height: 1.6;">
                        <?= nl2br(clean($detail['penyebab'])) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex">
                <div class="detail-box flex-fill w-100">
                    <div class="detail-box-header">
                        <span class="detail-box-icon text-info"><?= getIcon('tool') ?></span>
                        <span class="detail-box-title">Langkah Tindakan Teknis</span>
                    </div>
                    <div class="detail-box-body text-secondary" style="line-height: 1.6;">
                        <?= nl2br(clean($detail['tindakan'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Schedule & Emergency Contacts Box -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 d-flex">
                <div class="detail-box flex-fill w-100">
                    <div class="small text-muted text-uppercase fw-bold mb-1 d-flex align-items-center gap-1">
                        <?= getIcon('calendar') ?> Waktu Mulai Kejadian
                    </div>
                    <div class="fw-bold text-dark fs-6 mt-2"><?= formatTanggalIndo($detail['waktu_mulai'], true) ?></div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="detail-box flex-fill w-100 <?= $detail['status'] === 'selesai' ? 'bg-success-subtle border-success-subtle text-success-emphasis' : '' ?>">
                    <div class="small text-uppercase fw-bold mb-1 d-flex align-items-center gap-1">
                        <?= $detail['status'] === 'selesai' ? getIcon('check') . ' Status Pekerjaan:' : getIcon('clock') . ' Estimasi Normalisasi:' ?>
                    </div>
                    <div class="fw-bold fs-6 mt-2">
                        <?= $detail['status'] === 'selesai' ? 'Pekerjaan Selesai (Aliran Normal)' : formatTanggalIndo($detail['estimasi_selesai'], true) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4 d-flex">
                <div class="detail-box flex-fill w-100 bg-primary-subtle border-primary-subtle">
                    <div class="small text-uppercase fw-bold text-primary-emphasis mb-1 d-flex align-items-center gap-1">
                        <?= getIcon('phone') ?> Armada Tangki Air Siaga
                    </div>
                    <div class="fw-bold fs-6 mt-2">
                        <a href="tel:<?= clean($detail['kontak_posko']) ?>" class="text-primary fw-bold text-decoration-none d-inline-flex align-items-center gap-1">
                            <?= getIcon('phone') ?> <span><?= clean($detail['kontak_posko']) ?></span>
                        </a>
                    </div>
                    <small class="text-muted d-block mt-1">Layanan darurat bebas biaya</small>
                </div>
            </div>
        </div>

        <!-- Action / Sharing Buttons -->
        <div class="d-flex gap-2 flex-wrap pt-3 border-top justify-content-between align-items-center">
            <div class="d-flex gap-2 flex-wrap">
                <a href="https://wa.me/?text=<?= urlencode("📢 *INFORMASI GANGGUAN AIR BERSIH PERUMDA TIRTA INTAN GARUT*\n\nTiket: #" . $detail['nomor_tiket'] . "\nPerihal: " . $detail['judul'] . "\nWilayah: " . $detail['nama_kecamatan'] . " - " . $detail['wilayah_terdampak'] . "\nStatus: " . ucfirst($detail['status']) . "\n\nInformasi lengkap: " . "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                   target="_blank" class="btn btn-success fw-semibold d-inline-flex align-items-center gap-1">
                    <?= getIcon('whatsapp') ?> <span>Bagikan ke WhatsApp</span>
                </a>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="window.print()">
                    Cetak Lembar Pengumuman
                </button>
            </div>
            <div class="small text-muted">
                Petugas: <strong><?= clean($detail['nama_petugas'] ?? 'Petugas Tirta Intan') ?></strong> (<?= clean($detail['jabatan_petugas'] ?? 'Humas & Teknis') ?>)
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
