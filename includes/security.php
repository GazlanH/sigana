<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Modul Keamanan Terpusat (Security Hardening Layer)
 * - CSRF Token Protection
 * - Session Hardening & Timeout
 * - Brute-Force Rate Limiting
 * - Security Headers
 * - Input Validation
 * ============================================================================
 */

// ============================================================================
// 1. SESSION HARDENING
// ============================================================================

/**
 * Inisialisasi session dengan konfigurasi keamanan optimal.
 * Harus dipanggil SEBELUM session_start() manapun.
 */
function initSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return; // Session sudah berjalan
    }

    // Konfigurasi cookie session yang aman
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');

    if ($isHttps) {
        ini_set('session.cookie_secure', '1');
    }

    // Session lifetime: 2 jam
    ini_set('session.gc_maxlifetime', '7200');

    session_start();

    // Regenerasi session ID setiap 30 menit untuk cegah session fixation
    if (!isset($_SESSION['_session_created'])) {
        $_SESSION['_session_created'] = time();
    } elseif (time() - $_SESSION['_session_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_session_created'] = time();
    }
}

/**
 * Cek apakah session admin sudah expired (timeout 2 jam inaktif).
 * Return true jika session masih valid.
 */
function checkSessionTimeout($maxIdleMinutes = 120) {
    if (isset($_SESSION['_last_activity'])) {
        $idleSeconds = time() - $_SESSION['_last_activity'];
        if ($idleSeconds > ($maxIdleMinutes * 60)) {
            // Session expired — hapus semua data
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            return false;
        }
    }
    $_SESSION['_last_activity'] = time();
    return true;
}

// ============================================================================
// 2. CSRF TOKEN PROTECTION
// ============================================================================

/**
 * Generate atau ambil CSRF token aktif.
 * Token disimpan di session dan berlaku per-session.
 */
function csrfToken() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Render hidden input field untuk CSRF token di form HTML.
 */
function csrfField() {
    return '<input type="hidden" name="_csrf_token" value="' . csrfToken() . '">';
}

/**
 * Validasi CSRF token dari POST request.
 * Return true jika token valid.
 */
function csrfValidate() {
    $submittedToken = $_POST['_csrf_token'] ?? '';
    $sessionToken = $_SESSION['_csrf_token'] ?? '';

    if (empty($submittedToken) || empty($sessionToken)) {
        return false;
    }

    return hash_equals($sessionToken, $submittedToken);
}

/**
 * Enforce CSRF validation. Jika gagal, redirect dengan pesan error.
 */
function csrfGuard($redirectUrl = null) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrfValidate()) {
            if (function_exists('setFlash')) {
                setFlash('error', 'Sesi keamanan tidak valid atau telah kedaluwarsa. Silakan coba lagi.');
            }
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
            } else {
                header('Location: ' . $_SERVER['PHP_SELF']);
            }
            exit;
        }
    }
}

// ============================================================================
// 3. BRUTE-FORCE RATE LIMITING
// ============================================================================

/**
 * Cek apakah login attempt masih diperbolehkan.
 * Menggunakan session-based counter (cukup untuk single-server setup).
 *
 * @param int $maxAttempts Maksimal percobaan gagal
 * @param int $lockoutSeconds Durasi lockout dalam detik
 * @return array ['allowed' => bool, 'remaining' => int, 'lockout_until' => int|null]
 */
function checkLoginRateLimit($maxAttempts = 5, $lockoutSeconds = 300) {
    $now = time();

    // Bersihkan lockout yang sudah expired
    if (isset($_SESSION['_login_lockout_until']) && $now >= $_SESSION['_login_lockout_until']) {
        unset($_SESSION['_login_lockout_until']);
        $_SESSION['_login_attempts'] = 0;
    }

    // Cek apakah sedang dalam lockout
    if (isset($_SESSION['_login_lockout_until']) && $now < $_SESSION['_login_lockout_until']) {
        $remainingLockout = $_SESSION['_login_lockout_until'] - $now;
        return [
            'allowed' => false,
            'remaining' => 0,
            'lockout_remaining' => $remainingLockout
        ];
    }

    $attempts = (int)($_SESSION['_login_attempts'] ?? 0);
    $remaining = max(0, $maxAttempts - $attempts);

    return [
        'allowed' => ($attempts < $maxAttempts),
        'remaining' => $remaining,
        'lockout_remaining' => 0
    ];
}

/**
 * Catat percobaan login gagal.
 */
function recordFailedLogin($maxAttempts = 5, $lockoutSeconds = 300) {
    if (!isset($_SESSION['_login_attempts'])) {
        $_SESSION['_login_attempts'] = 0;
    }
    $_SESSION['_login_attempts']++;

    if ($_SESSION['_login_attempts'] >= $maxAttempts) {
        $_SESSION['_login_lockout_until'] = time() + $lockoutSeconds;
    }
}

/**
 * Reset login attempt counter (setelah login berhasil).
 */
function resetLoginAttempts() {
    unset($_SESSION['_login_attempts']);
    unset($_SESSION['_login_lockout_until']);
}

// ============================================================================
// 4. SECURITY HEADERS
// ============================================================================

/**
 * Kirim HTTP security headers standar.
 * Panggil di awal setiap response.
 */
function sendSecurityHeaders() {
    // Cegah clickjacking
    header('X-Frame-Options: DENY');

    // Cegah MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Aktifkan XSS filter di browser lama
    header('X-XSS-Protection: 1; mode=block');

    // Referrer policy — kirim origin saja untuk cross-origin
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions policy — nonaktifkan fitur sensitif
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

    // Content Security Policy — restrict resource loading
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'; frame-ancestors 'none';");
}

// ============================================================================
// 5. UTILITY KEAMANAN
// ============================================================================

/**
 * Validasi bahwa request adalah POST method.
 * Gunakan untuk operasi yang mengubah data (create/update/delete).
 */
function requirePost($redirectUrl = 'index.php') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (function_exists('setFlash')) {
            setFlash('error', 'Metode request tidak valid. Gunakan form yang tersedia.');
        }
        header('Location: ' . $redirectUrl);
        exit;
    }
}

/**
 * Sanitasi dan validasi URL untuk redirect.
 * Cegah Open Redirect attack.
 */
function safeRedirect($url, $allowedPaths = []) {
    // Hanya izinkan relative paths atau paths dalam whitelist
    $parsed = parse_url($url);
    if (isset($parsed['host']) || isset($parsed['scheme'])) {
        // Absolute URL — cek apakah host sama
        $currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
        if (isset($parsed['host']) && $parsed['host'] !== $currentHost) {
            $url = 'index.php'; // Fallback ke homepage
        }
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Dapatkan base URL yang aman (escape dari header injection).
 */
function getSecureBaseUrl() {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme = $isHttps ? 'https' : 'http';

    // Sanitasi SERVER_NAME (lebih aman dari HTTP_HOST)
    $host = preg_replace('/[^a-zA-Z0-9.\-:]/', '', $_SERVER['SERVER_NAME'] ?? 'localhost');
    $port = (int)($_SERVER['SERVER_PORT'] ?? 80);

    if (($isHttps && $port !== 443) || (!$isHttps && $port !== 80)) {
        return "{$scheme}://{$host}:{$port}";
    }
    return "{$scheme}://{$host}";
}
