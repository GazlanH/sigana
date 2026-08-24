<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/../includes/admin_header.php';

// Ambil Statistik
$total_semua = $pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
$total_perbaikan = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'perbaikan'")->fetchColumn();
$total_investigasi = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'investigasi'")->fetchColumn();
$total_normalisasi = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'normalisasi'")->fetchColumn();
$total_selesai = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'selesai'")->fetchColumn();

// 5 Pengumuman Terbaru
$stmt_recent = $pdo->query("
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    ORDER BY p.id DESC
    LIMIT 5
");
$recent_list = $stmt_recent->fetchAll();
?>

<!-- Header Title & Quick Button -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Dashboard Operasional</h2>
        <p class="text-muted small mb-0">Ringkasan status pemeliharaan dan pengumuman gangguan air di Garut.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary fw-semibold shadow-sm">
        <?= getIcon('tool') ?> + Buat Pengumuman Baru
    </a>
</div>

<!-- Metric Stat Cards Grid (Mobile 2 Columns, Desktop 4 Columns) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Total Info</div>
                <div class="h3 fw-bold text-dark mb-0 mt-1"><?= $total_semua ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-danger h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Perbaikan</div>
                <div class="h3 fw-bold text-danger mb-0 mt-1"><?= $total_perbaikan ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-warning h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Investigasi / Normal</div>
                <div class="h3 fw-bold text-warning-emphasis mb-0 mt-1"><?= $total_normalisasi + $total_investigasi ?></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-success h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Selesai</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?= $total_selesai ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notices Card & Table -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="h6 fw-bold mb-0 text-dark">Pengumuman Gangguan Air Terbaru</h3>
        <a href="pengumuman.php" class="btn btn-outline-secondary btn-sm">
            Lihat Semua (<?= $total_semua ?>)
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tiket</th>
                        <th style="min-width: 200px;">Perihal Gangguan</th>
                        <th style="min-width: 160px;">Wilayah Garut</th>
                        <th style="min-width: 130px;">Waktu Mulai</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_list)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada data pengumuman yang tercatat.
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
                                    <small class="text-muted"><?= clean(mb_strimwidth($item['penyebab'], 0, 50, '...')) ?></small>
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
                                        <a href="../detail.php?tiket=<?= urlencode($item['nomor_tiket']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm btn-action-touch" title="Pratinjau">
                                            Lihat
                                        </a>
                                        <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit">
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="pengumuman #<?= clean($item['nomor_tiket']) ?>" title="Hapus">
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
