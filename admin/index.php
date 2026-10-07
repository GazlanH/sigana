<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Dashboard Utama Admin (Pure Native PHP)
 * ============================================================================
 */

$pageTitle = "Dashboard Operasional";
require_once __DIR__ . '/../includes/admin_header.php';

// 1. Ambil Statistik
$total_semua = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
$total_perbaikan = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'perbaikan'")->fetchColumn();
$total_investigasi = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'investigasi'")->fetchColumn();
$total_normalisasi = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'normalisasi'")->fetchColumn();
$total_selesai = (int)$pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'selesai'")->fetchColumn();
$total_kecamatan = (int)$pdo->query("SELECT COUNT(*) FROM kecamatan")->fetchColumn();

// 2. Ambil 5 Pengumuman Terbaru
$stmt_recent = $pdo->query("
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    ORDER BY p.id DESC
    LIMIT 6
");
$recent_list = $stmt_recent->fetchAll();
?>

<!-- Header Title & Quick Button -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Dashboard Operasional SIGANA</h2>
        <p class="text-muted small mb-0">Ringkasan status pemeliharaan jaringan pipa dan pengumuman gangguan air di Kabupaten Garut.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="tambah.php" class="btn btn-primary fw-semibold shadow-sm d-inline-flex align-items-center gap-1">
            <?= getIcon('plus') ?> <span>Buat Pengumuman Baru</span>
        </a>
        <a href="kecamatan.php" class="btn btn-outline-secondary fw-semibold d-inline-flex align-items-center gap-1">
            <?= getIcon('pin') ?> <span>Wilayah (<?= $total_kecamatan ?>)</span>
        </a>
    </div>
</div>

<!-- Metric Stat Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Total Pengumuman</div>
                <div class="h3 fw-bold text-dark mb-0 mt-1"><?= $total_semua ?></div>
                <small class="text-muted" style="font-size: 0.72rem;">Seluruh riwayat gangguan</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-danger h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Dalam Perbaikan</div>
                <div class="h3 fw-bold text-danger mb-0 mt-1"><?= $total_perbaikan ?></div>
                <small class="text-danger" style="font-size: 0.72rem;">Pekerjaan fisik aktif</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-warning h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Normalisasi / Investigasi</div>
                <div class="h3 fw-bold text-warning-emphasis mb-0 mt-1"><?= $total_normalisasi + $total_investigasi ?></div>
                <small class="text-muted" style="font-size: 0.72rem;">Pengisian pipa & survei</small>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-success h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Pekerjaan Selesai</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?= $total_selesai ?></div>
                <small class="text-success" style="font-size: 0.72rem;">Aliran air normal</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notices Card & Table -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h3 class="h6 fw-bold mb-0 text-dark"><?= getIcon('warning') ?> Pengumuman Gangguan Air Terbaru</h3>
        </div>
        <a href="pengumuman.php" class="btn btn-outline-primary btn-sm fw-semibold">
            Lihat Semua Pengumuman (<?= $total_semua ?>) &rarr;
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 120px;">Nomor Tiket</th>
                        <th style="min-width: 220px;">Perihal Gangguan</th>
                        <th style="min-width: 170px;">Wilayah Kecamatan</th>
                        <th style="min-width: 130px;">Waktu Mulai</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_list)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Belum ada data pengumuman yang tercatat. Silakan buat pengumuman baru.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_list as $item): ?>
                            <tr>
                                <td>
                                    <span class="ticket-tag">#<?= clean($item['nomor_tiket']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= clean($item['judul']) ?></div>
                                    <small class="text-muted"><?= clean(mb_strimwidth($item['penyebab'], 0, 55, '...')) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">Kec. <?= clean($item['nama_kecamatan']) ?></div>
                                    <small class="text-muted"><?= clean(mb_strimwidth($item['wilayah_terdampak'], 0, 40, '...')) ?></small>
                                </td>
                                <td>
                                    <?= formatTanggalIndo($item['waktu_mulai'], false) ?>
                                </td>
                                <td>
                                    <?= renderStatusBadge($item['status']) ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="../detail.php?tiket=<?= urlencode($item['nomor_tiket']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm btn-action-touch" title="Lihat Tampilan Publik">
                                            Lihat
                                        </a>
                                        <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit Data">
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="pengumuman #<?= clean($item['nomor_tiket']) ?>" title="Hapus Data">
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
