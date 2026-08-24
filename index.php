<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Papan Pengumuman Gangguan Air";

// Statistik Ringkas
$stat_aktif = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status != 'selesai'")->fetchColumn();
$stat_perbaikan = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'perbaikan'")->fetchColumn();
$stat_normalisasi = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'normalisasi'")->fetchColumn();
$stat_selesai = $pdo->query("SELECT COUNT(*) FROM pengumuman WHERE status = 'selesai'")->fetchColumn();

// Ambil daftar kecamatan
$stmt_kecamatan = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
$daftar_kecamatan = $stmt_kecamatan->fetchAll();

// Ambil pengumuman
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

<!-- Banner Judul Halaman -->
<div class="bg-white border-bottom py-3">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <h2 class="h5 fw-bold text-dark mb-1">Papan Pengumuman Gangguan Aliran Air</h2>
                <p class="text-muted small mb-0">Informasi resmi pemeliharaan pipa transmisi dan estimasi waktu normalisasi pasokan air pelanggan.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-wrap gap-2">
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-2">
                        <?= getIcon('tool') ?> Dalam Perbaikan: <strong><?= $stat_perbaikan ?></strong>
                    </span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-2">
                        <?= getIcon('clock') ?> Normalisasi: <strong><?= $stat_normalisasi ?></strong>
                    </span>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-2">
                        <?= getIcon('check') ?> Selesai: <strong><?= $stat_selesai ?></strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Pencarian Controls -->
<div class="container my-3">
    <div class="card shadow-sm border">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><?= getIcon('search') ?></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama jalan, perumahan, atau nomor tiket...">
                    </div>
                </div>

                <!-- Select Kecamatan -->
                <div class="col-md-4">
                    <select id="filterKecamatan" class="form-select">
                        <option value="">Semua Wilayah Kecamatan</option>
                        <?php foreach ($daftar_kecamatan as $kec): ?>
                            <option value="<?= strtolower(clean($kec['nama_kecamatan'])) ?>">
                                Kecamatan <?= clean($kec['nama_kecamatan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status Filter Pills -->
                <div class="col-md-3 text-md-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn active" data-tab="aktif">Aktif (<?= $stat_aktif ?>)</button>
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="selesai">Selesai (<?= $stat_selesai ?>)</button>
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="semua">Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Pengumuman -->
<main class="container mb-5">
    <div class="d-flex flex-column gap-3" id="noticesContainer">
        <?php if (empty($pengumuman_list)): ?>
            <div class="card shadow-sm border text-center p-5">
                <h3 class="h6 fw-bold text-dark mb-1">Aliran Air Berjalan Normal</h3>
                <p class="text-muted small mb-0">Saat ini tidak ada laporan gangguan pasokan air di wilayah pelayanan Perumda Tirta Intan Garut.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pengumuman_list as $row): 
                $cardStatusClass = 'card-' . strtolower($row['status']);
            ?>
                <article class="card shadow-sm bulletin-card <?= $cardStatusClass ?>"
                    data-title="<?= clean($row['judul']) ?>"
                    data-wilayah="<?= clean($row['wilayah_terdampak']) ?>"
                    data-kecamatan="<?= strtolower(clean($row['nama_kecamatan'])) ?>"
                    data-status="<?= strtolower(clean($row['status'])) ?>"
                    data-ticket="<?= clean($row['nomor_tiket']) ?>"
                >
                    <div class="card-body p-3 p-md-4">
                        <!-- Top Metadata -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2 small text-muted">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="ticket-tag">#<?= clean($row['nomor_tiket']) ?></span>
                                <?= renderStatusBadge($row['status']) ?>
                                <?= renderDampakBadge($row['dampak_aliran']) ?>
                            </div>
                            <div>
                                Mulai: <strong><?= formatTanggalIndo($row['waktu_mulai'], true) ?></strong>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="h5 fw-bold text-dark mb-3"><?= clean($row['judul']) ?></h3>

                        <!-- Wilayah Terdampak (Blue Block) -->
                        <div class="info-block-wilayah mb-3">
                            <div class="block-tag mb-1">
                                <?= getIcon('pin') ?> KECAMATAN <?= clean($row['nama_kecamatan']) ?> (<?= clean($row['cabang_pelayanan']) ?>)
                            </div>
                            <div class="text-dark small">
                                <strong>Area Terdampak:</strong> <?= nl2br(clean($row['wilayah_terdampak'])) ?>
                            </div>
                        </div>

                        <!-- Grid: Penyebab & Tindakan -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="info-block-cause h-100">
                                    <div class="block-tag mb-1"><?= getIcon('warning') ?> PENYEBAB GANGGUAN</div>
                                    <div class="small"><?= clean($row['penyebab']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-block-action h-100">
                                    <div class="block-tag mb-1"><?= getIcon('tool') ?> TINDAKAN LAPANGAN</div>
                                    <div class="small"><?= clean($row['tindakan']) ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer: Estimasi & Actions -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                            <div class="info-block-eta <?= $row['status'] === 'selesai' ? 'selesai' : '' ?>">
                                <span><?= $row['status'] === 'selesai' ? getIcon('check') . ' Status:' : getIcon('clock') . ' Estimasi Normal:' ?></span>
                                <strong>
                                    <?= $row['status'] === 'selesai' ? 'Pekerjaan Selesai (Aliran Normal)' : formatTanggalIndo($row['estimasi_selesai'], true) ?>
                                </strong>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="detail.php?tiket=<?= urlencode($row['nomor_tiket']) ?>" class="btn btn-outline-secondary btn-sm fw-semibold">
                                    Rincian Lengkap
                                </a>
                                <button type="button" class="btn btn-success btn-sm fw-semibold"
                                    onclick="shareToWA(
                                        '<?= clean($row['nomor_tiket']) ?>',
                                        '<?= addslashes(clean($row['judul'])) ?>',
                                        '<?= addslashes(clean($row['nama_kecamatan'])) ?>',
                                        '<?= addslashes(clean($row['wilayah_terdampak'])) ?>',
                                        '<?= addslashes(clean($row['status'])) ?>',
                                        '<?= addslashes(formatTanggalIndo($row['estimasi_selesai'], true)) ?>'
                                    )"
                                >
                                    <?= getIcon('whatsapp') ?> Bagikan WA
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

            <div id="emptyState" class="card shadow-sm border text-center p-4" style="display:none;">
                <h4 class="h6 fw-bold text-dark mb-1">Pengumuman Tidak Ditemukan</h4>
                <p class="text-muted small mb-0">Tidak ada pengumuman yang sesuai dengan kata kunci atau filter kecamatan yang dipilih.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
