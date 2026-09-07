<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    /**
     * Daftar kecamatan + form tambah
     */
    public function index()
    {
        $pageTitle = "Master Wilayah Kecamatan";

        $list_kecamatan = Kecamatan::withCount([
            'pengumumans as total_pengumuman',
            'pengumumans as gangguan_aktif' => function ($query) {
                $query->where('status', '!=', 'selesai');
            }
        ])
        ->orderBy('nama_kecamatan', 'asc')
        ->get();

        return view('admin.kecamatan.index', compact('pageTitle', 'list_kecamatan'));
    }

    /**
     * Simpan kecamatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kecamatan' => ['required', 'string', 'max:100', 'unique:kecamatans,nama_kecamatan'],
            'cabang_pelayanan' => ['required', 'string', 'max:100'],
        ], [
            'nama_kecamatan.required' => 'Nama kecamatan wajib diisi.',
            'nama_kecamatan.unique' => 'Kecamatan ini sudah terdaftar sebelumnya.',
            'cabang_pelayanan.required' => 'Nama cabang pelayanan wajib diisi.',
        ]);

        Kecamatan::create($validated);

        return redirect()->route('admin.kecamatan.index')
            ->with('success', "Kecamatan {$request->nama_kecamatan} berhasil ditambahkan!");
    }

    /**
     * Hapus data kecamatan
     */
    public function destroy($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);

        if ($kecamatan->pengumumans()->count() > 0) {
            return redirect()->route('admin.kecamatan.index')
                ->with('error', "Kecamatan tidak dapat dihapus karena terdapat riwayat pengumuman terkait.");
        }

        $nama = $kecamatan->nama_kecamatan;
        $kecamatan->delete();

        return redirect()->route('admin.kecamatan.index')
            ->with('success', "Kecamatan {$nama} berhasil dihapus.");
    }
}
