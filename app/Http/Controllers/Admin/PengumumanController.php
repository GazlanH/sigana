<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Tampilkan semua pengumuman dengan filter
     */
    public function index(Request $request)
    {
        $pageTitle = "Kelola Pengumuman";

        $status_filter = $request->input('status', '');
        $kecamatan_filter = $request->input('kecamatan_id', '');
        $q = $request->input('q', '');

        $query = Pengumuman::with(['kecamatan', 'creator']);

        if (!empty($status_filter)) {
            $query->where('status', $status_filter);
        }

        if (!empty($kecamatan_filter)) {
            $query->where('kecamatan_id', $kecamatan_filter);
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'LIKE', "%{$q}%")
                    ->orWhere('nomor_tiket', 'LIKE', "%{$q}%")
                    ->orWhere('wilayah_terdampak', 'LIKE', "%{$q}%")
                    ->orWhere('penyebab', 'LIKE', "%{$q}%");
            });
        }

        $list_pengumuman = $query->orderBy('id', 'desc')->get();
        $kecamatan_options = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();

        return view('admin.pengumuman.index', compact(
            'pageTitle',
            'list_pengumuman',
            'kecamatan_options',
            'status_filter',
            'kecamatan_filter',
            'q'
        ));
    }

    /**
     * Form tambah pengumuman baru
     */
    public function create()
    {
        $pageTitle = "Buat Pengumuman Baru";
        $nomor_tiket_auto = Pengumuman::generateNomorTiket();
        $list_kecamatan = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();

        return view('admin.pengumuman.create', compact('pageTitle', 'nomor_tiket_auto', 'list_kecamatan'));
    }

    /**
     * Simpan pengumuman baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_tiket' => ['required', 'string', 'max:30', 'unique:pengumumans,nomor_tiket'],
            'judul' => ['required', 'string', 'max:200'],
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'wilayah_terdampak' => ['required', 'string'],
            'penyebab' => ['required', 'string'],
            'tindakan' => ['required', 'string'],
            'dampak_aliran' => ['required', 'in:mati_total,aliran_kecil,bertekanan_rendah'],
            'status' => ['required', 'in:investigasi,perbaikan,normalisasi,selesai'],
            'waktu_mulai' => ['required', 'date'],
            'estimasi_selesai' => ['nullable', 'date'],
            'kontak_posko' => ['nullable', 'string', 'max:50'],
        ], [
            'nomor_tiket.required' => 'Nomor tiket wajib diisi.',
            'nomor_tiket.unique' => 'Nomor tiket ini sudah pernah digunakan.',
            'judul.required' => 'Judul perihal gangguan wajib diisi.',
            'kecamatan_id.required' => 'Pilih kecamatan wilayah terdampak.',
            'wilayah_terdampak.required' => 'Rincian wilayah/jalan terdampak wajib diisi.',
            'penyebab.required' => 'Penyebab gangguan wajib diisi.',
            'tindakan.required' => 'Langkah tindakan teknis lapangan wajib diisi.',
            'waktu_mulai.required' => 'Waktu mulai kejadian gangguan wajib ditentukan.',
        ]);

        $validated['created_by'] = Auth::id();

        Pengumuman::create($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', "Pengumuman #{$request->nomor_tiket} berhasil dipublikasikan!");
    }

    /**
     * Form edit pengumuman
     */
    public function edit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pageTitle = "Edit Pengumuman #" . $pengumuman->nomor_tiket;
        $list_kecamatan = Kecamatan::orderBy('nama_kecamatan', 'asc')->get();

        return view('admin.pengumuman.edit', compact('pageTitle', 'pengumuman', 'list_kecamatan'));
    }

    /**
     * Update data pengumuman
     */
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'nomor_tiket' => ['required', 'string', 'max:30', 'unique:pengumumans,nomor_tiket,' . $pengumuman->id],
            'judul' => ['required', 'string', 'max:200'],
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'wilayah_terdampak' => ['required', 'string'],
            'penyebab' => ['required', 'string'],
            'tindakan' => ['required', 'string'],
            'dampak_aliran' => ['required', 'in:mati_total,aliran_kecil,bertekanan_rendah'],
            'status' => ['required', 'in:investigasi,perbaikan,normalisasi,selesai'],
            'waktu_mulai' => ['required', 'date'],
            'estimasi_selesai' => ['nullable', 'date'],
            'waktu_selesai_aktual' => ['nullable', 'date'],
            'kontak_posko' => ['nullable', 'string', 'max:50'],
        ]);

        // Jika status diubah jadi selesai dan waktu_selesai_aktual kosong, isi otomatis
        if ($validated['status'] === 'selesai' && empty($validated['waktu_selesai_aktual'])) {
            $validated['waktu_selesai_aktual'] = now();
        }

        $pengumuman->update($validated);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', "Perubahan Pengumuman #{$pengumuman->nomor_tiket} berhasil disimpan!");
    }

    /**
     * Hapus pengumuman
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $tiket = $pengumuman->nomor_tiket;
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', "Pengumuman #{$tiket} berhasil dihapus.");
    }
}
