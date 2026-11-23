<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Manajemen Akun Petugas & Multi-User (Pure Native PHP)
 * ============================================================================
 */

require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$currentUser = currentUser();
$errors = [];
$edit_id = (int) ($_GET['edit'] ?? 0);
$edit_data = null;

// Jika mode edit, ambil data user yang akan diedit
if ($edit_id > 0) {
    $stmt_e = $pdo->prepare("SELECT id, username, nama_lengkap, jabatan, role FROM users WHERE id = ? LIMIT 1");
    $stmt_e->execute([$edit_id]);
    $edit_data = $stmt_e->fetch();
}

// 1. Pemrosesan Form (Tambah, Edit, Reset Password, Hapus User)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValidate()) {
        $errors[] = 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan muat ulang halaman.';
    } else {
        $action = $_POST['action'] ?? '';

        // A. Hapus Petugas
        if ($action === 'hapus') {
            $id_del = (int) ($_POST['id'] ?? 0);
            if ($id_del === (int) $currentUser['id']) {
                setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
            } else {
                try {
                    $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt_del->execute([$id_del]);
                    setFlash('success', 'Akun petugas berhasil dihapus dari sistem.');
                } catch (PDOException $e) {
                    error_log('Delete user error: ' . $e->getMessage());
                    setFlash('error', 'Gagal menghapus user. Terjadi kesalahan pada database.');
                }
            }
            header('Location: users.php');
            exit;
        }

        // B. Reset Password Cepat
        if ($action === 'reset_password') {
            $id_reset = (int) ($_POST['id'] ?? 0);
            $new_pass = $_POST['new_password'] ?? '';

            if (empty($new_pass) || strlen($new_pass) < 8) {
                $errors[] = 'Password baru minimal 8 karakter.';
            } else {
                try {
                    $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt_rp = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt_rp->execute([$hash, $id_reset]);
                    setFlash('success', 'Kata sandi petugas berhasil direset!');
                    header('Location: users.php');
                    exit;
                } catch (PDOException $e) {
                    error_log('Reset pass error: ' . $e->getMessage());
                    $errors[] = 'Gagal mereset kata sandi petugas.';
                }
            }
        }

        // C. Tambah / Edit Petugas
        if ($action === 'tambah' || $action === 'edit') {
            $username = trim($_POST['username'] ?? '');
            $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
            $jabatan = trim($_POST['jabatan'] ?? 'Petugas Humas & Teknis');
            $role = in_array($_POST['role'] ?? '', ['admin', 'petugas']) ? $_POST['role'] : 'petugas';
            $password = $_POST['password'] ?? '';

            if (empty($username)) $errors[] = 'Username wajib diisi.';
            if (empty($nama_lengkap)) $errors[] = 'Nama lengkap wajib diisi.';
            if (!empty($username) && !preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $username)) {
                $errors[] = 'Username hanya boleh alfanumerik, underscore, dan titik (3-50 karakter).';
            }

            if ($action === 'tambah') {
                if (empty($password) || strlen($password) < 8) {
                    $errors[] = 'Kata sandi awal wajib diisi minimal 8 karakter.';
                }

                // Cek username unik
                $stmt_c = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
                $stmt_c->execute([$username]);
                if ($stmt_c->fetch()) {
                    $errors[] = "Username '{$username}' sudah terdaftar dalam sistem.";
                }

                if (empty($errors)) {
                    try {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt_in = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, jabatan, role) VALUES (?, ?, ?, ?, ?)");
                        $stmt_in->execute([$username, $hash, $nama_lengkap, $jabatan, $role]);
                        setFlash('success', "Akun petugas '{$nama_lengkap}' berhasil dibuat!");
                        header('Location: users.php');
                        exit;
                    } catch (PDOException $e) {
                        error_log('Add user error: ' . $e->getMessage());
                        $errors[] = 'Gagal menyimpan akun petugas baru.';
                    }
                }
            } elseif ($action === 'edit') {
                $id_up = (int) ($_POST['id'] ?? 0);
                
                // Cek username unik untuk user lain
                $stmt_c = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
                $stmt_c->execute([$username, $id_up]);
                if ($stmt_c->fetch()) {
                    $errors[] = "Username '{$username}' sudah digunakan oleh akun lain.";
                }

                if (empty($errors)) {
                    try {
                        $stmt_up = $pdo->prepare("UPDATE users SET username = ?, nama_lengkap = ?, jabatan = ?, role = ? WHERE id = ?");
                        $stmt_up->execute([$username, $nama_lengkap, $jabatan, $role, $id_up]);
                        setFlash('success', "Data akun '{$nama_lengkap}' berhasil diperbarui!");
                        header('Location: users.php');
                        exit;
                    } catch (PDOException $e) {
                        error_log('Edit user error: ' . $e->getMessage());
                        $errors[] = 'Gagal memperbarui data akun petugas.';
                    }
                }
            }
        }
    }
}

