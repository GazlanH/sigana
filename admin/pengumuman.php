<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Kelola Seluruh Data Pengumuman (Pure Native PHP)
 * ============================================================================
 */

$pageTitle = "Data Seluruh Pengumuman";
require_once __DIR__ . '/../includes/admin_header.php';

// 1. Parameter Filter & Search
$status_filter = trim($_GET['status'] ?? '');
$kecamatan_filter = (int) ($_GET['kecamatan_id'] ?? 0);
$q = trim($_GET['q'] ?? '');

// Whitelist validasi status filter
$allowed_statuses = ['investigasi', 'perbaikan', 'normalisasi', 'selesai'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = '';
}

// 2. Konfigurasi Paginasi
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

// 3. Query Klausa Where Bersama
$whereClause = " WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $whereClause .= " AND p.status = ?";
    $params[] = $status_filter;
}

if ($kecamatan_filter > 0) {
    $whereClause .= " AND p.kecamatan_id = ?";
    $params[] = $kecamatan_filter;
}

if (!empty($q)) {
    $whereClause .= " AND (p.judul LIKE ? OR p.nomor_tiket LIKE ? OR p.wilayah_terdampak LIKE ? OR p.penyebab LIKE ?)";
    $searchTerm = "%{$q}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// 4. Hitung Total Data (untuk Kalkulasi Halaman)
$countSql = "SELECT COUNT(*) FROM pengumuman p" . $whereClause;
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// 5. Query Ambil Data Sesuai Halaman (LIMIT & OFFSET)
$sql = "
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan, u.nama_lengkap as nama_petugas
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    LEFT JOIN users u ON p.created_by = u.id
    " . $whereClause . "
    ORDER BY p.id DESC
    LIMIT " . (int) $perPage . " OFFSET " . (int) $offset . "
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list_pengumuman = $stmt->fetchAll();

// 6. Ambil opsi kecamatan untuk dropdown
$kecamatan_options = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC")->fetchAll();

// Helper untuk membuat URL query string dengan mempertahankan filter
function buildPaginationUrl($targetPage, $q, $status_filter, $kecamatan_filter) {
    $query = ['page' => $targetPage];
    if (!empty($q)) $query['q'] = $q;
    if (!empty($status_filter)) $query['status'] = $status_filter;
    if ($kecamatan_filter > 0) $query['kecamatan_id'] = $kecamatan_filter;
    return 'pengumuman.php?' . http_build_query($query);
}
?>

<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Manajemen Pengumuman Gangguan</h2>
        <p class="text-muted small mb-0">Kelola info pemeliharaan pipa, update kemajuan teknis, dan estimasi waktu
            normalisasi.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary fw-semibold shadow-sm d-inline-flex align-items-center gap-1">
        <?= getIcon('plus') ?> <span>Buat Pengumuman Baru</span>
    </a>
</div>

<!-- Filter & Search Box -->
<div class="card shadow-sm border mb-3">
    <div class="card-body p-3">
        <form method="GET" action="pengumuman.php" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                    placeholder="Cari judul, tiket, jalan, atau penyebab..." value="<?= clean($q) ?>">
            </div>

            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="investigasi" <?= $status_filter === 'investigasi' ? 'selected' : '' ?>>Investigasi
                    </option>
                    <option value="perbaikan" <?= $status_filter === 'perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan
                    </option>
                    <option value="normalisasi" <?= $status_filter === 'normalisasi' ? 'selected' : '' ?>>Normalisasi
                    </option>
                    <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select name="kecamatan_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kecamatan --</option>
                    <?php foreach ($kecamatan_options as $kec): ?>
                        <option value="<?= $kec['id'] ?>" <?= $kecamatan_filter == $kec['id'] ? 'selected' : '' ?>>
                            Kec. <?= clean($kec['nama_kecamatan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit"
                    class="btn btn-secondary btn-sm flex-fill d-inline-flex align-items-center justify-content-center gap-1">
                    <?= getIcon('search') ?> <span>Filter</span>
                </button>
                <?php if (!empty($q) || !empty($status_filter) || $kecamatan_filter > 0): ?>
                    <a href="pengumuman.php" class="btn btn-outline-danger btn-sm" title="Reset Filter">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="h6 fw-bold mb-0 text-dark">Daftar Pengumuman (Total: <?= $totalRows ?> data)</h3>
        <?php if (!empty($q) || !empty($status_filter) || $kecamatan_filter > 0): ?>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Filter Aktif</span>
        <?php endif; ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 120px;">Nomor Tiket</th>
                        <th style="min-width: 240px;">Perihal & Area Garut</th>
                        <th style="min-width: 120px;">Dampak Air</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 150px;">Waktu & Estimasi</th>
                        <th style="min-width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list_pengumuman)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Tidak ada data pengumuman yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list_pengumuman as $row): ?>
                            <tr>
                                <td>
                                    <span class="ticket-tag">#<?= clean($row['nomor_tiket']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= clean($row['judul']) ?></div>
                                    <div class="small text-muted">
                                        <strong>Kec. <?= clean($row['nama_kecamatan']) ?></strong> &bull;
                                        <?= clean(mb_strimwidth($row['wilayah_terdampak'], 0, 50, '...')) ?>
                                    </div>
                                </td>
                                <td>
                                    <?= renderDampakBadge($row['dampak_aliran']) ?>
                                </td>
                                <td>
                                    <?= renderStatusBadge($row['status']) ?>
                                </td>
                                <td>
                                    <div class="small">
                                        Mulai: <?= formatTanggalIndo($row['waktu_mulai'], false) ?><br>
                                        <span
                                            class="fw-bold <?= $row['status'] === 'selesai' ? 'text-success' : 'text-danger' ?>">
                                            <?= $row['status'] === 'selesai' ? 'Selesai' : 'Est: ' . formatTanggalIndo($row['estimasi_selesai'], true) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="../detail.php?tiket=<?= urlencode($row['nomor_tiket']) ?>" target="_blank"
                                            class="btn btn-outline-secondary btn-sm btn-action-touch"
                                            title="Lihat Pratinjau Publik">
                                            Lihat
                                        </a>
                                        <a href="edit.php?id=<?= $row['id'] ?>"
                                            class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit Rincian Data">
                                            Edit
                                        </a>
                                        <form action="hapus.php" method="POST" class="d-inline"
                                            onsubmit="return confirm('⚠️ Konfirmasi Penghapusan:\n\nApakah Anda yakin ingin menghapus pengumuman #<?= clean($row['nomor_tiket']) ?>?\nTindakan ini bersifat permanen dan tidak dapat dibatalkan.')">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-action-touch"
                                                title="Hapus Pengumuman">
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

    <!-- Pagination Footer -->
    <?php if ($totalRows > 0): ?>
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="small text-muted">
                Menampilkan <strong><?= $totalRows === 0 ? 0 : $offset + 1 ?></strong> - <strong><?= min($offset + $perPage, $totalRows) ?></strong> dari <strong><?= $totalRows ?></strong> pengumuman
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Navigasi Halaman Pengumuman">
                    <ul class="pagination pagination-sm mb-0">
                        <!-- Tombol Sebelumnya -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= buildPaginationUrl($page - 1, $q, $status_filter, $kecamatan_filter) ?>" aria-label="Sebelumnya">
                                &laquo;
                            </a>
                        </li>

                        <!-- Nomor Halaman -->
                        <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);

                            if ($startPage > 1) {
                                echo '<li class="page-item"><a class="page-link" href="' . buildPaginationUrl(1, $q, $status_filter, $kecamatan_filter) . '">1</a></li>';
                                if ($startPage > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                }
                            }

                            for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= buildPaginationUrl($i, $q, $status_filter, $kecamatan_filter) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php
                            if ($endPage < $totalPages) {
                                if ($endPage < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="' . buildPaginationUrl($totalPages, $q, $status_filter, $kecamatan_filter) . '">' . $totalPages . '</a></li>';
                            }
                        ?>

                        <!-- Tombol Selanjutnya -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= buildPaginationUrl($page + 1, $q, $status_filter, $kecamatan_filter) ?>" aria-label="Selanjutnya">
                                &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>