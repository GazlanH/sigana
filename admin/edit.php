<?php
$pageTitle = "Edit Pengumuman";
require_once __DIR__ . '/../includes/admin_header.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('error', 'ID Pengumuman tidak valid.');
    header('Location: pengumuman.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    setFlash('error', 'Data pengumuman tidak ditemukan.');
    header('Location: pengumuman.php');
    exit;
}

$list_kecamatan = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $kecamatan_id = (int)($_POST['kecamatan_id'] ?? 0);
    $wilayah_terdampak = trim($_POST['wilayah_terdampak'] ?? '');
    $penyebab = trim($_POST['penyebab'] ?? '');
    $tindakan = trim($_POST['tindakan'] ?? '');
    $dampak_aliran = trim($_POST['dampak_aliran'] ?? 'mati_total');
    $status = trim($_POST['status'] ?? 'perbaikan');
    $waktu_mulai = trim($_POST['waktu_mulai'] ?? '');
    $estimasi_selesai = trim($_POST['estimasi_selesai'] ?? '');
    $kontak_posko = trim($_POST['kontak_posko'] ?? '0811-2345-6789');

    if (empty($judul)) $errors[] = 'Judul perihal gangguan wajib diisi.';
    if ($kecamatan_id <= 0) $errors[] = 'Pilih kecamatan wilayah terdampak.';
    if (empty($wilayah_terdampak)) $errors[] = 'Rincian wilayah terdampak wajib diisi.';
    if (empty($penyebab)) $errors[] = 'Penyebab gangguan wajib diisi.';
    if (empty($tindakan)) $errors[] = 'Langkah perbaikan wajib diisi.';
    if (empty($waktu_mulai)) $errors[] = 'Waktu mulai kejadian wajib ditentukan.';

    if (empty($errors)) {
        try {
            $update_sql = "
                UPDATE pengumuman SET
                    judul = ?,
                    kecamatan_id = ?,
                    wilayah_terdampak = ?,
                    penyebab = ?,
                    tindakan = ?,
                    dampak_aliran = ?,
                    status = ?,
                    waktu_mulai = ?,
                    estimasi_selesai = ?,
                    kontak_posko = ?,
                    waktu_selesai_aktual = CASE WHEN ? = 'selesai' AND waktu_selesai_aktual IS NULL THEN NOW() ELSE waktu_selesai_aktual END
                WHERE id = ?
            ";
            $stmt_up = $pdo->prepare($update_sql);
            $stmt_up->execute([
                $judul,
                $kecamatan_id,
                $wilayah_terdampak,
                $penyebab,
                $tindakan,
                $dampak_aliran,
                $status,
                $waktu_mulai,
                !empty($estimasi_selesai) ? $estimasi_selesai : null,
                $kontak_posko,
                $status,
                $id
            ]);

            setFlash('success', "Pengumuman #{$data['nomor_tiket']} berhasil diperbarui!");
            header('Location: pengumuman.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Edit Tiket #<?= clean($data['nomor_tiket']) ?></h2>
        <p class="text-muted small mb-0">Perbarui rincian teknis dan status kemajuan perbaikan.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../detail.php?tiket=<?= urlencode($data['nomor_tiket']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">Lihat Web</a>
        <a href="pengumuman.php" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm">
        <strong class="d-block mb-1">Terjadi kesalahan:</strong>
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= clean($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm border mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="edit.php?id=<?= $id ?>" method="POST">
            <div class="row g-3">
                <!-- Nomor Tiket (Read Only) -->
                <div class="col-12 col-md-6">
                    <label class="form-label">Nomor Tiket</label>
                    <input type="text" class="form-control bg-light fw-bold text-primary" value="<?= clean($data['nomor_tiket']) ?>" readonly>
                </div>

                <!-- Kecamatan Terdampak -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="kecamatan_id">Kecamatan Wilayah Pelayanan <span class="req">*</span></label>
                    <select id="kecamatan_id" name="kecamatan_id" class="form-select" required>
                        <?php foreach ($list_kecamatan as $kec): ?>
                            <option value="<?= $kec['id'] ?>" <?= $data['kecamatan_id'] == $kec['id'] ? 'selected' : '' ?>>
                                Kecamatan <?= clean($kec['nama_kecamatan']) ?> (<?= clean($kec['cabang_pelayanan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Judul / Perihal -->
                <div class="col-12">
                    <label class="form-label" for="judul">Judul Perihal Gangguan <span class="req">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control" value="<?= clean($data['judul']) ?>" required>
                </div>

                <!-- Wilayah / Jalan Terdampak -->
                <div class="col-12">
                    <label class="form-label" for="wilayah_terdampak">Daftar Wilayah, Jalan & Perumahan Terdampak <span class="req">*</span></label>
                    <textarea id="wilayah_terdampak" name="wilayah_terdampak" class="form-control" rows="3" required><?= clean($data['wilayah_terdampak']) ?></textarea>
                </div>

                <!-- Penyebab Gangguan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="penyebab">Penyebab Gangguan <span class="req">*</span></label>
                    <textarea id="penyebab" name="penyebab" class="form-control" rows="3" required><?= clean($data['penyebab']) ?></textarea>
                </div>

                <!-- Tindakan Teknis Lapangan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="tindakan">Tindakan Lapangan / Langkah Perbaikan <span class="req">*</span></label>
                    <textarea id="tindakan" name="tindakan" class="form-control" rows="3" required><?= clean($data['tindakan']) ?></textarea>
                </div>

                <!-- Dampak Aliran Air -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="dampak_aliran">Dampak Terhadap Aliran Air <span class="req">*</span></label>
                    <select id="dampak_aliran" name="dampak_aliran" class="form-select" required>
                        <option value="mati_total" <?= $data['dampak_aliran'] === 'mati_total' ? 'selected' : '' ?>>Air Mati Total (Aliran Padam)</option>
                        <option value="aliran_kecil" <?= $data['dampak_aliran'] === 'aliran_kecil' ? 'selected' : '' ?>>Aliran Air Kecil / Debit Rendah</option>
                        <option value="bertekanan_rendah" <?= $data['dampak_aliran'] === 'bertekanan_rendah' ? 'selected' : '' ?>>Tekanan Rendah / Air Keruh Sesaat</option>
                    </select>
                </div>

                <!-- Status Penanganan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="status">Status Terkini <span class="req">*</span></label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="investigasi" <?= $data['status'] === 'investigasi' ? 'selected' : '' ?>>Investigasi Lapangan</option>
                        <option value="perbaikan" <?= $data['status'] === 'perbaikan' ? 'selected' : '' ?>>Sedang Dalam Proses Perbaikan</option>
                        <option value="normalisasi" <?= $data['status'] === 'normalisasi' ? 'selected' : '' ?>>Tahap Normalisasi Aliran</option>
                        <option value="selesai" <?= $data['status'] === 'selesai' ? 'selected' : '' ?>>Selesai Ditangani</option>
                    </select>
                </div>

                <!-- Waktu Mulai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="waktu_mulai">Waktu Mulai Gangguan <span class="req">*</span></label>
                    <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($data['waktu_mulai'])) ?>" required>
                </div>

                <!-- Estimasi Selesai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="estimasi_selesai">Estimasi Selesai Normal</label>
                    <input type="datetime-local" id="estimasi_selesai" name="estimasi_selesai" class="form-control" value="<?= !empty($data['estimasi_selesai']) ? date('Y-m-d\TH:i', strtotime($data['estimasi_selesai'])) : '' ?>">
                </div>

                <!-- Kontak Posko -->
                <div class="col-12">
                    <label class="form-label" for="kontak_posko">Kontak Posko Tangki Air</label>
                    <input type="text" id="kontak_posko" name="kontak_posko" class="form-control" value="<?= clean($data['kontak_posko']) ?>">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
                <a href="pengumuman.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
