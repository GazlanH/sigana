# SIGANA - Sistem Informasi Gangguan Air
### PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
*Proyek Laporan & Sidang Akhir Praktik Kerja Lapangan (PKL) - SMK Jurusan Rekayasa Perangkat Lunak (RPL)*  
**Oleh:** Gazlan

---

## 🌊 1. Latar Belakang & Tujuan Proyek

Di era digitalisasi pelayanan publik, Perumda Air Minum Tirta Intan Garut terus berinovasi untuk memberikan pelayanan prima kepada seluruh pelanggan di Kabupaten Garut. Ketika terjadi kebocoran pipa transmisi, perawatan bak sedimentasi di Instalasi Pengolahan Air (IPA), atau gangguan pompa intake, masyarakat membutuhkan informasi yang cepat, jelas, dan transparan mengenai:
1. **Wilayah & jalan mana saja yang terdampak pemadaman aliran air.**
2. **Apa penyebab teknis gangguan.**
3. **Bagaimana langkah penanganan di lapangan.**
4. **Berapa estimasi waktu air kembali mengalir normal.**

**SIGANA (Sistem Informasi Gangguan Air)** hadir sebagai platform papan pengumuman satu arah berbasis web yang ringan, modern, mudah diakses pelanggan melalui smartphone, serta mempermudah staf Humas/Teknis Perumda dalam mempublikasikan pembaruan status perbaikan secara real-time.

---

## 🛠️ 2. Teknologi yang Digunakan (Tech Stack)

| Komponen | Teknologi | Alasan Pemilihan |
| :--- | :--- | :--- |
| **Backend** | **PHP Native (PDO - PHP 7.4 / 8.x)** | Ringan, terstruktur, aman dari SQL Injection dengan *Prepared Statements*, dan mudah dijelaskan baris demi baris saat sidang. |
| **Database** | **MySQL / MariaDB** | Standar basis data relasional industri, mendukung integritas relasi antar tabel (*Foreign Keys*). |
| **Frontend** | **HTML5, Vanilla Modern CSS, JavaScript** | Desain responsif, cepat dimuat di jaringan seluler tanpa beban framework berat, mendukung *Live Search* dan filter tanpa reload. |
| **Icons & Font** | **Lucide Icons & Google Fonts (Plus Jakarta Sans)** | Memberikan kesan UI/UX modern, profesional, dan elegan khas perusahaan daerah. |
| **Environment** | **Laragon (Apache & MySQL Server)** | Lingkungan pengembangan lokal yang cepat dan stabil. |

---

## 📁 3. Struktur Direktori Proyek

```text
c:\laragon\www\sigana\
├── assets/
│   ├── css/
│   │   ├── style.css         # Styling halaman publik (Glassmorphism, Card grid, Status badge)
│   │   └── admin.css         # Styling dashboard panel admin & layout sidebar
│   └── js/
│       ├── main.js           # Filter kecamatan, searchbox realtime, generator link WA
│       └── admin.js          # Konfirmasi hapus, status alert dismissal
├── config/
│   └── database.php          # Koneksi PDO + Auto-installer database db_sigana otomatis
├── includes/
│   ├── functions.php         # Format tanggal Indonesia, sanitasi XSS, badge helper, auth guard
│   ├── header.php            # Header publik (Call center bar, branding Tirta Intan)
│   ├── footer.php            # Footer publik resmi & kontak posko
│   ├── admin_header.php      # Sidebar & Topbar navigasi admin
│   └── admin_footer.php      # Footer panel admin
├── admin/
│   ├── index.php             # Dashboard metrik gangguan & tabel pengumuman terbaru
│   ├── login.php             # Halaman login petugas (Password hash security)
│   ├── logout.php            # Hapus session login
│   ├── pengumuman.php        # Daftar seluruh pengumuman + filter & pencarian
│   ├── tambah.php            # Form input pengumuman gangguan baru
│   ├── edit.php              # Form edit & update progres status (Investigasi -> Perbaikan -> Selesai)
│   ├── hapus.php             # Action hapus pengumuman
│   └── kecamatan.php         # Master data kecamatan & cabang pelayanan di Garut
├── index.php                 # Halaman utama papan pengumuman untuk pelanggan
├── detail.php                # Halaman detail rincian tiket gangguan + cetak lembar pengumuman
├── database.sql              # File SQL skema & data demo
└── README.md                 # Dokumentasi & panduan sidang PKL
```

---

## 🗄️ 4. Struktur Database (`db_sigana`)

