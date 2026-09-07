<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;

class DashboardController extends Controller
{
    /**
     * Dashboard operasional admin
     */
    public function index()
    {
        $pageTitle = "Dashboard Operasional";

        $total_semua = Pengumuman::count();
        $total_perbaikan = Pengumuman::where('status', 'perbaikan')->count();
        $total_investigasi = Pengumuman::where('status', 'investigasi')->count();
        $total_normalisasi = Pengumuman::where('status', 'normalisasi')->count();
        $total_selesai = Pengumuman::where('status', 'selesai')->count();

        $recent_list = Pengumuman::with('kecamatan')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'pageTitle',
            'total_semua',
            'total_perbaikan',
            'total_investigasi',
            'total_normalisasi',
            'total_selesai',
            'recent_list'
        ));
    }
}
