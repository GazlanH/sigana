<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Halaman Rincian Pengumuman Publik (Clean Corporate Design)
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
        <div class='card shadow-sm border p-5 mx-auto rounded-4 bg-white' style='max-width: 500px;'>
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

<main class="container my-4">
    <!-- Breadcrumb & Top Action -->
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a href="index.php" class="btn btn-outline-secondary btn-sm fw-semibold d-inline-flex align-items-center gap-1">
            &larr; <span>Kembali ke Papan Pengumuman</span>
        </a>
        <div class="small text-muted">
            Dipublikasikan oleh: <strong><?= clean($detail['nama_petugas'] ?? 'Petugas Humas Tirta Intan') ?></strong>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="card shadow-sm border bulletin-card card-<?= $currentStatus ?> rounded-4 overflow-hidden mb-4">
        <!-- Top Info Header -->
        <div class="card-header bg-light p-3 p-md-4 border-bottom">
            <div class="d-flex gap-2 flex-wrap align-items-center mb-2">
                <span class="ticket-tag">#<?= clean($detail['nomor_tiket']) ?></span>
                <?= renderStatusBadge($detail['status']) ?>
                <?= renderDampakBadge($detail['dampak_aliran']) ?>
            </div>
            <h1 class="h4 fw-bold text-dark mb-1">
                <?= clean($detail['judul']) ?>
            </h1>
            <div class="small text-muted d-flex align-items-center gap-1 flex-wrap">
                <span class="text-primary fw-semibold"><?= getIcon('pin') ?> Wilayah Pelayanan:</span>
                <strong class="text-dark">Kecamatan <?= clean($detail['nama_kecamatan']) ?></strong>
                <span class="badge bg-secondary-subtle text-secondary-emphasis border px-2 py-0" style="font-size: 0.72rem;"><?= clean($detail['cabang_pelayanan']) ?></span>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Tahap Progress Penanganan (Interactive Visual Timeline) -->
            <div class="bg-light p-3 rounded-3 border mb-4">
                <div class="small fw-bold text-uppercase text-muted mb-2 text-center" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                    Progres Penanganan Gangguan Lapangan
                </div>
                <div class="timeline-progress-track">
                    <?php 
                    $stageLabels = [
                        'investigasi' => '1. Investigasi',
                        'perbaikan'   => '2. Perbaikan',
                        'normalisasi' => '3. Normalisasi',
                        'selesai'     => '4. Selesai'
                    ];
                    foreach ($stages as $idx => $stg): 
                        $isCompleted = ($idx < $currentIndex) || ($currentStatus === 'selesai');
                        $isActive = ($idx === $currentIndex) && ($currentStatus !== 'selesai');
                        $classState = $isCompleted ? 'completed' : ($isActive ? 'active' : '');
                    ?>
                        <div class="timeline-step <?= $classState ?>">
                            <div class="timeline-dot">
                                <?= $isCompleted ? '✓' : ($idx + 1) ?>
                            </div>
                            <div class="timeline-label"><?= $stageLabels[$stg] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Blok Wilayah (Area Terdampak) -->
            <div class="bulletin-area-box mb-3 p-3">
                <div class="fw-bold mb-1" style="color: #0369a1;"><?= getIcon('pin') ?> DAFTAR WILAYAH & JALAN TERDAMPAK:</div>
                <div class="text-dark" style="font-size: 0.92rem; line-height: 1.6;">
                    <?= nl2br(clean($detail['wilayah_terdampak'])) ?>
                </div>
            </div>

            <!-- Grid Penyebab & Tindakan -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="bulletin-mini-box cause p-3 h-100">
                        <div class="bulletin-mini-label mb-2"><?= getIcon('warning') ?> PENYEBAB GANGGUAN</div>
                        <div class="text-secondary"><?= nl2br(clean($detail['penyebab'])) ?></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bulletin-mini-box action p-3 h-100">
                        <div class="bulletin-mini-label mb-2"><?= getIcon('tool') ?> TINDAKAN LAPANGAN</div>
                        <div class="text-secondary"><?= nl2br(clean($detail['tindakan'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Baris Waktu & Posko Tangki -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <div class="bg-light border rounded-3 p-3 h-100">
                        <div class="small text-muted text-uppercase fw-bold mb-1"><?= getIcon('clock') ?> Waktu Mulai Gangguan</div>
                        <div class="fw-bold text-dark"><?= formatTanggalIndo($detail['waktu_mulai'], true) ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-block-eta <?= $detail['status'] === 'selesai' ? 'selesai' : '' ?> w-100 d-block p-3 h-100 rounded-3">
                        <div class="small text-uppercase fw-bold mb-1">
                            <?= $detail['status'] === 'selesai' ? getIcon('check') . ' Status Pekerjaan:' : getIcon('clock') . ' Estimasi Waktu Normal:' ?>
                        </div>
                        <div class="fw-bold fs-6">
                            <?= $detail['status'] === 'selesai' ? 'Pekerjaan Selesai (Aliran Air Normal)' : formatTanggalIndo($detail['estimasi_selesai'], true) ?>
                        </div>
                        <?php if ($detail['status'] === 'selesai' && !empty($detail['waktu_selesai_aktual'])): ?>
                            <small class="d-block mt-1 opacity-75">Tuntas pada: <?= formatTanggalIndo($detail['waktu_selesai_aktual'], true) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-success-subtle text-success-emphasis border border-success-subtle rounded-3 p-3 h-100">
                        <div class="small text-uppercase fw-bold mb-1"><?= getIcon('phone') ?> Layanan Armada Tangki Darurat</div>
                        <div class="fw-bold fs-6">
                            <a href="tel:<?= clean($detail['kontak_posko']) ?>" class="text-success-emphasis text-decoration-none d-inline-flex align-items-center gap-1">
                                <?= getIcon('phone') ?> <span><?= clean($detail['kontak_posko']) ?></span>
                            </a>
                        </div>
                        <small class="d-block text-success-emphasis opacity-75 mt-1">Siaga penyaluran air bersih darurat</small>
                    </div>
                </div>
            </div>

            <!-- Imbauan Pelanggan -->
            <div class="alert alert-warning py-2 px-3 small mb-4 rounded-3 border-warning-subtle">
                <strong>Imbauan Resmi Pelanggan:</strong> Pelanggan diimbau untuk menampung air bersih secukupnya saat aliran air kembali mengalir secara bertahap. Tim teknis berupaya semaksimal mungkin menuntaskan pemeliharaan pipa transmisi.
            </div>

            <!-- Tombol Aksi / Sharing -->
            <div class="d-flex gap-2 flex-wrap pt-3 border-top">
                <button type="button" class="btn btn-success fw-semibold d-inline-flex align-items-center gap-1"
                    onclick="shareToWA(
                        '<?= clean($detail['nomor_tiket']) ?>',
                        '<?= addslashes(clean($detail['judul'])) ?>',
                        '<?= addslashes(clean($detail['nama_kecamatan'])) ?>',
                        '<?= addslashes(clean($detail['wilayah_terdampak'])) ?>',
                        '<?= addslashes(clean($detail['status'])) ?>',
                        '<?= addslashes(formatTanggalIndo($detail['estimasi_selesai'], true)) ?>'
                    )"
                >
                    <?= getIcon('whatsapp') ?> <span>Bagikan ke WhatsApp</span>
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="window.print()">
                    Cetak Lembar Pengumuman
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="copyLink('<?= clean($detail['nomor_tiket']) ?>')">
                    Salin Tautan
                </button>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
