# SIGANA - Sistem Informasi Gangguan Air

SIGANA adalah aplikasi web yang menampilkan informasi gangguan air untuk pelanggan PERUMDA Tirta Intan Garut.

## Teknologi
- Backend: PHP (PDO)
- Database: MySQL / MariaDB
- Frontend: HTML5, CSS (Bootstrap 5.3.3), JavaScript
- Server: Apache (Laragon / XAMPP)

## Cara Menjalankan
1. Pastikan Apache dan MySQL sudah berjalan.
2. Buka browser dan akses:
   - Publik: `http://localhost/sigana/`
   - Admin: `http://localhost/sigana/admin/login.php`
3. Login admin default: `admin / admin123` (ubah password setelah login).

## Struktur Direktori
```
sigana/
├─ assets/
│  ├─ css/      – style.css, admin.css
│  └─ js/       – main.js, admin.js
├─ config/       – database.php
├─ includes/     – header/footer, admin_header/footer, functions.php
├─ admin/        – dashboard, login, CRUD pages
├─ index.php     – halaman publik
├─ detail.php    – detail gangguan
├─ database.sql  – skema database
└─ README.md    – dokumentasi ini
```

Aplikasi siap dipresentasikan pada sidang PKL.
