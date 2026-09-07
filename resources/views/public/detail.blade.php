@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="mb-3">
        <a href="{{ route('public.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">&larr; Kembali ke Papan Pengumuman</a>
    </div>

    <div class="card shadow-sm border bulletin-card card-{{ strtolower($detail->status) }} overflow-hidden">
        <!-- Top Info Header -->
        <div class="card-header bg-light p-3 p-md-4 border-bottom">
            <div class="d-flex gap-2 flex-wrap align-items-center mb-2">
                <span class="ticket-tag">#{{ $detail->nomor_tiket }}</span>
                {!! \App\Helpers\SiganaHelper::renderStatusBadge($detail->status) !!}
                {!! \App\Helpers\SiganaHelper::renderDampakBadge($detail->dampak_aliran) !!}
            </div>
            <h1 class="h4 fw-bold text-dark mb-1">
                {{ $detail->judul }}
            </h1>
            <div class="small text-muted">
                Wilayah Pelayanan: <strong>Kecamatan {{ $detail->kecamatan->nama_kecamatan ?? '-' }} ({{ $detail->kecamatan->cabang_pelayanan ?? '-' }})</strong>
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <!-- Blok Wilayah (Area Terdampak) -->
            <div class="bulletin-area-box mb-3 p-3">
                <div class="fw-bold mb-1" style="color: #0369a1;">{!! \App\Helpers\SiganaHelper::icon('pin') !!} DAFTAR WILAYAH & JALAN TERDAMPAK</div>
                <div class="text-dark">
                    {!! nl2br(e($detail->wilayah_terdampak)) !!}
                </div>
            </div>

            <!-- Grid Penyebab & Tindakan -->
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="bulletin-mini-box cause p-3 h-100">
                        <div class="bulletin-mini-label mb-2">{!! \App\Helpers\SiganaHelper::icon('warning') !!} PENYEBAB GANGGUAN</div>
                        <div class="text-secondary">{!! nl2br(e($detail->penyebab)) !!}</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bulletin-mini-box action p-3 h-100">
                        <div class="bulletin-mini-label mb-2">{!! \App\Helpers\SiganaHelper::icon('tool') !!} TINDAKAN LAPANGAN</div>
                        <div class="text-secondary">{!! nl2br(e($detail->tindakan)) !!}</div>
                    </div>
                </div>
            </div>

            <!-- Baris Waktu & Posko Tangki -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <div class="bg-light border rounded p-2">
                        <div class="small text-muted text-uppercase fw-bold">{!! \App\Helpers\SiganaHelper::icon('clock') !!} Waktu Mulai:</div>
                        <div class="fw-semibold text-dark">{{ \App\Helpers\SiganaHelper::formatTanggalIndo($detail->waktu_mulai, true) }}</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-block-eta {{ $detail->status === 'selesai' ? 'selesai' : '' }} w-100 d-block p-2">
                        <div class="small text-uppercase fw-bold">
                            @if($detail->status === 'selesai')
                                {!! \App\Helpers\SiganaHelper::icon('check') !!} Status:
                            @else
                                {!! \App\Helpers\SiganaHelper::icon('clock') !!} Estimasi Normal:
                            @endif
                        </div>
                        <div class="fw-bold">
                            @if($detail->status === 'selesai')
                                Pekerjaan Selesai (Aliran Normal)
                            @else
                                {{ \App\Helpers\SiganaHelper::formatTanggalIndo($detail->estimasi_selesai, true) }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="bg-success-subtle text-success-emphasis border border-success-subtle rounded p-2">
                        <div class="small text-uppercase fw-bold">{!! \App\Helpers\SiganaHelper::icon('phone') !!} Posko Tangki Darurat:</div>
                        <div class="fw-bold">
                            <a href="tel:{{ $detail->kontak_posko }}" class="text-success-emphasis text-decoration-none">{{ $detail->kontak_posko }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Imbauan -->
            <div class="alert alert-warning py-2 px-3 small mb-4">
                <strong>Imbauan Pelanggan:</strong> Pelanggan diimbau untuk menampung air secukupnya jika aliran masih mengalir kecil. Tim teknis berupaya semaksimal mungkin menuntaskan perbaikan jaringan pipa.
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2 flex-wrap pt-3 border-top">
                <button type="button" class="btn btn-success fw-semibold"
                    onclick="shareToWA(
                        '{{ $detail->nomor_tiket }}',
                        '{{ addslashes($detail->judul) }}',
                        '{{ addslashes($detail->kecamatan->nama_kecamatan ?? '') }}',
                        '{{ addslashes($detail->wilayah_terdampak) }}',
                        '{{ addslashes($detail->status) }}',
                        '{{ addslashes(\App\Helpers\SiganaHelper::formatTanggalIndo($detail->estimasi_selesai, true)) }}'
                    )"
                >
                    {!! \App\Helpers\SiganaHelper::icon('whatsapp') !!} Bagikan ke WhatsApp
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="window.print()">
                    Cetak Lembar Pengumuman
                </button>
                <button type="button" class="btn btn-outline-secondary fw-semibold" onclick="copyLink('{{ $detail->nomor_tiket }}')">
                    Salin Tautan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
