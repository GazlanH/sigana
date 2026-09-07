<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'SIGANA' }} | PERUMDA Tirta Intan Garut</title>

    <!-- Meta SEO & Responsiveness -->
    <meta name="description" content="Sistem Informasi Gangguan dan Pemeliharaan Aliran Air Bersih PERUMDA Tirta Intan Kabupaten Garut.">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

    <!-- Bootstrap 5.3.3 CSS & Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=2.0">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Header Navbar Publik -->
<header class="navbar navbar-expand-lg navbar-dark bg-dark py-2 shadow-sm sticky-top border-bottom border-primary border-3">
    <div class="container">
        <!-- Logo & Identitas Perumda -->
        <a class="navbar-brand d-flex align-items-center gap-2 py-0" href="{{ route('public.index') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Perumda Tirta Intan Garut" class="brand-logo-img">
            <div class="d-flex flex-column text-start">
                <span class="fs-6 fw-bold text-white lh-1">SIGANA</span>
                <span class="text-white-50" style="font-size: 0.70rem; letter-spacing: 0.02em;">PERUMDA AIR MINUM TIRTA INTAN GARUT</span>
            </div>
        </a>

        <!-- Tombol Menu Mobile -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenuPublic" aria-controls="navMenuPublic" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Navigasi -->
        <div class="collapse navbar-collapse" id="navMenuPublic">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('public.index') ? 'active fw-bold text-white' : '' }}" href="{{ route('public.index') }}">
                        Papan Pengumuman
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white-50" href="tel:081120001122">
                        {!! \App\Helpers\SiganaHelper::icon('phone') !!} Call Center
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    @auth
                        <a class="btn btn-outline-light btn-sm fw-semibold px-3" href="{{ route('admin.dashboard') }}">
                            Panel Admin ({{ Auth::user()->username }})
                        </a>
                    @else
                        <a class="btn btn-outline-light btn-sm fw-semibold px-3" href="{{ route('login') }}">
                            Login Petugas
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Flash Message Notifications -->
@if(session('success') || session('error') || session('warning'))
<div class="container mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {!! \App\Helpers\SiganaHelper::icon('check') !!} {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            {!! \App\Helpers\SiganaHelper::icon('warning') !!} {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
@endif

<!-- Main Content Yield -->
<main class="flex-grow-1">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="brand-logo-img-sm">
            <span class="fw-bold text-dark">PERUMDA Air Minum Tirta Intan Garut</span>
        </div>
        <p class="text-muted small mb-1">
            Jl. Raya Bayongbong - Garut KM. 3 No. 100, Garut, Jawa Barat
        </p>
        <p class="text-muted small mb-0">
            &copy; {{ date('Y') }} <strong>SIGANA</strong> &bull; Sistem Informasi Gangguan Air Bersih Terpadu.
        </p>
    </div>
</footer>

<!-- Bootstrap 5.3.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/main.js') }}?v=2.0"></script>
@stack('scripts')
</body>
</html>
