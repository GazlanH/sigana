<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas | SIGANA Perumda Tirta Intan Garut</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">
    <!-- Google Fonts & Bootstrap 5.3.3 CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v=2.0">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-4 px-3">

<div class="card shadow-lg border-0" style="width: 100%; max-width: 420px; border-radius: 14px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo Perumda Tirta Intan Garut" width="64" height="64" class="mx-auto mb-2 d-block brand-logo-img" style="width: 64px; height: 64px; max-width: 64px; max-height: 64px;">
            <h1 class="h5 fw-bold text-dark mb-1">SIGANA ADMIN</h1>
            <p class="small text-muted mb-0">PERUMDA AIR MINUM TIRTA INTAN GARUT</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-dark" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Masukkan username" required autofocus value="{{ old('username') }}">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label small text-muted" for="remember">Ingat Sesi Saya</label>
            </div>

            <div class="alert alert-info py-2 px-3 small mb-3">
                <div class="fw-bold mb-1">Akun Pengujian Sidang PKL:</div>
                <div>User: <code>admin</code> &bull; Pass: <code>admin123</code></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                Masuk ke Panel Admin
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="{{ route('public.index') }}" class="text-muted small text-decoration-none">&larr; Kembali ke Papan Pengumuman Publik</a>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
