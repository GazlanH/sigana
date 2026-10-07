<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Papan Pengumuman Publik Resmi (Clean Modern & Less Clutter)
 * ============================================================================
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Informasi Gangguan Layanan";

// 1. Ambil Data Kecamatan untuk Filter Dropdown
$stmt_kecamatan = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
$daftar_kecamatan = $stmt_kecamatan->fetchAll();

// 2. Ambil Daftar Seluruh Pengumuman
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

<!-- 1. Hero Header Banner (Vibrant Curved Ocean Blue) -->
<section class="hero-header-banner">
    <div class="container">
        <h1 class="hero-title">Informasi Gangguan Layanan</h1>
        <div class="hero-breadcrumb">
            <a href="index.php">Home</a>
            <span>&rsaquo;</span>
            <span>Gangguan Layanan</span>
        </div>
    </div>
</section>

<!-- 2. Main Content Container -->
<main class="container mb-5">
    <!-- Floating Filter & Search Card -->
    <div class="filter-search-card">
        <div class="row g-3 align-items-end">
            <div class="col-lg-5 col-md-6">
                <label for="searchInput" class="filter-input-label">Info Gangguan</label>
                <input type="text" id="searchInput" class="filter-control-input" placeholder="Cari Informasi Gangguan..." autocomplete="off">
            </div>
            <div class="col-lg-4 col-md-6">
                <label for="filterKecamatan" class="filter-input-label">Kecamatan</label>
                <select id="filterKecamatan" class="filter-control-select">
                    <option value="">Pilih Kecamatan</option>
                    <?php foreach ($daftar_kecamatan as $kec): ?>
                        <option value="<?= strtolower(clean($kec['nama_kecamatan'])) ?>">
                            Kec. <?= clean($kec['nama_kecamatan']) ?> (<?= clean($kec['cabang_pelayanan']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-3 col-md-12">
                <button type="button" id="btnFilterSubmit" class="btn-filter-action">
                    <?= getIcon('filter') ?> <span>Filter</span>
                </button>
            </div>
        </div>

        <!-- Quick Status Filter Pills -->
        <div class="status-pill-tabs">
            <button type="button" class="status-pill-btn active" data-tab="semua">Semua Status</button>
            <button type="button" class="status-pill-btn" data-tab="perbaikan">Dalam Perbaikan</button>
            <button type="button" class="status-pill-btn" data-tab="investigasi">Investigasi</button>
            <button type="button" class="status-pill-btn" data-tab="normalisasi">Normalisasi</button>
            <button type="button" class="status-pill-btn" data-tab="selesai">Selesai</button>
        </div>
    </div>

    <!-- 3. Announcement Cards Grid (Clean 2 Columns) -->
    <div class="announcement-grid" id="announcementGrid">
        <?php if (!empty($pengumuman_list)): ?>
            <?php foreach ($pengumuman_list as $item): ?>
                <div class="pam-card bulletin-card"
                     data-title="<?= clean($item['judul']) ?>"
                     data-wilayah="<?= clean($item['wilayah_terdampak']) ?>"
                     data-kecamatan="<?= strtolower(clean($item['nama_kecamatan'])) ?>"
                     data-status="<?= strtolower(clean($item['status'])) ?>"
                     data-ticket="<?= clean($item['nomor_tiket']) ?>">
                    
                    <div>
                        <div class="pam-card-header">
                            <h2 class="pam-card-title">
                                <a href="detail.php?tiket=<?= urlencode($item['nomor_tiket']) ?>">
                                    <?= clean($item['judul']) ?>
                                </a>
                            </h2>
                            <div class="pam-badge-row">
                                <?= renderStatusBadge($item['status']) ?>
                                <?= renderDampakBadge($item['dampak_aliran']) ?>
                            </div>
                        </div>

                        <div class="pam-meta-list">
                            <!-- Schedule / Date Range -->
                            <div class="pam-meta-item">
                                <span class="meta-icon"><?= getIcon('calendar') ?></span>
                                <span>
                                    <?= formatTanggalIndo($item['waktu_mulai']) ?>
                                    <?php if (!empty($item['estimasi_selesai'])): ?>
                                        s/d <?= formatTanggalIndo($item['estimasi_selesai']) ?>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <!-- Location / Affected Areas -->
                            <div class="pam-meta-item location">
                                <span class="meta-icon"><?= getIcon('pin') ?></span>
                                <span>
                                    <strong>Kec. <?= clean($item['nama_kecamatan']) ?></strong> &bull; <?= clean($item['wilayah_terdampak']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pam-card-footer">
                        <a href="detail.php?tiket=<?= urlencode($item['nomor_tiket']) ?>" class="pam-link-more">
                            <span>Selengkapnya</span>
                            <?= getIcon('arrow-up-right') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Empty Search State -->
    <div id="emptyState" class="text-center py-5 my-4" style="display: <?= empty($pengumuman_list) ? 'block' : 'none' ?>;">
        <div class="card border-0 shadow-sm p-4 p-md-5 mx-auto rounded-4 bg-white" style="max-width: 520px;">
            <div class="text-primary mb-3" style="font-size: 2.5rem;"><?= getIcon('search') ?></div>
            <h3 class="h5 fw-bold text-dark mb-2">Tidak Ada Pengumuman Ditemukan</h3>
            <p class="text-muted small mb-3">Tidak ada data gangguan yang sesuai dengan kriteria pencarian atau filter yang dipilih.</p>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="window.resetFilters()">
                    Reset Pencarian
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Clean Pagination Buttons -->
    <div class="pam-pagination" id="pamPagination">
        <button type="button" class="pam-page-btn" id="prevPageBtn" aria-label="Halaman Sebelumnya">&lsaquo;</button>
        <div id="pageNumberContainer" class="d-inline-flex gap-2">
            <!-- Dynamically rendered by JS -->
        </div>
        <button type="button" class="pam-page-btn" id="nextPageBtn" aria-label="Halaman Berikutnya">&rsaquo;</button>
    </div>

    <!-- 5. Bottom Callout Banner (Engaging & Clean) -->
    <div class="pam-cta-banner">
        <h3>Dapatkan Layanan Air Minum Sekarang!</h3>
        <p>Laporkan gangguan aliran air atau ajukan permohonan bantuan armada tangki air darurat ke Perumda Air Minum Tirta Intan Garut.</p>
        <a href="https://wa.me/6281123456789?text=Halo%20Perumda%20Tirta%20Intan,%20saya%20ingin%20lapor%20gangguan%20air" target="_blank" class="btn-cta-action">
            <span>Hubungi Posko Pelayanan</span>
            <?= getIcon('arrow-up-right') ?>
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
