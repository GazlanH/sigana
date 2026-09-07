@extends('layouts.app')

@section('content')
<!-- Banner Judul Halaman -->
<div class="bg-white border-bottom py-3">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <h1 class="h5 fw-bold text-dark mb-1">Papan Pengumuman Gangguan Aliran Air</h1>
                <p class="text-muted small mb-0">Informasi resmi pemeliharaan pipa transmisi dan estimasi waktu normalisasi pasokan air pelanggan.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-wrap gap-2">
                    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-2">
                        {!! \App\Helpers\SiganaHelper::icon('tool') !!} Dalam Perbaikan: <strong>{{ $stat_perbaikan }}</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-2">
                        {!! \App\Helpers\SiganaHelper::icon('clock') !!} Normalisasi: <strong>{{ $stat_normalisasi }}</strong>
                    </span>
                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-2">
                        {!! \App\Helpers\SiganaHelper::icon('check') !!} Selesai: <strong>{{ $stat_selesai }}</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Pencarian Controls -->
<div class="container my-3">
    <div class="card shadow-sm border">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Search Input -->
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            {!! \App\Helpers\SiganaHelper::icon('search') !!}
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama jalan, perumahan, atau nomor tiket...">
                    </div>
                </div>

                <!-- Select Kecamatan -->
                <div class="col-md-4">
                    <select id="filterKecamatan" class="form-select">
                        <option value="">Semua Wilayah Kecamatan</option>
                        @foreach ($daftar_kecamatan as $kec)
                            <option value="{{ strtolower($kec->nama_kecamatan) }}">
                                Kecamatan {{ $kec->nama_kecamatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter Pills -->
                <div class="col-md-3 text-md-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn active" data-tab="aktif">Aktif ({{ $stat_aktif }})</button>
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="selesai">Selesai ({{ $stat_selesai }})</button>
                        <button type="button" class="btn btn-outline-primary btn-sm tab-nav-btn" data-tab="semua">Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Kartu Pengumuman -->
<div class="container my-3">
    <!-- State Kosong jika tidak ada data sama sekali -->
    @if($pengumuman_list->isEmpty())
        <div class="card shadow-sm border text-center py-5">
            <div class="card-body">
                <div class="display-4 text-muted mb-3">{!! \App\Helpers\SiganaHelper::icon('check') !!}</div>
                <h2 class="h5 fw-bold text-dark mb-1">Tidak Ada Gangguan Air Tercatat</h2>
                <p class="text-muted small mb-0">Seluruh pasokan aliran air pelanggan Perumda Tirta Intan Garut dalam kondisi normal.</p>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3" id="noticesContainer">
            @foreach ($pengumuman_list as $row)
                <div class="card shadow-sm border bulletin-card card-{{ strtolower($row->status) }}"
                     data-ticket="{{ $row->nomor_tiket }}"
                     data-title="{{ $row->judul }}"
                     data-kecamatan="{{ strtolower($row->kecamatan->nama_kecamatan ?? '') }}"
                     data-wilayah="{{ $row->wilayah_terdampak }}"
                     data-status="{{ strtolower($row->status) }}">
                    
                    <div class="card-body p-3 p-md-4">
                        <!-- Baris Atas: Nomor Tiket, Badge Status & Dampak -->
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="ticket-tag">#{{ $row->nomor_tiket }}</span>
                                {!! \App\Helpers\SiganaHelper::renderStatusBadge($row->status) !!}
                                {!! \App\Helpers\SiganaHelper::renderDampakBadge($row->dampak_aliran) !!}
                            </div>
                            <div class="small text-muted fw-semibold">
                                {!! \App\Helpers\SiganaHelper::icon('pin') !!} Kec. {{ $row->kecamatan->nama_kecamatan ?? '-' }} ({{ $row->kecamatan->cabang_pelayanan ?? '-' }})
                            </div>
                        </div>

                        <!-- Judul Gangguan -->
                        <h2 class="h5 fw-bold text-dark mb-2">
                            <a href="{{ route('public.detail', $row->nomor_tiket) }}" class="text-dark text-decoration-none hover-primary">
                                {{ $row->judul }}
                            </a>
                        </h2>

                        <!-- Blok Wilayah Terdampak -->
                        <div class="bulletin-area-box mb-3">
                            <span class="fw-bold">{!! \App\Helpers\SiganaHelper::icon('pin') !!} Wilayah Terdampak:</span>
                            <span class="text-dark">{{ $row->wilayah_terdampak }}</span>
                        </div>

                        <!-- Grid Mini: Penyebab & Tindakan -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="bulletin-mini-box cause h-100">
                                    <div class="bulletin-mini-label">{!! \App\Helpers\SiganaHelper::icon('warning') !!} Penyebab:</div>
                                    <div class="text-secondary">{{ \Illuminate\Support\Str::limit($row->penyebab, 140) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bulletin-mini-box action h-100">
                                    <div class="bulletin-mini-label">{!! \App\Helpers\SiganaHelper::icon('tool') !!} Tindakan:</div>
                                    <div class="text-secondary">{{ \Illuminate\Support\Str::limit($row->tindakan, 140) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Bawah: Waktu, Estimasi & Tombol Rincian -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                            <div class="d-flex align-items-center flex-wrap gap-2 small">
                                <span class="text-muted">
                                    {!! \App\Helpers\SiganaHelper::icon('clock') !!} Mulai: <strong>{{ \App\Helpers\SiganaHelper::formatTanggalIndo($row->waktu_mulai, true) }}</strong>
                                </span>
                                &bull;
                                <span class="info-block-eta {{ $row->status === 'selesai' ? 'selesai' : '' }}">
                                    @if($row->status === 'selesai')
                                        {!! \App\Helpers\SiganaHelper::icon('check') !!} <strong>Pekerjaan Selesai (Normal)</strong>
                                    @else
                                        {!! \App\Helpers\SiganaHelper::icon('clock') !!} Est. Selesai: <strong>{{ \App\Helpers\SiganaHelper::formatTanggalIndo($row->estimasi_selesai, true) }}</strong>
                                    @endif
                                </span>
                            </div>

                            <div class="d-flex gap-2 bulletin-action-btns">
                                <button type="button" class="btn btn-outline-success btn-sm fw-semibold"
                                    onclick="shareToWA(
                                        '{{ $row->nomor_tiket }}',
                                        '{{ addslashes($row->judul) }}',
                                        '{{ addslashes($row->kecamatan->nama_kecamatan ?? '') }}',
                                        '{{ addslashes($row->wilayah_terdampak) }}',
                                        '{{ addslashes($row->status) }}',
                                        '{{ addslashes(\App\Helpers\SiganaHelper::formatTanggalIndo($row->estimasi_selesai, true)) }}'
                                    )"
                                >
                                    {!! \App\Helpers\SiganaHelper::icon('whatsapp') !!} Bagikan
                                </button>
                                <a href="{{ route('public.detail', $row->nomor_tiket) }}" class="btn btn-primary btn-sm fw-semibold">
                                    Rincian Tiket &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State Filter Real-Time JS -->
        <div id="emptyState" class="card shadow-sm border text-center py-5 mt-3" style="display: none;">
            <div class="card-body">
                <div class="display-5 text-muted mb-2">{!! \App\Helpers\SiganaHelper::icon('search') !!}</div>
                <h3 class="h6 fw-bold text-dark mb-1">Pengumuman Tidak Ditemukan</h3>
                <p class="text-muted small mb-0">Tidak ada info gangguan yang cocok dengan pencarian kata kunci atau filter wilayah.</p>
            </div>
        </div>
    @endif
</div>
@endsection
