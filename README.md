# SIGANA - Sistem Informasi Gangguan Air (Laravel Edition)

**SIGANA** adalah aplikasi web resmi Sistem Informasi Gangguan & Pemeliharaan Aliran Air untuk pelanggan **PERUMDA Air Minum Tirta Intan Garut**, yang telah dimigrasikan dan dibangun menggunakan framework **Laravel (MVC)** yang bersih, aman, dan modern.

---

## 🚀 Teknologi & Fitur Utama

- **Framework**: Laravel 10.x (PHP 8.1+)
- **Arsitektur**: Model - View - Controller (MVC) + Blade Templating
- **Database**: MySQL / MariaDB (Migrations & Seeders)
- **Otentikasi & Keamanan**:
  - Laravel Authentication (Username & Password Bcrypt)
  - CSRF Protection (`@csrf`)
  - Request Validation (`$request->validate()`)
  - Middleware Guard (`auth`)
- **Frontend**:
  - Bootstrap 5.3.3 + Google Fonts (*Plus Jakarta Sans*)
  - Real SVG Icons (Tanpa dependensi CDN eksternal)
  - Fitur Filter Pencarian Real-Time (JS)
  - Integrasi Bagikan Informasi ke WhatsApp & Cetak Pengumuman
- **Pengujian**: PHPUnit / Laravel Feature Tests (100% Passed)

---

## 🛠️ Persyaratan Sistem
- PHP >= 8.1 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer
- MySQL / MariaDB
- Server: Laragon / XAMPP / Apache / Nginx

---

## 💻 Cara Menjalankan

1. **Pastikan MySQL Aktif** di Laragon / XAMPP.
2. **Konfigurasi Database** di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_sigana
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. **Jalankan Migrasi & Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Jalankan Aplikasi**:
   - Jika menggunakan Laragon: Akses langsung `http://sigana.test` atau `http://localhost/sigana/public/`
   - Atau menggunakan dev server bawaan:
     ```bash
     php artisan serve
     ```
     dan buka `http://127.0.0.1:8000` di browser Anda.

---

## 🔑 Akun Login Admin Default

- **URL Login**: `http://127.0.0.1:8000/admin/login` (atau `/sigana/public/admin/login`)
- **Username**: `admin`
- **Password**: `admin123`

---

## 📂 Struktur Direktori Laravel

```text
sigana/
├─ app/
│  ├─ Helpers/
│  │  └─ SiganaHelper.php         – Helper SVG Icon, format tanggal Indonesia, badges
│  ├─ Http/
│  │  └─ Controllers/
│  │     ├─ PublicController.php  – Halaman beranda publik & detail tiket
│  │     └─ Admin/
│  │        ├─ AuthController.php        – Login & Logout session
│  │        ├─ DashboardController.php   – Statistik & data operasional
│  │        ├─ PengumumanController.php  – CRUD pengumuman gangguan air
│  │        └─ KecamatanController.php   – CRUD master wilayah & cabang
│  └─ Models/
│     ├─ User.php                – Model akun petugas/admin
│     ├─ Kecamatan.php           – Model data wilayah kecamatan
│     └─ Pengumuman.php          – Model tiket gangguan air + auto generate ID
├─ database/
│  ├─ migrations/                – Migrations tabel users, kecamatans, pengumumans
│  └─ seeders/DatabaseSeeder.php – Seeder akun admin, 12 kecamatan Garut, & data tiket
├─ public/
│  └─ assets/                    – CSS, JS, dan Logo resmi
├─ resources/
│  └─ views/
│     ├─ layouts/
│     │  ├─ app.blade.php        – Layout halaman publik
│     │  └─ admin.blade.php      – Layout panel admin
│     ├─ public/
│     │  ├─ index.blade.php      – Papan pengumuman publik
│     │  └─ detail.blade.php     – Rincian tiket gangguan
│     └─ admin/
│        ├─ login.blade.php      – Halaman login admin
│        ├─ dashboard.blade.php  – Dashboard admin
│        ├─ pengumuman/          – View CRUD pengumuman (index, create, edit)
│        └─ kecamatan/           – View CRUD kecamatan (index)
├─ routes/
│  └─ web.php                    – Definisi seluruh rute web
├─ tests/
│  └─ Feature/SiganaFeatureTest.php – Unit & Feature Test otomatis
└─ README.md
```

---

*Dikembangkan untuk SIGANA - PERUMDA Air Minum Tirta Intan Garut.*