### 1. Tabel `users` (Akun Petugas / Admin)
- `id` (INT, PK, Auto Increment)
- `username` (VARCHAR 50, Unique)
- `password` (VARCHAR 255 - Terenkripsi `password_hash()`)
- `nama_lengkap` (VARCHAR 100)
- `jabatan` (VARCHAR 100)
- `role` (ENUM: `admin`, `petugas`)
- `created_at` (TIMESTAMP)

### 2. Tabel `kecamatan` (Wilayah Pelayanan di Garut)
- `id` (INT, PK, Auto Increment)
- `nama_kecamatan` (VARCHAR 100, Unique)
- `cabang_pelayanan` (VARCHAR 100)
- `created_at` (TIMESTAMP)

### 3. Tabel `pengumuman` (Data Gangguan Air)
- `id` (INT, PK, Auto Increment)
- `nomor_tiket` (VARCHAR 30, Unique, Contoh: `GNG-202608-001`)
- `judul` (VARCHAR 200)
- `kecamatan_id` (INT, Foreign Key -> `kecamatan.id`)
- `wilayah_terdampak` (TEXT)
- `penyebab` (TEXT)
- `tindakan` (TEXT)
- `dampak_aliran` (ENUM: `mati_total`, `aliran_kecil`, `bertekanan_rendah`)
- `status` (ENUM: `investigasi`, `perbaikan`, `normalisasi`, `selesai`)
- `waktu_mulai` (DATETIME)
- `estimasi_selesai` (DATETIME)
- `waktu_selesai_aktual` (DATETIME)
- `kontak_posko` (VARCHAR 50)
- `created_by` (INT, Foreign Key -> `users.id`)
- `created_at`, `updated_at` (TIMESTAMP)

---

## 🚀 5. Cara Menjalankan Aplikasi

1. Buka aplikasi **Laragon**.
2. Klik tombol **"Start All"** (pastikan Apache dan MySQL aktif).
3. Buka browser (Google Chrome / Edge) dan kunjungi:
   - **Halaman Publik Pelanggan:** `http://localhost/sigana`
   - **Halaman Login Admin:** `http://localhost/sigana/admin/login.php`
4. **Akun Login Admin Default:**
   - **Username:** `admin`
   - **Password:** `admin123`

---

## 🎓 6. Kisi-Kisi Tanya Jawab (Q&A) untuk Sidang PKL

Saat presentasi di depan penguji sidang SMK RPL, beberapa pertanyaan teknis yang sering ditanyakan dan cara menjawabnya:

#### Q1: "Kenapa memilih menggunakan PDO dibandingkan mysqli biasa?"
> **Jawaban:**  
> *"Saya menggunakan **PDO (PHP Data Objects)** karena PDO lebih fleksibel, mendukung berbagai jenis RDBMS, dan memiliki fitur **Prepared Statements** yang secara default mencegah celah keamanan **SQL Injection**. Selain itu, PDO menggunakan penanganan error berbasis `Exception` sehingga error handling program menjadi lebih rapi dan aman."*

#### Q2: "Bagaimana sistem mengamankan password akun admin?"
> **Jawaban:**  
> *"Password admin tidak disimpan dalam bentuk teks biasa (plain text), melainkan di-hash menggunakan fungsi bawaan PHP `password_hash($password, PASSWORD_BCRYPT)` dan diverifikasi saat login dengan `password_verify()`. Ini menjamin keamanan data pengguna meskipun basis data diakses pihak yang tidak berwenang."*

#### Q3: "Bagaimana cara kerja filter pencarian kecamatan di halaman pelanggan?"
> **Jawaban:**  
> *"Pencarian dan filter kecamatan di halaman publik dibangun menggunakan JavaScript DOM manipulation. Data atribut seperti `data-kecamatan`, `data-status`, dan `data-title` disematkan pada setiap kartu pengumuman, sehingga ketika pelanggan memilih kecamatan atau mengetikkan kata kunci, antarmuka langsung menyaring tampilan secara instan tanpa perlu memuat ulang halaman (*zero page-reload*)."*

#### Q4: "Apa nilai manfaat proyek ini bagi instansi Perumda Tirta Intan Garut?"
> **Jawaban:**  
> *"Proyek ini memangkas beban pengaduan pelanggan yang menumpuk di Call Center saat terjadi pipa bocor. Pelanggan bisa langsung memantau status pengerjaan secara mandiri melalui web, membagikan info ke WhatsApp warga secara resmi, dan meningkatkan transparansi pelayanan publik Tirta Intan Garut."*
