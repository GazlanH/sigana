@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Edit Pengumuman #{{ $pengumuman->nomor_tiket }}</h1>
        <p class="text-muted small mb-0">Perbarui rincian investigasi lapangan, progres perbaikan pipa, dan estimasi waktu normalisasi.</p>
    </div>
    <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali ke Daftar</a>
</div>

<div class="card shadow-sm border mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('admin.pengumuman.update', $pengumuman->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <!-- Nomor Tiket -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="nomor_tiket">Nomor Tiket <span class="req">*</span></label>
                    <input type="text" id="nomor_tiket" name="nomor_tiket" class="form-control @error('nomor_tiket') is-invalid @enderror" value="{{ old('nomor_tiket', $pengumuman->nomor_tiket) }}" required>
                </div>

                <!-- Kecamatan Terdampak -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="kecamatan_id">Kecamatan Wilayah Pelayanan <span class="req">*</span></label>
                    <select id="kecamatan_id" name="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach ($list_kecamatan as $kec)
                            <option value="{{ $kec->id }}" {{ old('kecamatan_id', $pengumuman->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                Kecamatan {{ $kec->nama_kecamatan }} ({{ $kec->cabang_pelayanan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Judul / Perihal -->
                <div class="col-12">
                    <label class="form-label" for="judul">Judul Perihal Gangguan Air <span class="req">*</span></label>
                    <input type="text" id="judul" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $pengumuman->judul) }}" required>
                </div>

                <!-- Wilayah / Jalan Terdampak -->
                <div class="col-12">
                    <label class="form-label" for="wilayah_terdampak">Daftar Wilayah, Jalan & Perumahan Terdampak <span class="req">*</span></label>
                    <textarea id="wilayah_terdampak" name="wilayah_terdampak" class="form-control @error('wilayah_terdampak') is-invalid @enderror" rows="3" required>{{ old('wilayah_terdampak', $pengumuman->wilayah_terdampak) }}</textarea>
                </div>

                <!-- Penyebab Gangguan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="penyebab">Penyebab Gangguan <span class="req">*</span></label>
                    <textarea id="penyebab" name="penyebab" class="form-control @error('penyebab') is-invalid @enderror" rows="3" required>{{ old('penyebab', $pengumuman->penyebab) }}</textarea>
                </div>

                <!-- Tindakan Teknis Lapangan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="tindakan">Tindakan Lapangan / Langkah Perbaikan <span class="req">*</span></label>
                    <textarea id="tindakan" name="tindakan" class="form-control @error('tindakan') is-invalid @enderror" rows="3" required>{{ old('tindakan', $pengumuman->tindakan) }}</textarea>
                </div>

                <!-- Dampak Aliran Air -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="dampak_aliran">Dampak Terhadap Aliran Air <span class="req">*</span></label>
                    <select id="dampak_aliran" name="dampak_aliran" class="form-select @error('dampak_aliran') is-invalid @enderror" required>
                        <option value="mati_total" {{ old('dampak_aliran', $pengumuman->dampak_aliran) === 'mati_total' ? 'selected' : '' }}>Air Mati Total (Aliran Padam)</option>
                        <option value="aliran_kecil" {{ old('dampak_aliran', $pengumuman->dampak_aliran) === 'aliran_kecil' ? 'selected' : '' }}>Aliran Air Kecil / Debit Rendah</option>
                        <option value="bertekanan_rendah" {{ old('dampak_aliran', $pengumuman->dampak_aliran) === 'bertekanan_rendah' ? 'selected' : '' }}>Tekanan Rendah / Air Keruh Sesaat</option>
                    </select>
                </div>

                <!-- Status Penanganan -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="statusSelect">Status Penanganan <span class="req">*</span></label>
                    <select id="statusSelect" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="investigasi" {{ old('status', $pengumuman->status) === 'investigasi' ? 'selected' : '' }}>Investigasi Lapangan</option>
                        <option value="perbaikan" {{ old('status', $pengumuman->status) === 'perbaikan' ? 'selected' : '' }}>Sedang Dalam Proses Perbaikan</option>
                        <option value="normalisasi" {{ old('status', $pengumuman->status) === 'normalisasi' ? 'selected' : '' }}>Tahap Normalisasi Aliran</option>
                        <option value="selesai" {{ old('status', $pengumuman->status) === 'selesai' ? 'selected' : '' }}>Selesai Ditangani</option>
                    </select>
                </div>

                <!-- Waktu Mulai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="waktu_mulai">Waktu Mulai Gangguan <span class="req">*</span></label>
                    <input type="datetime-local" id="waktu_mulai" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', $pengumuman->waktu_mulai ? $pengumuman->waktu_mulai->format('Y-m-d\TH:i') : '') }}" required>
                </div>

                <!-- Estimasi Selesai -->
                <div class="col-12 col-md-6">
                    <label class="form-label" for="estimasiSelesai">Estimasi Selesai Normal</label>
                    <input type="datetime-local" id="estimasiSelesai" name="estimasi_selesai" class="form-control @error('estimasi_selesai') is-invalid @enderror" value="{{ old('estimasi_selesai', $pengumuman->estimasi_selesai ? $pengumuman->estimasi_selesai->format('Y-m-d\TH:i') : '') }}">
                </div>

                <!-- Kontak Posko -->
                <div class="col-12">
                    <label class="form-label" for="kontak_posko">Kontak Posko / Layanan Armada Tangki Air</label>
                    <input type="text" id="kontak_posko" name="kontak_posko" class="form-control @error('kontak_posko') is-invalid @enderror" value="{{ old('kontak_posko', $pengumuman->kontak_posko) }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
                <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary fw-semibold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
