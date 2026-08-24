<?php
$pageTitle = "Kelola Pengumuman";
require_once __DIR__ . '/../includes/admin_header.php';

// Filter parameters
$status_filter = $_GET['status'] ?? '';
$kecamatan_filter = $_GET['kecamatan_id'] ?? '';
$q = $_GET['q'] ?? '';

// Build Query
$sql = "
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan, u.nama_lengkap as nama_petugas
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE 1=1
";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND p.status = ?";
    $params[] = $status_filter;
}

if (!empty($kecamatan_filter)) {
    $sql .= " AND p.kecamatan_id = ?";
    $params[] = $kecamatan_filter;
}

if (!empty($q)) {
    $sql .= " AND (p.judul LIKE ? OR p.nomor_tiket LIKE ? OR p.wilayah_terdampak LIKE ? OR p.penyebab LIKE ?)";
    $searchTerm = "%{$q}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list_pengumuman = $stmt->fetchAll();

// Ambil list kecamatan untuk filter dropdown
$kecamatan_options = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC")->fetchAll();
?>

<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Daftar Seluruh Pengumuman</h2>
        <p class="text-muted small mb-0">Kelola info pemeliharaan pipa, update status teknis, dan estimasi waktu normalisasi.</p>
    </div>
    <a href="tambah.php" class="btn btn-primary fw-semibold shadow-sm">
        <?= getIcon('tool') ?> + Buat Pengumuman Baru
    </a>
</div>

<!-- Filter Box -->
<div class="card shadow-sm border mb-3">
    <div class="card-body p-3">
        <form method="GET" action="pengumuman.php" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari judul, nomor tiket, atau jalan..." value="<?= clean($q) ?>">
            </div>

            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="investigasi" <?= $status_filter === 'investigasi' ? 'selected' : '' ?>>Investigasi</option>
                    <option value="perbaikan" <?= $status_filter === 'perbaikan' ? 'selected' : '' ?>>Dalam Perbaikan</option>
                    <option value="normalisasi" <?= $status_filter === 'normalisasi' ? 'selected' : '' ?>>Normalisasi</option>
                    <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select name="kecamatan_id" class="form-select form-select-sm">
                    <option value="">Semua Kecamatan</option>
                    <?php foreach ($kecamatan_options as $kec): ?>
                        <option value="<?= $kec['id'] ?>" <?= $kecamatan_filter == $kec['id'] ? 'selected' : '' ?>>
                            Kec. <?= clean($kec['nama_kecamatan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-secondary btn-sm flex-fill">
                    <?= getIcon('search') ?> Filter
                </button>
                <?php if (!empty($q) || !empty($status_filter) || !empty($kecamatan_filter)): ?>
                    <a href="pengumuman.php" class="btn btn-outline-danger btn-sm" title="Reset Filter">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tiket</th>
                        <th style="min-width: 220px;">Perihal & Wilayah Garut</th>
                        <th style="min-width: 120px;">Dampak</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 140px;">Waktu & Estimasi</th>
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
                                        <strong>Kec. <?= clean($row['nama_kecamatan']) ?></strong> &bull; <?= clean(mb_strimwidth($row['wilayah_terdampak'], 0, 45, '...')) ?>
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
                                        <span class="fw-bold <?= $row['status'] === 'selesai' ? 'text-success' : 'text-danger' ?>">
                                            <?= $row['status'] === 'selesai' ? 'Selesai' : 'Est: ' . formatTanggalIndo($row['estimasi_selesai'], true) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="../detail.php?tiket=<?= urlencode($row['nomor_tiket']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm btn-action-touch" title="Pratinjau">
                                            Lihat
                                        </a>
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit">
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="pengumuman #<?= clean($row['nomor_tiket']) ?>" title="Hapus">
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
