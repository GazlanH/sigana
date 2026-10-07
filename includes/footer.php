<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Footer Publik Resmi (Clean Corporate Design)
 * ============================================================================
 */
?>
    <!-- Corporate Footer -->
    <footer class="corporate-footer bg-white border-top mt-auto pt-4 pb-3">
        <div class="container">
            <div class="row g-4 mb-3">
                <div class="col-lg-5 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="assets/img/logo.png" alt="Logo Tirta Intan" width="36" height="36" class="brand-logo-img-sm">
                        <div>
                            <div class="fw-bold text-dark lh-1">PERUMDA AIR MINUM TIRTA INTAN</div>
                            <small class="text-muted fw-semibold">KABUPATEN GARUT</small>
                        </div>
                    </div>
                    <p class="small text-muted mb-2 pe-lg-4" style="line-height: 1.6;">
                        Badan Usaha Milik Daerah (BUMD) Pemerintah Kabupaten Garut yang bertugas mengelola dan mendistribusikan air minum berkualitas secara merata untuk masyarakat.
                    </p>
                    <div class="small text-muted">
                        <?= getIcon('pin') ?> Jl. Raya Bayongbong KM 3, Kec. Cilawu, Kab. Garut, Jawa Barat 44181
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Layanan Pelanggan</h6>
                    <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-1">
                        <li><?= getIcon('phone') ?> Call Center: <strong>(0262) 232450</strong></li>
                        <li><?= getIcon('whatsapp') ?> WhatsApp: <strong>0811-2345-6789</strong></li>
                        <li><?= getIcon('clock') ?> Jam Layanan: 24 Jam Nonstop</li>
                        <li><?= getIcon('tool') ?> Posko Tangki Darurat Siaga</li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-12">
                    <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Tentang SIGANA</h6>
                    <p class="small text-muted mb-3" style="line-height: 1.6;">
                        Sistem Informasi Pengumuman Gangguan Aliran Air (SIGANA) dikembangkan sebagai wujud transparansi informasi publik dan respons cepat penanganan pemeliharaan jaringan pipa.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="index.php" class="btn btn-outline-secondary btn-sm py-1 px-2" style="font-size: 0.78rem;">Papan Pengumuman</a>
                        <a href="admin/login.php" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 0.78rem;">Login Petugas Admin</a>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 small text-muted">
                <div>
                    &copy; <?= date('Y') ?> <strong>Perumda Air Minum Tirta Intan Garut</strong>. Seluruh Hak Cipta Dilindungi.
                </div>
                <div>
                    SIGANA Versi 2.5 &bull; Pure Native Architecture
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Frontend Application Logic -->
    <script src="assets/js/main.js?v=2.5"></script>
</body>
</html>
