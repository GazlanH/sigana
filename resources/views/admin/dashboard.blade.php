@extends('layouts.admin')

@section('content')
<!-- Header Title & Quick Button -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Dashboard Operasional</h1>
        <p class="text-muted small mb-0">Ringkasan status pemeliharaan dan pengumuman gangguan air di Garut.</p>
    </div>
    <a href="{{ route('admin.pengumuman.create') }}" class="btn btn-primary fw-semibold shadow-sm">
        {!! \App\Helpers\SiganaHelper::icon('tool') !!} + Buat Pengumuman Baru
    </a>
</div>

<!-- Metric Stat Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Total Info</div>
                <div class="h3 fw-bold text-dark mb-0 mt-1">{{ $total_semua }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-danger h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Perbaikan</div>
                <div class="h3 fw-bold text-danger mb-0 mt-1">{{ $total_perbaikan }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-warning h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Investigasi / Normal</div>
                <div class="h3 fw-bold text-warning-emphasis mb-0 mt-1">{{ $total_normalisasi + $total_investigasi }}</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card shadow-sm border admin-stat-card c-success h-100">
            <div class="card-body p-3">
                <div class="text-muted small text-uppercase fw-bold">Selesai</div>
                <div class="h3 fw-bold text-success mb-0 mt-1">{{ $total_selesai }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notices Card & Table -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="h6 fw-bold mb-0 text-dark">Pengumuman Gangguan Air Terbaru</h2>
        <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-secondary btn-sm">
            Lihat Semua ({{ $total_semua }})
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tiket</th>
                        <th style="min-width: 200px;">Perihal Gangguan</th>
                        <th style="min-width: 160px;">Wilayah Garut</th>
                        <th style="min-width: 130px;">Waktu Mulai</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent_list as $row)
                        <tr>
                            <td>
                                <span class="ticket-tag">#{{ $row->nomor_tiket }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->judul }}</div>
                                <div class="text-muted small">{!! \Illuminate\Support\Str::limit($row->wilayah_terdampak, 45) !!}</div>
                            </td>
                            <td>
                                <span class="fw-semibold">Kec. {{ $row->kecamatan->nama_kecamatan ?? '-' }}</span>
                                <div class="text-muted small">{{ $row->kecamatan->cabang_pelayanan ?? '-' }}</div>
                            </td>
                            <td class="small">
                                {{ \App\Helpers\SiganaHelper::formatTanggalIndo($row->waktu_mulai, false) }}
                            </td>
                            <td>
                                {!! \App\Helpers\SiganaHelper::renderStatusBadge($row->status) !!}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('public.detail', $row->nomor_tiket) }}" target="_blank" class="btn btn-outline-secondary btn-sm btn-action-touch" title="Pratinjau">
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.pengumuman.edit', $row->id) }}" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Belum ada data pengumuman yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
