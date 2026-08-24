<?php
$pageTitle = "Buat Pengumuman Baru";
require_once __DIR__ . '/../includes/admin_header.php';

// Generate nomor tiket otomatis
$nomor_tiket_auto = generateNomorTiket($pdo);

// Ambil list kecamatan
$stmt_kec = $pdo->query("SELECT * FROM kecamatan ORDER BY nama_kecamatan ASC");
$list_kecamatan = $stmt_kec->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_tiket = trim($_POST['nomor_tiket'] ?? '');
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

    // Validasi
    if (empty($nomor_tiket)) $errors[] = 'Nomor tiket wajib diisi.';
    if (empty($judul)) $errors[] = 'Judul perihal gangguan wajib diisi.';
    if ($kecamatan_id <= 0) $errors[] = 'Pilih kecamatan wilayah terdampak.';
    if (empty($wilayah_terdampak)) $errors[] = 'Rincian wilayah/jalan terdampak wajib diisi.';
    if (empty($penyebab)) $errors[] = 'Penyebab gangguan wajib diisi.';
    if (empty($tindakan)) $errors[] = 'Langkah tindakan teknis lapangan wajib diisi.';
    if (empty($waktu_mulai)) $errors[] = 'Waktu mulai kejadian gangguan wajib ditentukan.';

    // Cek duplikasi nomor tiket
    $check = $pdo->prepare("SELECT id FROM pengumuman WHERE nomor_tiket = ? LIMIT 1");
    $check->execute([$nomor_tiket]);
    if ($check->fetch()) {
        $errors[] = 'Nomor tiket ini sudah pernah digunakan.';
    }

    if (empty($errors)) {
        try {
            $insert_sql = "
                INSERT INTO pengumuman (
                    nomor_tiket, judul, kecamatan_id, wilayah_terdampak, 
                    penyebab, tindakan, dampak_aliran, status, 
                    waktu_mulai, estimasi_selesai, kontak_posko, created_by
                ) VALUES (
                    ?, ?, ?, ?, 
                    ?, ?, ?, ?, 
                    ?, ?, ?, ?
                )
            ";
            $stmt_ins = $pdo->prepare($insert_sql);
            $stmt_ins->execute([
                $nomor_tiket,
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
                $user['id']
            ]);

            setFlash('success', "Pengumuman #{$nomor_tiket} berhasil dipublikasikan!");
            header('Location: pengumuman.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Form Input Pengumuman Baru</h2>
        <p class="text-muted small mb-0">Publikasikan informasi pemeliharaan dan gangguan pipa kepada pelanggan.</p>
    </div>
    <a href="pengumuman.php" class="btn btn-outline-secondary btn-sm">&larr; Kembali ke Daftar</a>
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
        <form action="tambah.php" method="POST">
            <div class="row g-3">
                <!-- Nomor Tiket -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nomor_tiket">Nomor Tiket <span class="req">*</span></label>
                    <input type="text" id="nomor_tiket" name="nomor_tiket" class="form-control" value="<?= isset($_POST['nomor_tiket']) ? clean($_POST['nomor_tiket']) : $nomor_tiket_auto ?>" required>
                    <div class="form-text small">Format otomatis: GNG-YYYYMM-XXX</div>
                </div>

                <!-- Kecamatan Terdampak -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="kecamatan_id">Kecamatan Wilayah Pelayanan <span class="req">*</span></label>
                    <select id="kecamatan_id" name="kecamatan_id" class="form-select" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        <?php foreach ($list_kecamatan as $kec): ?>
                            <option value="<?= $kec['id'] ?>" <?= (isset($_POST['kecamatan_id']) && $_POST['kecamatan_id'] == $kec['id']) ? 'selected' : '' ?>>
                                Kecamatan <?= clean($kec['nama_kecamatan']) ?> (<?= clean($kec['cabang_pelayanan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Judul / Perihal -->
                <div class="col-12">
                    <label class="form-label" for="judul">Judul Perihal Gangguan Air <span class="req">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control" placeholder="Contoh: Perbaikan Kebocoran Pipa Transmisi HDPE 200mm" value="<?= isset($_POST['judul']) ? clean($_POST['judul']) : '' ?>" required>
                </div>

                <!-- Wilayah / Jalan Terdampak -->
                <div class="col-12">
                    <label class="form-label" for="wilayah_terdampak">Daftar Wilayah, Jalan & Perumahan Terdampak <span class="req">*</span></label>
                    <textarea id="wilayah_terdampak" name="wilayah_terdampak" class="form-control" rows="3" placeholder="Contoh: Jl. Patriot, Komplek Perumahan Gordah, Perumahan Pemda, Kp. Sukaregang RT 01-04" required><?= isset($_POST['wilayah_terdampak']) ? clean($_POST['wilayah_terdampak']) : '' ?></textarea>
                </div>

                <!-- Penyebab Gangguan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="penyebab">Penyebab Gangguan <span class="req">*</span></label>
                    <textarea id="penyebab" name="penyebab" class="form-control" rows="3" placeholder="Contoh: Pipa transmisi bocor akibat tingginya tekanan air dan pergeseran tanah." required><?= isset($_POST['penyebab']) ? clean($_POST['penyebab']) : '' ?></textarea>
                </div>

                <!-- Tindakan Teknis Lapangan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="tindakan">Tindakan Lapangan / Langkah Perbaikan <span class="req">*</span></label>
                    <textarea id="tindakan" name="tindakan" class="form-control" rows="3" placeholder="Contoh: Tim Trandis sedang melakukan penggalian dan penyambungan pipa baru." required><?= isset($_POST['tindakan']) ? clean($_POST['tindakan']) : '' ?></textarea>
                </div>

                <!-- Dampak Aliran Air -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="dampak_aliran">Dampak Terhadap Aliran Air <span class="req">*</span></label>
                    <select id="dampak_aliran" name="dampak_aliran" class="form-select" required>
                        <option value="mati_total" <?= (isset($_POST['dampak_aliran']) && $_POST['dampak_aliran'] === 'mati_total') ? 'selected' : '' ?>>Air Mati Total (Aliran Padam)</option>
                        <option value="aliran_kecil" <?= (isset($_POST['dampak_aliran']) && $_POST['dampak_aliran'] === 'aliran_kecil') ? 'selected' : '' ?>>Aliran Air Kecil / Debit Rendah</option>
                        <option value="bertekanan_rendah" <?= (isset($_POST['dampak_aliran']) && $_POST['dampak_aliran'] === 'bertekanan_rendah') ? 'selected' : '' ?>>Tekanan Rendah / Air Keruh Sesaat</option>
                    </select>
                </div>

                <!-- Status Penanganan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="status">Status Penanganan <span class="req">*</span></label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="investigasi" <?= (isset($_POST['status']) && $_POST['status'] === 'investigasi') ? 'selected' : '' ?>>Investigasi Lapangan</option>
                        <option value="perbaikan" <?= (!isset($_POST['status']) || $_POST['status'] === 'perbaikan') ? 'selected' : '' ?>>Sedang Dalam Proses Perbaikan</option>
                        <option value="normalisasi" <?= (isset($_POST['status']) && $_POST['status'] === 'normalisasi') ? 'selected' : '' ?>>Tahap Normalisasi Aliran</option>
                        <option value="selesai" <?= (isset($_POST['status']) && $_POST['status'] === 'selesai') ? 'selected' : '' ?>>Selesai Ditangani</option>
                    </select>
                </div>

                <!-- Waktu Mulai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="waktu_mulai">Waktu Mulai Gangguan <span class="req">*</span></label>
                    <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="form-control" value="<?= isset($_POST['waktu_mulai']) ? clean($_POST['waktu_mulai']) : date('Y-m-d\TH:i') ?>" required>
                </div>

                <!-- Estimasi Selesai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="estimasi_selesai">Estimasi Selesai Normal</label>
                    <input type="datetime-local" id="estimasi_selesai" name="estimasi_selesai" class="form-control" value="<?= isset($_POST['estimasi_selesai']) ? clean($_POST['estimasi_selesai']) : date('Y-m-d\TH:i', strtotime('+4 hours')) ?>">
                </div>

                <!-- Kontak Posko -->
                <div class="col-12">
                    <label class="form-label" for="kontak_posko">Kontak Posko / Layanan Armada Tangki Air</label>
                    <input type="text" id="kontak_posko" name="kontak_posko" class="form-control" value="<?= isset($_POST['kontak_posko']) ? clean($_POST['kontak_posko']) : '0811-2345-6789' ?>">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
                <a href="pengumuman.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold">
                    Publikasikan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
