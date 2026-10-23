# sigana

## demo aplikasi

<!-- kamu bisa simpan file gif di folder assets/img/ atau ganti path di bawah ini -->
![demo aplikasi](assets/img/demo.gif)

---

## tentang aplikasi

sigana (sistem informasi gangguan aliran air) adalah aplikasi web untuk mempublikasikan dan memantau informasi gangguan aliran air bersih serta perbaikan pipa jaringan pelanggan di wilayah perumda air minum tirta intan kabupaten garut.

---

## fungsi utama aplikasi

### untuk masyarakat umum
- melihat pengumuman gangguan aliran air secara langsung beserta status penanganannya (investigasi, dalam perbaikan, normalisasi, dan selesai).
- memfilter daftar pengumuman berdasarkan kecamatan dan dampak aliran air tanpa reload halaman.
- melihat detail pengumuman seperti penyebab gangguan, tindakan teknis petugas, estimasi air normal, dan kontak armada tangki air darurat.
- membagikan info gangguan langsung ke WhatsApp dengan format teks yang sudah rapi dan menyertakan tautan resmi.
- mencetak pengumuman ke lembar kertas fisik untuk kebutuhan papan informasi kantor atau warga.

### untuk petugas dan admin
- melihat ringkasan data dan statistik pekerjaan gangguan lewat dashboard operasional.
- membuat pengumuman gangguan baru dengan nomor tiket yang dibuat otomatis.
- mengubah status penanganan dan memperbarui perkembangan perbaikan di lapangan.
- mengelola master data wilayah kecamatan yang terhubung ke jaringan pelayanan.
- mengelola akun petugas dan mengganti kata sandi secara aman.

---

## teknologi yang dipakai

- **PHP**
  dipakai sebagai bahasa backend utama untuk mengurus logika aplikasi, rute halaman, pengolahan form, sistem sesi login petugas, dan menampilkan data ke halaman web.

- **MySQL**
  dipakai sebagai database untuk menyimpan seluruh data pengumuman gangguan, data kecamatan, dan akun pengguna secara terstruktur.

- **PDO (PHP Data Objects)**
  dipakai sebagai penghubung antara PHP dan MySQL dengan prepared statements agar aman dari celah SQL injection, sekaligus menjalankan proses migrasi tabel database secara otomatis.

- **HTML5**
  dipakai untuk menyusun struktur dasar halaman web baik pada bagian publik maupun panel admin.

- **CSS3**
  dipakai untuk mengatur tampilan antarmuka, tata letak, warna tema, kartu pengumuman, dan format tampilan khusus saat dicetak.

- **Bootstrap 5**
  dipakai sebagai framework CSS pendukung untuk mempermudah pengaturan grid responsif di berbagai ukuran layar, komponen formulir, tombol, notifikasi alert, dan menu mobile drawer.

- **JavaScript (Vanilla JS)**
  dipakai di sisi browser untuk menjalankan fitur pencarian dan filter kartu pengumuman secara real-time, sistem pagination halaman, dan konfirmasi saat menghapus data.

- **Google Fonts**
  dipakai untuk memuat font Plus Jakarta Sans agar teks informasi mudah dan nyaman dibaca.

- **SVG**
  dipakai untuk menampilkan ikon antarmuka secara inline sehingga tampilan tetap tajam dan beban muat halaman tetap ringan tanpa library icon font tambahan.
