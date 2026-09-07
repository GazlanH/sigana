<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Halaman Utama Papan Pengumuman Publik
     */
    public function index(Request $request)
    {
        $pageTitle = "Papan Pengumuman Gangguan Air";

        // Statistik Ringkas
        $stat_aktif = Pengumuman::where('status', '!=', 'selesai')->count();
        $stat_perbaikan = Pengumuman::where('status', 'perbaikan')->count();
        $stat_normalisasi = Pengumuman::where('status', 'normalisasi')->count();
        $stat_selesai = Pengumuman::where('status', 'selesai')->count();

        // Ambil daftar kecamatan
        $daftar_kecamatan = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();

        // Ambil pengumuman dengan relasi kecamatan & user creator
        $pengumuman_list = Pengumuman::with(['kecamatan', 'creator'])
            ->orderByRaw("
                CASE 
                    WHEN status = 'perbaikan' THEN 1
                    WHEN status = 'investigasi' THEN 2
                    WHEN status = 'normalisasi' THEN 3
                    ELSE 4
                END, id DESC
            ")
            ->get();

        return view('public.index', compact(
            'pageTitle',
            'stat_aktif',
            'stat_perbaikan',
            'stat_normalisasi',
            'stat_selesai',
            'daftar_kecamatan',
            'pengumuman_list'
        ));
    }

    /**
     * Halaman Rincian Detail Tiket Pengumuman
     */
    public function show($tiket)
    {
        $detail = Pengumuman::with(['kecamatan', 'creator'])
            ->where('nomor_tiket', $tiket)
            ->firstOrFail();

        $pageTitle = "Rincian Tiket #" . $detail->nomor_tiket;

        return view('public.detail', compact('pageTitle', 'detail'));
    }
}
