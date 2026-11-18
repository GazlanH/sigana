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
$total_semua = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman")->fetchColumn();
$total_perbaikan = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'perbaikan'")->fetchColumn();
$total_investigasi = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'investigasi'")->fetchColumn();
$total_normalisasi = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'normalisasi'")->fetchColumn();
$total_selesai = (int) $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'selesai'")->fetchColumn();
$total_kecamatan = (int) $pdo->query("SELECT COUNT(*) FROM kecamatan")->fetchColumn();

// 2. Ambil 6 Pengumuman Terbaru
$stmt_recent = $pdo->query("
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    ORDER BY p.id DESC
    LIMIT 6
");
$recent_list = $stmt_recent->fetchAll();

// 3. Ringkasan Status Operasional per Cabang Pelayanan (OCC / Operations Control)
$stmt_cabang = $pdo->query("
    SELECT 
        k.cabang_pelayanan,
        GROUP_CONCAT(DISTINCT k.nama_kecamatan ORDER BY k.nama_kecamatan SEPARATOR ', ') as daftar_kecamatan,
        COUNT(DISTINCT k.id) as total_kecamatan,
        COUNT(p.id) as total_gangguan,
        SUM(CASE WHEN p.status = 'perbaikan' THEN 1 ELSE 0 END) as jml_perbaikan,
        SUM(CASE WHEN p.status IN ('investigasi', 'normalisasi') THEN 1 ELSE 0 END) as jml_siaga,
        SUM(CASE WHEN p.status != 'selesai' AND p.status IS NOT NULL THEN 1 ELSE 0 END) as gangguan_aktif
    FROM kecamatan k
    LEFT JOIN pengumuman p ON k.id = p.kecamatan_id
    GROUP BY k.cabang_pelayanan
    ORDER BY gangguan_aktif DESC, k.cabang_pelayanan ASC
");
$cabang_status = $stmt_cabang->fetchAll();
?>

<!-- Header Title & Quick Button -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Dashboard Operasional SIGANA</h2>
        <p class="text-muted small mb-0">Ringkasan status pemeliharaan jaringan pipa dan pengumuman gangguan air di
            Kabupaten Garut.</p>
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
                <div class="h3 fw-bold text-warning-emphasis mb-0 mt-1"><?= $total_normalisasi + $total_investigasi ?>
                </div>
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

<!-- Operations Control Center (OCC) - Status Cabang Pelayanan -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">OCC PERUMDA</span>
                <h3 class="h6 fw-bold mb-0 text-dark">Status Operasional Wilayah Cabang Pelayanan</h3>
            </div>
            <p class="text-muted small mb-0 mt-1">Pemantauan real-time kondisi distribusi aliran air per kantor cabang operasional.</p>
        </div>
        <div class="d-flex align-items-center gap-2 small">
            <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded-circle bg-success" style="width: 8px; height: 8px;"></span> Aman</span>
            <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded-circle bg-warning" style="width: 8px; height: 8px;"></span> Siaga/Normalisasi</span>
            <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded-circle bg-danger" style="width: 8px; height: 8px;"></span> Gangguan Fisik</span>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="row g-3">
            <?php foreach ($cabang_status as $cab): 
                $jmlAktif = (int) $cab['gangguan_aktif'];
                $jmlPerbaikan = (int) $cab['jml_perbaikan'];
                $jmlSiaga = (int) $cab['jml_siaga'];

                if ($jmlPerbaikan > 0) {
                    $themeClass = 'border-danger-subtle bg-danger-subtle text-danger-emphasis';
                    $indicatorClass = 'bg-danger';
                    $badgeText = $jmlPerbaikan . ' Titik Perbaikan';
                    $statusLabel = 'Gangguan Fisik';
                } elseif ($jmlSiaga > 0) {
                    $themeClass = 'border-warning-subtle bg-warning-subtle text-warning-emphasis';
                    $indicatorClass = 'bg-warning';
                    $badgeText = $jmlSiaga . ' Normalisasi/Survei';
                    $statusLabel = 'Tahap Normalisasi';
                } else {
                    $themeClass = 'border-success-subtle bg-success-subtle text-success-emphasis';
                    $indicatorClass = 'bg-success';
                    $badgeText = 'Layanan Normal';
                    $statusLabel = 'Aman & Terkendali';
                }
            ?>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card h-100 border p-3 rounded-3 bg-white hover-shadow-sm transition-all" style="box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <div>
                                <h4 class="h6 fw-bold mb-0 text-dark"><?= clean($cab['cabang_pelayanan']) ?></h4>
                                <small class="text-muted d-block text-truncate" style="max-width: 170px;" title="<?= clean($cab['daftar_kecamatan']) ?>">
                                    Kec. <?= clean($cab['daftar_kecamatan']) ?>
                                </small>
                            </div>
                            <span class="d-inline-block rounded-circle <?= $indicatorClass ?>" style="width: 10px; height: 10px; flex-shrink: 0;" title="<?= $statusLabel ?>"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="badge border <?= $themeClass ?>" style="font-size: 0.72rem; font-weight: 600;">
                                <?= $badgeText ?>
                            </span>
                            <a href="pengumuman.php?q=<?= urlencode($cab['cabang_pelayanan']) ?>" class="small text-decoration-none text-primary fw-semibold" style="font-size: 0.78rem;">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
                                    <small
                                        class="text-muted"><?= clean(mb_strimwidth($item['penyebab'], 0, 55, '...')) ?></small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">Kec. <?= clean($item['nama_kecamatan']) ?></div>
                                    <small
                                        class="text-muted"><?= clean(mb_strimwidth($item['wilayah_terdampak'], 0, 40, '...')) ?></small>
                                </td>
                                <td>
                                    <?= formatTanggalIndo($item['waktu_mulai'], false) ?>
                                </td>
                                <td>
                                    <?= renderStatusBadge($item['status']) ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="../detail.php?tiket=<?= urlencode($item['nomor_tiket']) ?>" target="_blank"
                                            class="btn btn-outline-secondary btn-sm btn-action-touch"
                                            title="Lihat Tampilan Publik">
                                            Lihat
                                        </a>
                                        <a href="edit.php?id=<?= $item['id'] ?>"
                                            class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit Data">
                                            Edit
                                        </a>
                                        <form action="hapus.php" method="POST" class="d-inline"
                                            onsubmit="return confirm('⚠️ Konfirmasi Penghapusan:\n\nApakah Anda yakin ingin menghapus pengumuman #<?= clean($item['nomor_tiket']) ?>?\nTindakan ini bersifat permanen dan tidak dapat dibatalkan.')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-action-touch"
                                                title="Hapus Data">
                                                Hapus
                                            </button>
                                        </form>
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