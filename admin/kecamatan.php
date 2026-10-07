<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Kelola Master Wilayah Kecamatan & Cabang Pelayanan (Pure Native PHP)
 * ============================================================================
 */

$pageTitle = "Master Wilayah Kecamatan";
require_once __DIR__ . '/../includes/admin_header.php';

$errors = [];
$edit_id = (int)($_GET['edit'] ?? 0);
$edit_data = null;

// Jika mode edit, ambil data kecamatan yang akan diedit
if ($edit_id > 0) {
    $stmt_e = $pdo->prepare("SELECT * FROM kecamatan WHERE id = ? LIMIT 1");
    $stmt_e->execute([$edit_id]);
    $edit_data = $stmt_e->fetch();
}

// 1. Tambah atau Update Kecamatan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nama_kecamatan = trim($_POST['nama_kecamatan'] ?? '');
    $cabang_pelayanan = trim($_POST['cabang_pelayanan'] ?? '');

    if (empty($nama_kecamatan)) $errors[] = 'Nama kecamatan wajib diisi.';
    if (empty($cabang_pelayanan)) $errors[] = 'Nama kantor cabang pelayanan wajib diisi.';

    if (empty($errors)) {
        if ($action === 'tambah') {
            try {
                $stmt = $pdo->prepare("INSERT INTO kecamatan (nama_kecamatan, cabang_pelayanan) VALUES (?, ?)");
                $stmt->execute([$nama_kecamatan, $cabang_pelayanan]);
                setFlash('success', "Kecamatan {$nama_kecamatan} berhasil ditambahkan!");
                header('Location: kecamatan.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = "Kecamatan '{$nama_kecamatan}' mungkin sudah terdaftar.";
            }
        } elseif ($action === 'edit') {
            $id_update = (int)($_POST['id'] ?? 0);
            if ($id_update > 0) {
                try {
                    $stmt = $pdo->prepare("UPDATE kecamatan SET nama_kecamatan = ?, cabang_pelayanan = ? WHERE id = ?");
                    $stmt->execute([$nama_kecamatan, $cabang_pelayanan, $id_update]);
                    setFlash('success', "Data kecamatan {$nama_kecamatan} berhasil diperbarui!");
                    header('Location: kecamatan.php');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = "Gagal memperbarui data kecamatan: " . $e->getMessage();
                }
            }
        }
    }
}

// 2. Hapus Kecamatan
if (isset($_GET['hapus'])) {
    $id_del = (int)$_GET['hapus'];
    try {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM pengumuman WHERE kecamatan_id = ?");
        $stmt_check->execute([$id_del]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            setFlash('error', "Kecamatan tidak dapat dihapus karena masih terkait dengan {$count} riwayat pengumuman.");
        } else {
            $stmt = $pdo->prepare("DELETE FROM kecamatan WHERE id = ?");
            $stmt->execute([$id_del]);
            setFlash('success', "Data kecamatan berhasil dihapus dari sistem.");
        }
    } catch (PDOException $e) {
        setFlash('error', "Gagal menghapus kecamatan: " . $e->getMessage());
    }
    header('Location: kecamatan.php');
    exit;
}

// 3. Ambil Daftar Seluruh Kecamatan Beserta Statistik Pengumuman
$sql_kec = "
    SELECT k.*, 
        COUNT(p.id) as total_pengumuman,
        SUM(CASE WHEN p.status != 'selesai' THEN 1 ELSE 0 END) as gangguan_aktif
    FROM kecamatan k
    LEFT JOIN pengumuman p ON k.id = p.kecamatan_id
    GROUP BY k.id
    ORDER BY k.nama_kecamatan ASC
";
$list_kecamatan = $pdo->query($sql_kec)->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Kelola Wilayah & Cabang Pelayanan</h2>
        <p class="text-muted small mb-0">Daftar kecamatan dan kantor cabang operasional Perumda Air Minum Tirta Intan Garut.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm mb-3">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= clean($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3 align-items-start mb-4">
    <!-- Form Tambah / Edit Kecamatan (Left Column) -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0 text-dark">
                    <?= $edit_data ? getIcon('edit') . ' Edit Kecamatan' : getIcon('plus') . ' Tambah Kecamatan Baru' ?>
                </h3>
            </div>
            <div class="card-body p-3">
                <form action="kecamatan.php" method="POST">
                    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" for="nama_kecamatan">Nama Kecamatan <span class="req">*</span></label>
                        <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-control" placeholder="Contoh: Cisurupan" value="<?= $edit_data ? clean($edit_data['nama_kecamatan']) : '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="cabang_pelayanan">Kantor Cabang Pelayanan <span class="req">*</span></label>
                        <input type="text" id="cabang_pelayanan" name="cabang_pelayanan" class="form-control" placeholder="Contoh: Cabang Bayongbong" value="<?= $edit_data ? clean($edit_data['cabang_pelayanan']) : '' ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <?php if ($edit_data): ?>
                            <a href="kecamatan.php" class="btn btn-secondary flex-fill">Batal</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary fw-semibold flex-fill d-inline-flex align-items-center justify-content-center gap-1">
                            <?= getIcon('check') ?> <span><?= $edit_data ? 'Simpan Perubahan' : 'Simpan Kecamatan' ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Kecamatan (Right Column) -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="h6 fw-bold mb-0 text-dark">Daftar Kecamatan Pelayanan (<?= count($list_kecamatan) ?>)</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Kecamatan</th>
                                <th>Cabang Pelayanan</th>
                                <th>Status Gangguan</th>
                                <th class="text-end" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($list_kecamatan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data kecamatan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_kecamatan as $i => $item): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td class="fw-bold text-dark">Kec. <?= clean($item['nama_kecamatan']) ?></td>
                                        <td class="text-muted small"><?= clean($item['cabang_pelayanan']) ?></td>
                                        <td>
                                            <?php if ($item['gangguan_aktif'] > 0): ?>
                                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle"><?= (int)$item['gangguan_aktif'] ?> Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aman (0)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <a href="kecamatan.php?edit=<?= $item['id'] ?>" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit Data">
                                                    Edit
                                                </a>
                                                <a href="kecamatan.php?hapus=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="Kecamatan <?= clean($item['nama_kecamatan']) ?>" title="Hapus Data">
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
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
