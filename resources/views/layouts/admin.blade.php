<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Admin' }} | Panel SIGANA Garut</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

    <!-- Google Fonts & Bootstrap 5.3.3 CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=2.0">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v=2.0">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- Top Admin Header -->
<header class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-2 shadow-sm">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Logo & Title -->
        <a class="navbar-brand d-flex align-items-center gap-2 py-0" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="brand-logo-img-sm">
            <div class="d-flex flex-column">
                <span class="fs-6 fw-bold text-dark lh-1">SIGANA PANEL</span>
                <span class="text-muted" style="font-size: 0.70rem;">TIRTA INTAN GARUT</span>
            </div>
        </a>

        <!-- Desktop Navigation Bar -->
        <nav class="d-none d-lg-flex align-items-center gap-1 admin-desktop-nav mx-auto">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('tool') !!} Dashboard
            </a>
            <a href="{{ route('admin.pengumuman.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('warning') !!} Kelola Pengumuman
            </a>
            <a href="{{ route('admin.kecamatan.index') }}" class="admin-nav-link {{ request()->routeIs('admin.kecamatan.*') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('pin') !!} Data Wilayah & Cabang
            </a>
            <a href="{{ route('public.index') }}" target="_blank" class="admin-nav-link text-muted">
                {!! \App\Helpers\SiganaHelper::icon('search') !!} Lihat Web Publik &nearr;
            </a>
        </nav>

        <!-- User Info & Logout Button Desktop -->
        <div class="d-none d-lg-flex align-items-center gap-3">
            <div class="text-end lh-sm">
                <div class="fw-bold small text-dark">{{ Auth::user()->nama_lengkap ?? 'Administrator' }}</div>
                <div class="text-muted" style="font-size: 0.70rem;">{{ Auth::user()->jabatan ?? 'Petugas' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold">
                    Keluar
                </button>
            </form>
        </div>

        <!-- Mobile Drawer Toggle Button -->
        <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMobileOffcanvas" aria-controls="adminMobileOffcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</header>

<!-- Mobile Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="adminMobileOffcanvas" aria-labelledby="adminMobileOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="brand-logo-img-sm">
            <div>
                <h5 class="offcanvas-title fw-bold text-dark fs-6" id="adminMobileOffcanvasLabel">SIGANA ADMIN</h5>
                <span class="text-muted small" style="font-size: 0.70rem;">Perumda Tirta Intan Garut</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-3">
        <!-- User Info in Drawer -->
        <div class="bg-light p-3 rounded mb-3 border">
            <div class="fw-bold text-dark">{{ Auth::user()->nama_lengkap ?? 'Administrator' }}</div>
            <div class="text-muted small">{{ Auth::user()->jabatan ?? 'Petugas' }}</div>
        </div>

        <nav class="d-flex flex-column gap-1 mb-auto">
            <a href="{{ route('admin.dashboard') }}" class="admin-drawer-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('tool') !!} Dashboard
            </a>
            <a href="{{ route('admin.pengumuman.index') }}" class="admin-drawer-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('warning') !!} Kelola Pengumuman
            </a>
            <a href="{{ route('admin.kecamatan.index') }}" class="admin-drawer-link {{ request()->routeIs('admin.kecamatan.*') ? 'active' : '' }}">
                {!! \App\Helpers\SiganaHelper::icon('pin') !!} Data Wilayah & Cabang
            </a>
            <a href="{{ route('public.index') }}" target="_blank" class="admin-drawer-link text-muted">
                {!! \App\Helpers\SiganaHelper::icon('search') !!} Lihat Web Publik &nearr;
            </a>
        </nav>

        <div class="pt-3 border-top mt-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger w-100 fw-semibold">
                    Keluar dari Sistem
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main Admin Container -->
<main class="flex-grow-1 py-4">
    <div class="container-fluid px-3 px-lg-4">
        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3" role="alert">
                {!! \App\Helpers\SiganaHelper::icon('check') !!} {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                {!! \App\Helpers\SiganaHelper::icon('warning') !!} {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3" role="alert">
                <strong class="d-block mb-1">Periksa kembali data Anda:</strong>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Footer -->
<footer class="bg-white border-top py-3 text-center small text-muted mt-auto">
    &copy; {{ date('Y') }} <strong>SIGANA</strong> &bull; Panel Administrasi PERUMDA Air Minum Tirta Intan Garut.
</footer>

<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/admin.js') }}?v=2.0"></script>
@stack('scripts')
</body>
</html>
