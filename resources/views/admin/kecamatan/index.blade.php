@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h1 class="h4 fw-bold text-dark mb-1">Kelola Wilayah & Cabang Pelayanan</h1>
        <p class="text-muted small mb-0">Daftar kecamatan di wilayah operasional Perumda Tirta Intan Garut.</p>
    </div>
</div>

<div class="row g-3 align-items-start mb-4">
    <!-- Form Tambah Kecamatan (Left Column) -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h2 class="h6 fw-bold mb-0 text-dark">{!! \App\Helpers\SiganaHelper::icon('pin') !!} Tambah Kecamatan Baru</h2>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('admin.kecamatan.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="nama_kecamatan">Nama Kecamatan <span class="req">*</span></label>
                        <input type="text" id="nama_kecamatan" name="nama_kecamatan" class="form-control @error('nama_kecamatan') is-invalid @enderror" placeholder="Contoh: Cisurupan" value="{{ old('nama_kecamatan') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="cabang_pelayanan">Kantor Cabang Pelayanan <span class="req">*</span></label>
                        <input type="text" id="cabang_pelayanan" name="cabang_pelayanan" class="form-control @error('cabang_pelayanan') is-invalid @enderror" placeholder="Contoh: Cabang Bayongbong" value="{{ old('cabang_pelayanan') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        Simpan Kecamatan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Kecamatan (Right Column) -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h2 class="h6 fw-bold mb-0 text-dark">Daftar Kecamatan Pelayanan ({{ $list_kecamatan->count() }})</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Kecamatan</th>
                                <th>Cabang Pelayanan</th>
                                <th>Status Gangguan</th>
                                <th class="text-end" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($list_kecamatan as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold text-dark">Kec. {{ $item->nama_kecamatan }}</td>
                                    <td class="text-muted small">{{ $item->cabang_pelayanan }}</td>
                                    <td>
                                        @if ($item->gangguan_aktif > 0)
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle">{{ $item->gangguan_aktif }} Aktif</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Aman (0)</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.kecamatan.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm btn-action-touch confirm-delete" data-item="Kecamatan {{ $item->nama_kecamatan }}" title="Hapus">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada data kecamatan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