// 2. Ambil Daftar Seluruh Petugas
$list_users = $pdo->query("
    SELECT u.id, u.username, u.nama_lengkap, u.jabatan, u.role, u.created_at,
           COUNT(p.id) as total_pengumuman
    FROM users u
    LEFT JOIN pengumuman p ON u.id = p.created_by
    GROUP BY u.id
    ORDER BY u.role ASC, u.nama_lengkap ASC
")->fetchAll();

$pageTitle = "Manajemen Akun Petugas";
require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h2 class="h4 fw-bold text-dark mb-1">Manajemen Akun Petugas</h2>
        <p class="text-muted small mb-0">Kelola kredensial login staf humas, cabang pelayanan, dan administrator SIGANA.</p>
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

<div class="row g-3 align-items-start mb-4">
    <!-- Form Tambah / Edit Petugas (Left Column) -->
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3">
                <h3 class="h6 fw-bold mb-0 text-dark">
                    <?= $edit_data ? getIcon('edit') . ' Edit Petugas' : getIcon('plus') . ' Tambah Petugas Baru' ?>
                </h3>
            </div>
            <div class="card-body p-3">
                <form action="users.php" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'tambah' ?>">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" for="username">Username Login <span class="req">*</span></label>
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="Contoh: petugas_tarogong"
                            value="<?= $edit_data ? clean($edit_data['username']) : (isset($_POST['username']) ? clean($_POST['username']) : '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="nama_lengkap">Nama Lengkap & Gelar <span class="req">*</span></label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                            placeholder="Contoh: Ahmad Fauzi, S.T."
                            value="<?= $edit_data ? clean($edit_data['nama_lengkap']) : (isset($_POST['nama_lengkap']) ? clean($_POST['nama_lengkap']) : '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="jabatan">Jabatan / Cabang Pelayanan <span class="req">*</span></label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control"
                            placeholder="Contoh: Staf Humas Cabang Tarogong"
                            value="<?= $edit_data ? clean($edit_data['jabatan']) : (isset($_POST['jabatan']) ? clean($_POST['jabatan']) : 'Petugas Humas & Teknis') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="role">Hak Akses (Role) <span class="req">*</span></label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="petugas" <?= ($edit_data && $edit_data['role'] === 'petugas') ? 'selected' : '' ?>>Petugas (Operator Posko)</option>
                            <option value="admin" <?= ($edit_data && $edit_data['role'] === 'admin') ? 'selected' : '' ?>>Administrator (Akses Penuh)</option>
                        </select>
                    </div>

                    <?php if (!$edit_data): ?>
                        <div class="mb-3">
                            <label class="form-label" for="password">Kata Sandi Awal <span class="req">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                        </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2 pt-2">
                        <?php if ($edit_data): ?>
                            <a href="users.php" class="btn btn-secondary flex-fill">Batal</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary fw-semibold flex-fill d-inline-flex align-items-center justify-content-center gap-1">
                            <?= getIcon('check') ?>
                            <span><?= $edit_data ? 'Simpan Perubahan' : 'Buat Akun Petugas' ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Petugas (Right Column) -->
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="h6 fw-bold mb-0 text-dark">Daftar Akun Petugas Terdaftar (<?= count($list_users) ?>)</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Petugas</th>
                                <th>Jabatan / Cabang</th>
                                <th>Role</th>
                                <th>Kontribusi</th>
                                <th class="text-end" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($list_users as $u): 
                                $isSelf = ($u['id'] === $currentUser['id']);
                            ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                            <?= clean($u['nama_lengkap']) ?>
                                            <?php if ($isSelf): ?>
                                                <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.65rem;">Anda</span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">@<?= clean($u['username']) ?></small>
                                    </td>
                                    <td>
                                        <span class="small text-secondary"><?= clean($u['jabatan']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $u['role'] === 'admin' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-secondary border' ?>" style="font-size: 0.72rem;">
                                            <?= strtoupper(clean($u['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted"><?= (int) $u['total_pengumuman'] ?> Pengumuman</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <a href="users.php?edit=<?= $u['id'] ?>" class="btn btn-outline-primary btn-sm btn-action-touch" title="Edit Identitas">
                                                Edit
                                            </a>
                                            <!-- Tombol Reset Sandi Cepat via Modal Sederhana -->
                                            <button type="button" class="btn btn-outline-warning btn-sm btn-action-touch" data-bs-toggle="modal" data-bs-target="#modalReset<?= $u['id'] ?>" title="Reset Password">
                                                Sandi
                                            </button>
                                            <?php if (!$isSelf): ?>
                                                <form action="users.php" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus akun petugas <?= clean($u['nama_lengkap']) ?>?')">
                                                    <?= csrfField() ?>
                                                    <input type="hidden" name="action" value="hapus">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-action-touch" title="Hapus Akun">
                                                        Hapus
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Modal Reset Password -->
                                        <div class="modal fade" id="modalReset<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm text-start">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header py-2">
                                                        <h6 class="modal-title fw-bold">Reset Kata Sandi</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="users.php" method="POST">
                                                        <div class="modal-body py-3">
                                                            <?= csrfField() ?>
                                                            <input type="hidden" name="action" value="reset_password">
                                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                            <p class="small text-muted mb-2">Masukkan kata sandi baru untuk <strong><?= clean($u['nama_lengkap']) ?></strong>:</p>
                                                            <input type="password" name="new_password" class="form-control form-control-sm" placeholder="Minimal 8 karakter" required minlength="8">
                                                        </div>
                                                        <div class="modal-footer py-2">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning btn-sm fw-semibold">Simpan Sandi Baru</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
