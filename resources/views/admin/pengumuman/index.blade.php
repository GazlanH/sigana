@extends('layouts.admin')

@section('content')
<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Daftar Seluruh Pengumuman</h1>
        <p class="text-muted small mb-0">Kelola info pemeliharaan pipa, update status teknis, dan estimasi waktu normalisasi.</p>
    </div>
    <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary fw-semibold shadow-sm">
        {!! \App\Helpers\SiganaHelper::icon('tool') !!} + Buat Pengumuman Baru
    </a>
</div>

<!-- Filter Box -->
<div class="card shadow-sm border mb-3">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.pengumuman.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari judul, nomor tiket, atau jalan..." value="{{ $q }}">
            </div>

            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="investigasi" {{ $status_filter === 'investigasi' ? 'selected' : '' }}>Investigasi</option>
                    <option value="perbaikan" {{ $status_filter === 'perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                    <option value="normalisasi" {{ $status_filter === 'normalisasi' ? 'selected' : '' }}>Normalisasi</option>
                    <option value="selesai" {{ $status_filter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select name="kecamatan_id" class="form-select form-select-sm">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($kecamatan_options as $kec)
                        <option value="{{ $kec->id }}" {{ $kecamatan_filter == $kec->id ? 'selected' : '' }}>
                            Kec. {{ $kec->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-secondary btn-sm flex-fill">
                    {!! \App\Helpers\SiganaHelper::icon('search') !!} Filter
                </button>
                @if(!empty($q) || !empty($status_filter) || !empty($kecamatan_filter))
                    <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-danger btn-sm" title="Reset Filter">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tiket</th>
                        <th style="min-width: 220px;">Perihal & Wilayah Garut</th>
                        <th style="min-width: 120px;">Dampak</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 140px;">Waktu & Estimasi</th>
                        <th style="min-width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($list_pengumuman as $row)
                        <tr>
                            <td>
                                <span class="ticket-tag">#{{ $row->nomor_tiket }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->judul }}</div>
                                <div class="small text-muted">
                                    <strong>Kec. {{ $row->kecamatan->nama_kecamatan ?? '-' }}</strong> &bull; {{ \Illuminate\Support\Str::limit($row->wilayah_terdampak, 45) }}
                                </div>
                            </td>
                            <td>
                                {!! \App\Helpers\SiganaHelper::renderDampakBadge($row->dampak_aliran) !!}
                            </td>
                            <td>
                                {!! \App\Helpers\SiganaHelper::renderStatusBadge($row->status) !!}
                            </td>
                            <td>
                                <div class="small">
                                    Mulai: {{ \App\Helpers\SiganaHelper::formatTanggalIndo($row->waktu_mulai, false) }}<br>
                                    <span class="fw-bold {{ $row->status === 'selesai' ? 'text-success' : 'text-danger' }}">
                                        {{ $row->status === 'selesai' ? 'Selesai' : 'Est: ' . \App\Helpers\SiganaHelper::formatTanggalIndo($row->estimasi_selesai, true) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('public.detail', $row->nomor_tiket) }}" target="_blank" class="btn btn-outline-secondary btn-sm btn-action-touch" title="Pratinjau">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.pengumuman.edit', $row->id) }}" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pengumuman.destroy', $row->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="pengumuman #{{ $row->nomor_tiket }}" title="Hapus">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                Tidak ada data pengumuman yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
