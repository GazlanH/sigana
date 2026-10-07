<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Kelola Profil & Kata Sandi Petugas Admin (Pure Native PHP)
 * ============================================================================
 */

$pageTitle = "Profil Akun Petugas";
require_once __DIR__ . '/../includes/admin_header.php';

$userId = $user['id'];
$errors = [];
$successMsg = '';

// Ambil data user terkini dari database
$stmt_u = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt_u->execute([$userId]);
$userData = $stmt_u->fetch();

if (!$userData) {
    setFlash('error', 'Data pengguna tidak ditemukan.');
    header('Location: index.php');
    exit;
}

// 1. Update Profil (Nama & Jabatan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $username = trim($_POST['username'] ?? '');

    if (empty($nama_lengkap)) $errors[] = 'Nama lengkap wajib diisi.';
    if (empty($username)) $errors[] = 'Username wajib diisi.';

    // Cek username unik jika diubah
    if (!empty($username) && $username !== $userData['username']) {
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
        $stmt_check->execute([$username, $userId]);
        if ($stmt_check->fetch()) {
            $errors[] = 'Username ini sudah digunakan oleh akun lain.';
        }
    }

    if (empty($errors)) {
        try {
            $stmt_up = $pdo->prepare("UPDATE users SET nama_lengkap = ?, jabatan = ?, username = ? WHERE id = ?");
            $stmt_up->execute([$nama_lengkap, $jabatan, $username, $userId]);

            // Update session
            updateUserSession([
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'username' => $username
            ]);

            setFlash('success', 'Profil akun petugas berhasil diperbarui!');
            header('Location: profil.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Gagal memperbarui profil: ' . $e->getMessage();
        }
    }
}

// 2. Ganti Kata Sandi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $pass_lama = trim($_POST['pass_lama'] ?? '');
    $pass_baru = trim($_POST['pass_baru'] ?? '');
    $pass_konfirmasi = trim($_POST['pass_konfirmasi'] ?? '');

    if (empty($pass_lama)) $errors[] = 'Password saat ini wajib diisi.';
    if (empty($pass_baru)) $errors[] = 'Password baru wajib diisi.';
    if (strlen($pass_baru) < 6) $errors[] = 'Password baru minimal 6 karakter.';
    if ($pass_baru !== $pass_konfirmasi) $errors[] = 'Konfirmasi password baru tidak cocok.';

    // Cek password lama
    if (empty($errors)) {
        if (!password_verify($pass_lama, $userData['password']) && $pass_lama !== $userData['password']) {
            $errors[] = 'Password saat ini yang Anda masukkan salah.';
        } else {
            try {
                $newHash = password_hash($pass_baru, PASSWORD_DEFAULT);
                $stmt_p = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_p->execute([$newHash, $userId]);

                setFlash('success', 'Kata sandi akun berhasil diperbarui! Gunakan password baru untuk login berikutnya.');
                header('Location: profil.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Gagal mengubah password: ' . $e->getMessage();
            }
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Pengaturan Profil & Kata Sandi</h2>
        <p class="text-muted small mb-0">Kelola identitas petugas dan amankan akses akun admin SIGANA.</p>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger shadow-sm mb-3">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= clean($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <!-- Form Biodata Petugas (Left) -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0 text-dark"><?= getIcon('user') ?> Informasi Identitas Petugas</h3>
            </div>
            <div class="card-body p-3 p-md-4">
                <form action="profil.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="mb-3">
                        <label class="form-label" for="username">Username Login <span class="req">*</span></label>
                        <input type="text" id="username" name="username" class="form-control" value="<?= clean($userData['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_lengkap">Nama Lengkap Petugas <span class="req">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?= clean($userData['nama_lengkap']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="jabatan">Jabatan / Bagian</label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control" value="<?= clean($userData['jabatan']) ?>" placeholder="Contoh: Staff Humas & Pengaduan">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role Akses</label>
                        <input type="text" class="form-control bg-light" value="<?= strtoupper(clean($userData['role'])) ?>" readonly>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary fw-semibold d-inline-flex align-items-center gap-1">
                            <?= getIcon('check') ?> <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form Ganti Password (Right) -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border h-100">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0 text-dark"><?= getIcon('key') ?> Ganti Kata Sandi (Password)</h3>
            </div>
            <div class="card-body p-3 p-md-4">
                <form action="profil.php" method="POST">
                    <input type="hidden" name="action" value="change_password">

                    <div class="mb-3">
                        <label class="form-label" for="pass_lama">Kata Sandi Saat Ini <span class="req">*</span></label>
                        <input type="password" id="pass_lama" name="pass_lama" class="form-control" placeholder="Masukkan password lama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pass_baru">Kata Sandi Baru <span class="req">*</span></label>
                        <input type="password" id="pass_baru" name="pass_baru" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="pass_konfirmasi">Konfirmasi Kata Sandi Baru <span class="req">*</span></label>
                        <input type="password" id="pass_konfirmasi" name="pass_konfirmasi" class="form-control" placeholder="Ulangi password baru" required>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-danger fw-semibold d-inline-flex align-items-center gap-1">
                            <?= getIcon('lock') ?> <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
