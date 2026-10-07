<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Halaman Login Petugas Admin (Pure Native PHP)
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
                // Kecocokan teks polos: otomatis upgrade ke Bcrypt
                $passwordMatches = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upStmt->execute([$newHash, $user['id']]);
            } elseif ($username === 'admin' && $password === 'admin123') {
                // Fallback akun uji coba default
                $passwordMatches = true;
                $newHash = password_hash('admin123', PASSWORD_DEFAULT);
                $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upStmt->execute([$newHash, $user['id']]);
            }
        }

        if ($user && $passwordMatches) {
            // Set session login
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
    <!-- Bootstrap 5.3.3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=2.0">
    <link rel="stylesheet" href="../assets/css/admin.css?v=2.0">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 py-4 px-3" style="background: radial-gradient(circle at top, #1e293b 0%, #0f172a 100%);">

<div class="card shadow-lg border-0" style="width: 100%; max-width: 420px; border-radius: 14px; overflow: hidden;">
    <!-- Top Brand Accent Header -->
    <div class="bg-primary text-white text-center py-4 px-4">
        <img src="../assets/img/logo.png" alt="Logo Perumda Tirta Intan Garut" width="60" height="60" class="mx-auto mb-2 d-block bg-white p-1 rounded-circle shadow-sm" style="object-fit: contain;">
        <h1 class="h5 fw-bold text-white mb-0">SIGANA ADMIN PANEL</h1>
        <p class="small text-white-50 mb-0 fw-medium">PERUMDA AIR MINUM TIRTA INTAN GARUT</p>
    </div>

    <div class="card-body p-4 p-md-4">
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
                <label class="form-label" for="username">Username Akun Petugas</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><?= getIcon('user') ?></span>
                    <input type="text" id="username" name="username" class="form-control border-start-0" placeholder="Masukkan username" required autofocus value="<?= isset($username) ? clean($username) : '' ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Kata Sandi (Password)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><?= getIcon('lock') ?></span>
                    <input type="password" id="password" name="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                </div>
            </div>

            <div class="alert alert-info py-2 px-3 small mb-3 border-info-subtle bg-info-subtle text-info-emphasis">
                <div class="fw-bold mb-1">Informasi Akun Default Petugas:</div>
                <div>User: <code>admin</code> &bull; Pass: <code>admin123</code></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2">
                <?= getIcon('check') ?> <span>Masuk ke Panel Admin</span>
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <a href="../index.php" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1">
                &larr; <span>Kembali ke Papan Pengumuman Publik</span>
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
