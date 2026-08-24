<?php
$pageTitle = "Master Wilayah Kecamatan";
require_once __DIR__ . '/../includes/admin_header.php';

$errors = [];

// Tambah Kecamatan Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $nama_kecamatan = trim($_POST['nama_kecamatan'] ?? '');
    $cabang_pelayanan = trim($_POST['cabang_pelayanan'] ?? '');

    if (empty($nama_kecamatan)) $errors[] = 'Nama kecamatan wajib diisi.';
    if (empty($cabang_pelayanan)) $errors[] = 'Nama cabang pelayanan wajib diisi.';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO kecamatan (nama_kecamatan, cabang_pelayanan) VALUES (?, ?)");
            $stmt->execute([$nama_kecamatan, $cabang_pelayanan]);
            setFlash('success', "Kecamatan {$nama_kecamatan} berhasil ditambahkan!");
            header('Location: kecamatan.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Kecamatan ini mungkin sudah terdaftar.";
        }
    }
}

// Hapus Kecamatan
if (isset($_GET['hapus'])) {
    $id_del = (int)$_GET['hapus'];
    try {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM pengumuman WHERE kecamatan_id = ?");
        $stmt_check->execute([$id_del]);
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            setFlash('error', "Kecamatan tidak dapat dihapus karena terdapat {$count} riwayat pengumuman.");
        } else {
            $stmt = $pdo->prepare("DELETE FROM kecamatan WHERE id = ?");
            $stmt->execute([$id_del]);
            setFlash('success', "Data kecamatan berhasil dihapus.");
        }
    } catch (PDOException $e) {
        setFlash('error', "Gagal menghapus kecamatan: " . $e->getMessage());
    }
    header('Location: kecamatan.php');
    exit;
}

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
        <p class="text-muted small mb-0">Daftar kecamatan di wilayah operasional Perumda Tirta Intan Garut.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= clean($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3 align-items-start mb-4">
    <!-- Form Tambah Kecamatan (Left Column) -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0 text-dark"><?= getIcon('pin') ?> Tambah Kecamatan Baru</h3>
            </div>
            <div class="card-body p-3">
                <form action="kecamatan.php" method="POST">
                    <input type="hidden" name="action" value="tambah">

                    <div class="mb-3">
                        <label class="form-label" for="nama_kecamatan">Nama Kecamatan <span class="req">*</span></label>
                        <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-control" placeholder="Contoh: Cisurupan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="cabang_pelayanan">Kantor Cabang Pelayanan <span class="req">*</span></label>
                        <input type="text" id="cabang_pelayanan" name="cabang_pelayanan" class="form-control" placeholder="Contoh: Cabang Bayongbong" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        Simpan Kecamatan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Kecamatan (Right Column) -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
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
                                <th class="text-end" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_kecamatan as $i => $item): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="fw-bold text-dark">Kec. <?= clean($item['nama_kecamatan']) ?></td>
                                    <td class="text-muted small"><?= clean($item['cabang_pelayanan']) ?></td>
                                    <td>
                                        <?php if ($item['gangguan_aktif'] > 0): ?>
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle"><?= $item['gangguan_aktif'] ?> Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aman (0)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="kecamatan.php?hapus=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="Kecamatan <?= clean($item['nama_kecamatan']) ?>" title="Hapus">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
