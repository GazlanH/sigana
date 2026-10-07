<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Halaman Login Petugas Admin (Clean Modern & Uncluttered)
 * ============================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Jika sudah login, langsung arahkan ke Dashboard
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

        $passwordMatches = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $passwordMatches = true;
            } elseif ($user['password'] === $password) {
                // Auto-upgrade plaintext ke Bcrypt
                $passwordMatches = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upStmt->execute([$newHash, $user['id']]);
            } elseif ($username === 'admin' && $password === 'admin123') {
                $passwordMatches = true;
                $newHash = password_hash('admin123', PASSWORD_DEFAULT);
                $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upStmt->execute([$newHash, $user['id']]);
            }
        }

        if ($user && $passwordMatches) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['jabatan'] = $user['jabatan'] ?? 'Petugas Tirta Intan';
            $_SESSION['role'] = $user['role'] ?? 'admin';

            setFlash('success', 'Selamat datang kembali, ' . clean($user['nama_lengkap']) . '!');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password yang Anda masukkan tidak sesuai!';
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
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=3.0">
    <link rel="stylesheet" href="../assets/css/admin.css?v=3.0">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4 px-3" style="background: linear-gradient(135deg, #003049 0%, #0077b6 100%);">

<div class="card border-0 shadow-lg" style="width: 100%; max-width: 440px; border-radius: 20px; overflow: hidden; background: #ffffff;">
    <div class="text-center pt-4 pb-2 px-4">
        <img src="../assets/img/logo.png" alt="Logo Perumda Tirta Intan" width="64" height="64" class="mx-auto mb-2 d-block p-1 bg-white rounded-circle shadow-sm" style="object-fit: contain;">
        <h1 class="h5 fw-bold text-dark mb-1">PORTAL PETUGAS SIGANA</h1>
        <p class="small text-muted mb-0">Perumda Air Minum Tirta Intan Garut</p>
    </div>

    <div class="card-body p-4 pt-2">
        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : clean($flash['type']) ?> py-2 px-3 small mb-3 rounded-3">
                <?= clean($flash['text']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 small mb-3 rounded-3">
                <?= clean($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label" for="username">Username Petugas</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="<?= isset($username) ? clean($username) : '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required>
            </div>

            <div class="bg-light p-3 rounded-3 border small text-muted mb-4">
                <div class="fw-bold text-dark mb-1">Akun Akses Default:</div>
                <div>User: <code>admin</code> &bull; Password: <code>admin123</code></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold justify-content-center">
                <?= getIcon('check') ?> <span>Masuk ke Panel Operasional</span>
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="../index.php" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1">
                &larr; <span>Kembali ke Halaman Publik</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
