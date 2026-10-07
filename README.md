# 💧 SIGANA (Sistem Informasi Gangguan Aliran Air)
### Perumda Air Minum Tirta Intan Kabupaten Garut

**SIGANA** adalah aplikasi web resmi untuk publikasi, pemantauan, dan pengelolaan informasi gangguan pasokan serta pemeliharaan jaringan pipa transmisi/distribusi air bersih pelanggan di wilayah Kabupaten Garut.

Dibuat menggunakan **Pure Native PHP & MySQL (Zero Framework Dependency)** sehingga sangat ringan, cepat, mudah dijalankan langsung di Laragon/XAMPP, dan sangat mudah dipelihara atau di-troubleshoot.

---

## 🚀 Fitur Utama

### 🌐 Sisi Pengguna Publik
1. **Papan Pengumuman Interaktif**: Menampilkan daftar pengumuman gangguan air yang terstruktur dengan badge status (`Investigasi`, `Dalam Perbaikan`, `Normalisasi`, `Selesai`).
2. **Pencarian & Filter Cepat (Real-Time)**: Cari berdasarkan nama jalan, perumahan, nomor tiket, atau filter berdasarkan kecamatan dan status penanganan tanpa perlu reload halaman.
3. **Statistik Gangguan Terkini**: Ringkasan jumlah pekerjaan perbaikan, normalisasi, dan selesai.
4. **Halaman Rincian Tiket (`detail.php`)**: Menampilkan penyebab, langkah teknis lapangan, estimasi normalisasi, serta nomor armada tangki air darurat.
5. **Bagikan ke WhatsApp (`Share to WA`)**: Format pesan otomatis yang rapi dan memuat ringkasan gangguan beserta tautan resmi.
6. **Cetak Lembar Pengumuman**: Tampilan print-friendly untuk dicetak dan ditempel di papan pengumuman fisik kantor cabang / desa.

### 🔐 Sisi Panel Admin & Petugas
1. **Dashboard Operasional**: Metrik data gangguan, quick actions, dan tabel pengumuman terbaru.
2. **Manajemen Pengumuman (CRUD)**:
   - Tambah pengumuman dengan generator nomor tiket otomatis (`GNG-YYYYMM-XXX`).
   - Edit rincian gangguan dan pembaruan status real-time.
   - Hapus data dengan konfirmasi keamanan.
3. **Master Wilayah Kecamatan**:
   - Tambah & edit daftar kecamatan serta kantor cabang pelayanan.
   - Statistik aktif gangguan per kecamatan.
   - Proteksi hapus jika kecamatan masih memiliki riwayat pengumuman.
4. **Pengaturan Akun Petugas (`profil.php`)**: Ubah identitas petugas dan pembaruan kata sandi aman (Enkripsi Bcrypt).
5. **Autentikasi & Session Guard**: Pengamanan halaman admin dengan proteksi session native.

---

## 📁 Struktur Direktori Proyek

```text
sigana/
│
├── admin/                     # Modul Panel Admin & Petugas
│   ├── index.php              # Dashboard Operasional Admin
│   ├── login.php              # Halaman Login Petugas
│   ├── logout.php             # Handler Logout Petugas
│   ├── pengumuman.php         # Kelola Seluruh Data Pengumuman
│   ├── tambah.php             # Form Input Pengumuman Baru
│   ├── edit.php               # Form Edit & Update Status
│   ├── hapus.php              # Action Hapus Pengumuman
│   ├── kecamatan.php          # Kelola Master Kecamatan
│   └── profil.php             # Kelola Profil Petugas & Ganti Password
│
├── assets/                    # Aset Statis (CSS, JS, Gambar)
│   ├── css/
│   │   ├── style.css          # Styling Utama & Kartu Pengumuman
│   │   └── admin.css          # Styling Panel Admin
│   ├── js/
│   │   ├── main.js            # Logika Pencarian, Filter & WhatsApp Share
│   │   └── admin.js           # Konfirmasi Hapus & Flash Alert Dismiss
│   └── img/
│       └── logo.png           # Logo Resmi Perumda Tirta Intan Garut
│
├── config/
│   └── database.php           # Koneksi PDO & Auto-Migration Database
│
├── includes/                  # Komponen Reusable Header/Footer/Helper
│   ├── functions.php          # Fungsi Helper, Format Tanggal, SVG Icons, Flash Msg
│   ├── header.php             # Header Publik
│   ├── footer.php             # Footer Publik
│   ├── admin_header.php       # Topbar & Sidebar Admin
│   └── admin_footer.php       # Footer Admin
│
├── .env                       # File Konfigurasi Environment (Opsional)
├── database.sql               # Skema Tabel MySQL & Data Seeder Awal
├── detail.php                 # Halaman Rincian Pengumuman Publik
├── index.php                  # Halaman Papan Pengumuman Publik
└── README.md                  # Dokumentasi Proyek
```

---

## 🛠️ Panduan Instalasi & Menjalankan di Laragon

1. **Letakkan Folder Proyek**:
   Pastikan folder proyek berada di `C:\laragon\www\sigana`

2. **Jalankan Service**:
   Buka Laragon, klik tombol **Start All** (Apache & MySQL).

3. **Buka Melalui Browser**:
   - **Halaman Publik**: [http://localhost/sigana/](http://localhost/sigana/) atau [http://sigana.test/](http://sigana.test/)
   - **Panel Admin**: [http://localhost/sigana/admin/login.php](http://localhost/sigana/admin/login.php)

> **Catatan Auto-Setup**:
> Sistem secara otomatis akan membuat database `db_sigana` dan mengeksekusi tabel serta seeder awal dari `database.sql` pada saat pertama kali halaman web dibuka.

---

## 🔑 Akun Default Petugas Admin

| Keterangan | Nilai |
| :--- | :--- |
| **Username** | `admin` |
| **Password** | `admin123` |
| **Akses** | Administrator / Petugas Humas & Teknis |

---

## 🔧 Panduan Troubleshooting

| Gejala Masalah | Penyebab Umum | Solusi Cepat |
| :--- | :--- | :--- |
| **Koneksi Database Gagal** | Service MySQL belum berjalan atau port 3306 bentrok. | Buka Laragon / XAMPP, pastikan tombol MySQL berwarna hijau (*Running*). Periksa kredensial di file `.env`. |
| **Halaman tidak mau login** | Session PHP terblokir di browser. | Pastikan cookie diizinkan di browser, atau coba buka via *Incognito Window*. |
| **Gambar logo tidak muncul** | Path file berbeda. | Pastikan file `assets/img/logo.png` tersedia di folder proyek. |

---
*Dikembangkan untuk Perumda Air Minum Tirta Intan Kabupaten Garut.*
