<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Footer Publik Resmi (Deep Oceanic Blue & Clean Multi-Column)
 * ============================================================================
 */
?>
    <!-- Corporate Deep Blue Footer -->
    <footer class="corporate-footer">
        <div class="container">
            <div class="row g-4 mb-4">
                <!-- Column 1: Identity & Address -->
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="assets/img/logo.png" alt="Logo Tirta Intan Garut" width="44" height="44" class="footer-brand-logo">
                        <div>
                            <div class="footer-brand-title">PERUMDA TIRTA INTAN</div>
                            <div class="footer-brand-sub">KABUPATEN GARUT</div>
                        </div>
                    </div>
                    <p class="footer-desc pe-lg-3">
                        Jl. Raya Bayongbong KM 3, Kec. Cilawu, Kab. Garut, Jawa Barat 44181.<br>
                        Badan Usaha Milik Daerah (BUMD) Pemerintah Kabupaten Garut pengelola distribusi air bersih masyarakat.
                    </p>
                    <div class="small text-info-subtle mt-2">
                        <strong>Status Operasional:</strong> Posko Siaga 24 Jam Nonstop
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h4 class="footer-heading">Quick Link</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Gangguan Layanan</a></li>
                        <li><a href="index.php">Wilayah Pelayanan</a></li>
                        <li><a href="tel:0262232450">Call Center Resmi</a></li>
                        <li><a href="admin/login.php">Portal Petugas</a></li>
                    </ul>
                </div>

                <!-- Column 3: Layanan Pelanggan -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h4 class="footer-heading">Layanan Pelanggan</h4>
                    <ul class="footer-links">
                        <li><a href="https://wa.me/6281123456789" target="_blank">Permintaan Tangki Air</a></li>
                        <li><a href="https://wa.me/6281123456789" target="_blank">Lapor Pipa Bocor</a></li>
                        <li><a href="tel:0262232450">Pengaduan Gangguan</a></li>
                        <li><a href="index.php">Cek Status Penanganan</a></li>
                    </ul>
                </div>

                <!-- Column 4: Kontak & Social Media -->
                <div class="col-lg-3 col-md-6">
                    <h4 class="footer-heading">Kontak & Posko</h4>
                    <ul class="footer-links">
                        <li><a href="tel:0262232450"><?= getIcon('phone') ?> (0262) 232450</a></li>
                        <li><a href="https://wa.me/6281123456789" target="_blank"><?= getIcon('whatsapp') ?> 0811-2345-6789 (WhatsApp)</a></li>
                        <li><a href="https://instagram.com" target="_blank">Instagram @tirtyaintangarut</a></li>
                        <li><a href="https://facebook.com" target="_blank">Facebook Tirta Intan Garut</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> <strong>Perumda Air Minum Tirta Intan Kabupaten Garut</strong>. All rights reserved.
                </div>
                <div class="d-none d-sm-block">
                    SIGANA &bull; Sistem Informasi Gangguan Aliran Air
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Frontend Application Logic -->
    <script src="assets/js/main.js?v=3.0"></script>
</body>
</html>
