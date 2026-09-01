<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['jabatan'] = $user['jabatan'];
            $_SESSION['role'] = $user['role'];

            setFlash('success', 'Selamat datang kembali, ' . $user['nama_lengkap'] . '!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password yang Anda masukkan salah!';
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas | SIGANA Perumda Tirta Intan Garut</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-4 px-3">

<div class="card shadow-lg border-0" style="width: 100%; max-width: 400px; border-radius: 12px;">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <img src="../assets/img/logo.png" alt="Logo Perumda Tirta Intan Garut" width="64" height="64" class="mx-auto mb-2 d-block" style="width: 64px; height: 64px; max-width: 64px; max-height: 64px; object-fit: contain;">
            <h1 class="h5 fw-bold text-dark mb-1">SIGANA ADMIN</h1>
            <p class="small text-muted mb-0">PERUMDA AIR MINUM TIRTA INTAN GARUT</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : clean($flash['type']) ?> py-2 px-3 small mb-3">
                <?= clean($flash['text']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 small mb-3">
                <?= clean($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-dark" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="<?= isset($username) ? clean($username) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-dark" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required>
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
            <a href="../index.php" class="text-muted small text-decoration-none">&larr; Kembali ke Papan Pengumuman Publik</a>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
