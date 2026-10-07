<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Papan Pengumuman Publik Resmi (Clean Corporate Design)
 * ============================================================================
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Papan Pengumuman Gangguan Aliran Air";

// 1. Statistik Ringkas
$stat_aktif = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status != 'selesai'")->fetchColumn();
$stat_perbaikan = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'perbaikan'")->fetchColumn();
$stat_investigasi = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'investigasi'")->fetchColumn();
$stat_normalisasi = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'normalisasi'")->fetchColumn();
$stat_selesai = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'selesai'")->fetchColumn();

// 2. Daftar Kecamatan
$stmt_kecamatan = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
$daftar_kecamatan = $stmt_kecamatan->fetchAll();

// 3. Daftar Pengumuman Lengkap
$query = "
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan, u.nama_lengkap as nama_petugas
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    LEFT JOIN users u ON p.created_by = u.id
    ORDER BY 
        CASE 
            WHEN p.status = 'perbaikan' THEN 1
            WHEN p.status = 'investigasi' THEN 2
            WHEN p.status = 'normalisasi' THEN 3
            ELSE 4
        END,
        p.id DESC
";
$stmt_pengumuman = $pdo->query($query);
$pengumuman_list = $stmt_pengumuman->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Informasi Resmi -->
<section class="hero-corporate-banner py-4 py-md-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 mb-2 bg-light px-3 py-1 rounded-pill border">
                    <span class="pulse-indicator"></span>
                    <span class="small fw-bold text-dark">Layanan Informasi Operasional Air Bersih Garut</span>
                </div>
                <h1 class="h3 fw-bold text-dark mb-2 lh-sm" style="letter-spacing: -0.02em;">
                    Papan Informasi Pemeliharaan & Gangguan Pasokan Air
                </h1>
                <p class="text-muted small mb-0 pe-lg-4" style="line-height: 1.6;">
                    Portal resmi Perumda Air Minum Tirta Intan Garut untuk memantau status pemeliharaan jaringan transmisi pipa, estimasi waktu penyelesaian, dan penyaluran armada tangki air darurat.
                </p>
            </div>

            <!-- Status Counter Pills Grid -->
            <div class="col-lg-5">
                <div class="bg-light p-3 rounded-4 border">
                    <div class="small fw-bold text-uppercase text-muted mb-2 ps-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Status Penanganan Lapangan
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="stat-pill-chip danger">
                            <?= getIcon('tool') ?> <span>Dalam Perbaikan: <strong><?= (int)$stat_perbaikan ?></strong></span>
                        </div>
                        <div class="stat-pill-chip info">
                            <?= getIcon('clock') ?> <span>Normalisasi: <strong><?= (int)$stat_normalisasi ?></strong></span>
                        </div>
                        <?php if ($stat_investigasi > 0): ?>
                            <div class="stat-pill-chip" style="background: #fffbeb; border-color: #fde68a; color: #92400e;">
                                <?= getIcon('search') ?> <span>Investigasi: <strong><?= (int)$stat_investigasi ?></strong></span>
                            </div>
                        <?php endif; ?>
                        <div class="stat-pill-chip success">
                            <?= getIcon('check') ?> <span>Selesai: <strong><?= (int)$stat_selesai ?></strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Pencarian Cepat -->
