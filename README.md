# SIGANA - Sistem Informasi Gangguan Air

SIGANA adalah aplikasi web untuk mempublikasikan informasi gangguan dan pemeliharaan aliran air bersih kepada pelanggan PERUMDA Air Minum Tirta Intan Kabupaten Garut secara transparan dan cepat.

Aplikasi ini dibuat dan dikembangkan sebagai bagian dari project Praktik Kerja Lapangan (PKL) di PERUMDA Tirta Intan Garut.

## Fitur Utama
- **Papan Pengumuman Publik**: Pelanggan dapat melihat status perbaikan pipa, area/jalan terdampak, dan estimasi waktu normalisasi aliran air secara real-time.
- **Pencarian & Filter Wilayah**: Memudahkan pencarian berdasarkan nomor tiket, jalan, atau kecamatan.
- **Integrasi WhatsApp**: Memudahkan masyarakat membagikan info gangguan langsung ke grup/kontak WhatsApp.
- **Panel Admin Petugas**: Manajemen input pengumuman gangguan, update progres teknis di lapangan, dan master data kecamatan.

## Teknologi
- Framework: Laravel (PHP)
- Database: MySQL
- Frontend: Bootstrap 5, Vanilla JS, CSS
- Web Server: Apache / Laragon

## Cara Menjalankan
1. Clone repository ini:
   ```bash
   git clone https://github.com/GazlanH/sigana.git
   ```
2. Masuk ke direktori dan install dependency:
   ```bash
   composer install
   ```
3. Salin file `.env` dan generate key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Sesuaikan konfigurasi database di file `.env`, lalu jalankan migrasi & seeder:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server:
   ```bash
   php artisan serve
   ```
   Akses di browser `http://127.0.0.1:8000`.

## Akun Default Admin
- **Username**: `admin`
- **Password**: `admin123`
