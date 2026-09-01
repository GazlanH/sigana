<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$nomor_tiket = $_GET['tiket'] ?? '';

if (empty($nomor_tiket)) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, k.nama_kecamatan, k.cabang_pelayanan, u.nama_lengkap as nama_petugas, u.jabatan as jabatan_petugas
    FROM pengumuman p
    JOIN kecamatan k ON p.kecamatan_id = k.id
    LEFT JOIN users u ON p.created_by = u.id
    WHERE p.nomor_tiket = ?
    LIMIT 1
");
$stmt->execute([$nomor_tiket]);
$detail = $stmt->fetch();

if (!$detail) {
    die("<div class='container my-5 text-center'>
        <h3>Pengumuman Tidak Ditemukan</h3>
        <a href='index.php' class='btn btn-primary mt-3'>Kembali ke Papan Pengumuman</a>
    </div>");
}

$pageTitle = "Rincian Tiket #" . $detail['nomor_tiket'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="container my-4">
    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary btn-sm fw-semibold">&larr; Kembali ke Papan Pengumuman</a>
    </div>

    <div class="card shadow-sm border bulletin-card card-<?= strtolower($detail['status']) ?> overflow-hidden">
        <!-- Top Info Header -->
        <div class="card-header bg-light p-3 p-md-4 border-bottom">
            <div class="d-flex gap-2 flex-wrap align-items-center mb-2">
                <span class="ticket-tag">#<?= clean($detail['nomor_tiket']) ?></span>
                <?= renderStatusBadge($detail['status']) ?>
                <?= renderDampakBadge($detail['dampak_aliran']) ?>
            </div>
            <h2 class="h4 fw-bold text-dark mb-1">
                <?= clean($detail['judul']) ?>
            </h2>
            <div class="small text-muted">
                Wilayah Pelayanan: <strong>Kecamatan <?= clean($detail['nama_kecamatan']) ?> (<?= clean($detail['cabang_pelayanan']) ?>)</strong>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Blok Wilayah (Area Terdampak) -->
            <div class="bulletin-area-box mb-3 p-3">
                <div class="fw-bold mb-1" style="color: #0369a1;"><?= getIcon('pin') ?> DAFTAR WILAYAH & JALAN TERDAMPAK</div>
                <div class="text-dark">
                    <?= nl2br(clean($detail['wilayah_terdampak'])) ?>
                </div>
            </div>

            <!-- Grid Penyebab & Tindakan -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="bulletin-mini-box cause p-3 h-100">
                        <div class="bulletin-mini-label mb-2"><?= getIcon('warning') ?> PENYEBAB GANGGUAN</div>
                        <div class="text-secondary"><?= nl2br(clean($detail['penyebab'])) ?></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bulletin-mini-box action p-3 h-100">
                        <div class="bulletin-mini-label mb-2"><?= getIcon('tool') ?> TINDAKAN LAPANGAN</div>
                        <div class="text-secondary"><?= nl2br(clean($detail['tindakan'])) ?></div>
                    </div>
                </div>
            </div>

            <!-- Baris Waktu & Posko Tangki -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <div class="bg-light border rounded p-2">
                        <div class="small text-muted text-uppercase fw-bold"><?= getIcon('clock') ?> Waktu Mulai:</div>
                        <div class="fw-semibold text-dark"><?= formatTanggalIndo($detail['waktu_mulai'], true) ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-block-eta <?= $detail['status'] === 'selesai' ? 'selesai' : '' ?> w-100 d-block p-2">
                        <div class="small text-uppercase fw-bold">
                            <?= $detail['status'] === 'selesai' ? getIcon('check') . ' Status:' : getIcon('clock') . ' Estimasi Normal:' ?>
                        </div>
                        <div class="fw-bold">
                            <?= $detail['status'] === 'selesai' ? 'Pekerjaan Selesai (Aliran Normal)' : formatTanggalIndo($detail['estimasi_selesai'], true) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-success-subtle text-success-emphasis border border-success-subtle rounded p-2">
                        <div class="small text-uppercase fw-bold"><?= getIcon('phone') ?> Posko Tangki Darurat:</div>
                        <div class="fw-bold">
                            <a href="tel:<?= clean($detail['kontak_posko']) ?>" class="text-success-emphasis text-decoration-none"><?= clean($detail['kontak_posko']) ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imbauan -->
            <div class="alert alert-warning py-2 px-3 small mb-4">
                <strong>Imbauan Pelanggan:</strong> Pelanggan diimbau untuk menampung air secukupnya jika aliran masih mengalir kecil. Tim teknis berupaya semaksimal mungkin menuntaskan perbaikan jaringan pipa.
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2 flex-wrap pt-3 border-top">
                <button type="button" class="btn btn-success fw-semibold"
                    onclick="shareToWA(
                        '<?= clean($detail['nomor_tiket']) ?>',
                        '<?= addslashes(clean($detail['judul'])) ?>',
                        '<?= addslashes(clean($detail['nama_kecamatan'])) ?>',
                        '<?= addslashes(clean($detail['wilayah_terdampak'])) ?>',
                        '<?= addslashes(clean($detail['status'])) ?>',
                        '<?= addslashes(formatTanggalIndo($detail['estimasi_selesai'], true)) ?>'
                    )"
                >
                    <?= getIcon('whatsapp') ?> Bagikan ke WhatsApp
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="window.print()">
                    Cetak Lembar Pengumuman
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="copyLink('<?= clean($detail['nomor_tiket']) ?>')">
                    Salin Tautan
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