<section class="container my-3 my-md-4">
    <div class="filter-search-card p-3 p-md-3">
        <div class="row g-2 align-items-center">
            <!-- Search Input -->
            <div class="col-12 col-md-5">
                <div class="input-group search-input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3"><?= getIcon('search') ?></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-2" placeholder="Cari nama jalan, perumahan, kelurahan, atau nomor tiket...">
                </div>
            </div>

            <!-- Select Kecamatan -->
            <div class="col-12 col-md-4">
                <select id="filterKecamatan" class="form-select form-select-corporate">
                    <option value="">Semua Wilayah Kecamatan (<?= count($daftar_kecamatan) ?>)</option>
                    <?php foreach ($daftar_kecamatan as $kec): ?>
                        <option value="<?= strtolower(clean($kec['nama_kecamatan'])) ?>">
                            Kecamatan <?= clean($kec['nama_kecamatan']) ?> (<?= clean($kec['cabang_pelayanan']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Status Filter Segmented Button -->
            <div class="col-12 col-md-3 text-md-end">
                <div class="btn-group w-100" role="group" aria-label="Filter Status">
                    <button type="button" class="btn btn-primary btn-sm tab-nav-btn active" data-tab="aktif">Aktif (<?= (int)$stat_aktif ?>)</button>
                    <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="selesai">Selesai (<?= (int)$stat_selesai ?>)</button>
                    <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="semua">Semua</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Pengumuman -->
<main class="container mb-5">
    <div class="d-flex flex-column gap-3" id="noticesContainer">
        <?php if (empty($pengumuman_list)): ?>
            <div class="card shadow-sm border text-center p-5 rounded-4 bg-white">
                <div class="text-success mb-2" style="font-size: 2.5rem;">
                    <?= getIcon('check') ?>
                </div>
                <h3 class="h5 fw-bold text-dark mb-1">Aliran Pasokan Air Berjalan Normal</h3>
                <p class="text-muted small mb-0">Saat ini tidak ada laporan pemeliharaan atau gangguan pipa di wilayah pelayanan Perumda Tirta Intan Garut.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pengumuman_list as $row): 
                $cardStatusClass = 'card-' . strtolower(clean($row['status']));
            ?>
                <article class="card bulletin-card <?= $cardStatusClass ?>"
                    data-title="<?= clean($row['judul']) ?>"
                    data-wilayah="<?= clean($row['wilayah_terdampak']) ?>"
                    data-kecamatan="<?= strtolower(clean($row['nama_kecamatan'])) ?>"
                    data-status="<?= strtolower(clean($row['status'])) ?>"
                    data-ticket="<?= clean($row['nomor_tiket']) ?>"
                >
                    <div class="card-body p-3 p-md-4">
                        <!-- Top Metadata Bar -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="ticket-tag">#<?= clean($row['nomor_tiket']) ?></span>
                                <?= renderStatusBadge($row['status']) ?>
                                <?= renderDampakBadge($row['dampak_aliran']) ?>
                            </div>
                            <div class="text-muted small d-flex align-items-center gap-1">
                                <?= getIcon('clock') ?> <span>Mulai Gangguan:</span> <strong class="text-dark"><?= formatTanggalIndo($row['waktu_mulai'], true) ?></strong>
                            </div>
                        </div>

                        <!-- Title & Service Branch Subtitle -->
                        <div class="mb-3">
                            <h2 class="bulletin-title mb-1"><?= clean($row['judul']) ?></h2>
                            <div class="text-muted small d-flex align-items-center gap-1 flex-wrap">
                                <span class="text-primary fw-semibold"><?= getIcon('pin') ?> Wilayah Pelayanan:</span>
                                <strong class="text-dark">Kecamatan <?= clean($row['nama_kecamatan']) ?></strong>
                                <span class="badge bg-light text-secondary border px-2 py-0" style="font-size: 0.72rem;"><?= clean($row['cabang_pelayanan']) ?></span>
                            </div>
                        </div>

                        <!-- Area Terdampak Callout -->
                        <div class="bulletin-area-box mb-3">
                            <strong><?= getIcon('pin') ?> Wilayah / Jalan Terdampak:</strong>
                            <div class="mt-1"><?= nl2br(clean($row['wilayah_terdampak'])) ?></div>
                        </div>

                        <!-- Grid: Penyebab & Tindakan -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="bulletin-mini-box cause h-100">
                                    <div class="bulletin-mini-label"><?= getIcon('warning') ?> Penyebab Gangguan</div>
                                    <div><?= clean($row['penyebab']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bulletin-mini-box action h-100">
                                    <div class="bulletin-mini-label"><?= getIcon('tool') ?> Tindakan Lapangan</div>
                                    <div><?= clean($row['tindakan']) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Estimasi & Actions -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                            <div class="info-block-eta <?= $row['status'] === 'selesai' ? 'selesai' : '' ?>">
                                <?= $row['status'] === 'selesai' ? getIcon('check') : getIcon('clock') ?>
                                <span><?= $row['status'] === 'selesai' ? 'Status Pekerjaan:' : 'Estimasi Normalisasi:' ?></span>
                                <strong>
                                    <?= $row['status'] === 'selesai' ? 'Pekerjaan Selesai (Aliran Air Normal)' : formatTanggalIndo($row['estimasi_selesai'], true) ?>
                                </strong>
                            </div>

                            <div class="d-flex gap-2 bulletin-action-btns">
                                <a href="detail.php?tiket=<?= urlencode($row['nomor_tiket']) ?>" class="btn btn-outline-primary btn-sm fw-semibold">
                                    Rincian Lengkap
                                </a>
                                <button type="button" class="btn btn-success btn-sm fw-semibold d-inline-flex align-items-center gap-1"
                                    onclick="shareToWA(
                                        '<?= clean($row['nomor_tiket']) ?>',
                                        '<?= addslashes(clean($row['judul'])) ?>',
                                        '<?= addslashes(clean($row['nama_kecamatan'])) ?>',
                                        '<?= addslashes(clean($row['wilayah_terdampak'])) ?>',
                                        '<?= addslashes(clean($row['status'])) ?>',
                                        '<?= addslashes(formatTanggalIndo($row['estimasi_selesai'], true)) ?>'
                                    )"
                                >
                                    <?= getIcon('whatsapp') ?> <span>Bagikan WA</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <!-- State Ketika Filter Kosong -->
            <div id="emptyState" class="card shadow-sm border text-center p-4 rounded-4 bg-white" style="display:none;">
                <div class="text-muted mb-2" style="font-size: 2rem;">
                    <?= getIcon('search') ?>
                </div>
                <h4 class="h6 fw-bold text-dark mb-1">Pengumuman Tidak Ditemukan</h4>
                <p class="text-muted small mb-0">Tidak ada pengumuman yang sesuai dengan kata kunci pencarian atau filter wilayah kecamatan yang dipilih.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
