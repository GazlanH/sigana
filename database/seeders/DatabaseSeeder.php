<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Pengumuman;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User Admin Default
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'nama_lengkap' => 'Gazlan Pratama (Admin PKL)',
                'jabatan' => 'Staff Humas & Pengaduan Pelanggan',
                'role' => 'admin',
            ]
        );

        // 2. Daftar Kecamatan Wilayah Pelayanan Perumda Tirta Intan Garut
        $kecamatans = [
            ['id' => 1, 'nama_kecamatan' => 'Garut Kota', 'cabang_pelayanan' => 'Cabang Garut Kota'],
            ['id' => 2, 'nama_kecamatan' => 'Tarogong Kidul', 'cabang_pelayanan' => 'Cabang Tarogong'],
            ['id' => 3, 'nama_kecamatan' => 'Tarogong Kaler', 'cabang_pelayanan' => 'Cabang Tarogong'],
            ['id' => 4, 'nama_kecamatan' => 'Karangpawitan', 'cabang_pelayanan' => 'Cabang Karangpawitan'],
            ['id' => 5, 'nama_kecamatan' => 'Cilawu', 'cabang_pelayanan' => 'Cabang Cilawu'],
            ['id' => 6, 'nama_kecamatan' => 'Samarang', 'cabang_pelayanan' => 'Cabang Samarang'],
            ['id' => 7, 'nama_kecamatan' => 'Banyuresmi', 'cabang_pelayanan' => 'Cabang Banyuresmi'],
            ['id' => 8, 'nama_kecamatan' => 'Bayongbong', 'cabang_pelayanan' => 'Cabang Bayongbong'],
            ['id' => 9, 'nama_kecamatan' => 'Leles', 'cabang_pelayanan' => 'Cabang Leles'],
            ['id' => 10, 'nama_kecamatan' => 'Kadungora', 'cabang_pelayanan' => 'Cabang Kadungora'],
            ['id' => 11, 'nama_kecamatan' => 'Wanaraja', 'cabang_pelayanan' => 'Cabang Wanaraja'],
            ['id' => 12, 'nama_kecamatan' => 'Limbangan', 'cabang_pelayanan' => 'Cabang Limbangan'],
        ];

        foreach ($kecamatans as $item) {
            Kecamatan::updateOrCreate(
                ['id' => $item['id']],
                [
                    'nama_kecamatan' => $item['nama_kecamatan'],
                    'cabang_pelayanan' => $item['cabang_pelayanan'],
                ]
            );
        }

        // 3. Data Dummy / Realistis Pengumuman Gangguan
        $now = Carbon::now();

        $pengumumans = [
            [
                'id' => 1,
                'nomor_tiket' => 'GNG-202608-001',
                'judul' => 'Perbaikan Kebocoran Pipa Distribusi Utama HDPE 250mm',
                'kecamatan_id' => 2,
                'wilayah_terdampak' => 'Jl. Patriot, Perumahan Gordah, Perumahan Pemda, dan sekitarnya',
                'penyebab' => 'Pipa distribusi transmisi utama mengalami kebocoran akibat tingginya tekanan air dan pergeseran tanah.',
                'tindakan' => 'Tim Transmisi dan Distribusi (Trandis) sedang melakukan penggalian, pemotongan segmen pipa rusak, dan penyambungan mechanical joint.',
                'dampak_aliran' => 'mati_total',
                'status' => 'perbaikan',
                'waktu_mulai' => $now->copy()->subHours(2),
                'estimasi_selesai' => $now->copy()->addHours(4),
                'kontak_posko' => '0811-2000-1122',
                'created_by' => $admin->id,
            ],
            [
                'id' => 2,
                'nomor_tiket' => 'GNG-202608-002',
                'judul' => 'Pemeliharaan Rutin & Pengurasan Bak Sedimentasi IPA Cikembar',
                'kecamatan_id' => 1,
                'wilayah_terdampak' => 'Kecamatan Garut Kota (Kel. Paminggir, Kel. Pakuwon, Kel. Muarasanding, Jl. Cimanuk sebagian)',
                'penyebab' => 'Pemeliharaan berkala unit sedimentasi dan filtrasi guna menjaga kualitas kejernihan air bersih menjelang musim penghujan.',
                'tindakan' => 'Petugas Water Treatment Plant (WTP) melakukan flushing lumpur dan kalibrasi klorinasi.',
                'dampak_aliran' => 'aliran_kecil',
                'status' => 'investigasi',
                'waktu_mulai' => $now->copy()->subHour(),
                'estimasi_selesai' => $now->copy()->addHours(5),
                'kontak_posko' => '0811-2000-1123',
                'created_by' => $admin->id,
            ],
            [
                'id' => 3,
                'nomor_tiket' => 'GNG-202608-003',
                'judul' => 'Gangguan Pompa Intake Akibat Fluktuasi Aliran Listrik PLN',
                'kecamatan_id' => 4,
                'wilayah_terdampak' => 'Desa Cimurah, Desa Situgede, Perumahan Graha Mandala Karangpawitan',
                'penyebab' => 'Gangguan pasokan tegangan listrik di gardu penyuplai intake mata air cipancar sehingga pompa otomatis mati.',
                'tindakan' => 'Koordinasi dengan tim teknis PLN Rayon Garut Kota dan pengoperasian genset darurat Perumda.',
                'dampak_aliran' => 'bertekanan_rendah',
                'status' => 'normalisasi',
                'waktu_mulai' => $now->copy()->subHours(5),
                'estimasi_selesai' => $now->copy()->addHour(),
                'kontak_posko' => '0811-2000-1124',
                'created_by' => $admin->id,
            ],
            [
                'id' => 4,
                'nomor_tiket' => 'GNG-202608-004',
                'judul' => 'Pembersihan Sumbatan Sampah di Intake Sungai Cimanuk',
                'kecamatan_id' => 3,
                'wilayah_terdampak' => 'Kp. Rancabango, Pasawahan, dan Jl. Otista Tarogong Kaler',
                'penyebab' => 'Peningkatan volume sampah ranting pohon di kisi saringan intake pasca hujan deras.',
                'tindakan' => 'Pembersihan manual kisi saringan (trash screen) dan penggelontoran lumpur.',
                'dampak_aliran' => 'mati_total',
                'status' => 'selesai',
                'waktu_mulai' => $now->copy()->subHours(24),
                'estimasi_selesai' => $now->copy()->subHours(18),
                'kontak_posko' => '0811-2000-1122',
                'created_by' => $admin->id,
            ],
        ];

        foreach ($pengumumans as $p) {
            Pengumuman::updateOrCreate(
                ['nomor_tiket' => $p['nomor_tiket']],
                $p
            );
        }
    }
}
