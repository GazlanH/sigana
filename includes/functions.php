<?php
/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Helper Functions & Utilitas Inti (Pure Native PHP)
 * ============================================================================
 */

// 1. Inisialisasi Session Global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Hubungkan ke Database
require_once __DIR__ . '/../config/database.php';

/**
 * Sanitasi string mencegah serangan XSS
 */
function clean($str) {
    return htmlspecialchars(trim((string)($str ?? '')), ENT_QUOTES, 'UTF-8');
}

/**
 * Icon SVG Asli Berbasis Vektor (Zero External Font/CDN Dependency)
 */
function getIcon($name, $class = '') {
    $classAttr = $class ? ' ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') : '';
    switch ($name) {
        case 'pin':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';
        case 'warning':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
        case 'tool':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>';
        case 'clock':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
        case 'phone':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
        case 'whatsapp':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>';
        case 'check':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        case 'search':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
        case 'user':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
        case 'key':
        case 'lock':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>';
        case 'edit':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
        case 'trash':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';
        case 'plus':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>';
        case 'building':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>';
        case 'arrow-up-right':
        case 'external':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>';
        case 'calendar':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
        case 'filter':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>';
        case 'chevron-down':
            return '<svg class="svg-icon' . $classAttr . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
        default:
            return '';
    }
}

/**
 * Format Tanggal & Waktu Bahasa Indonesia Alami
 */
function formatTanggalIndo($datetimeStr, $withTime = true) {
    if (!$datetimeStr) return '-';

    $timestamp = strtotime($datetimeStr);
    if (!$timestamp) return '-';

    $bulan = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    $tgl = date('j', $timestamp);
    $namaBulan = $bulan[(int)date('n', $timestamp)] ?? '';
    $thn = date('Y', $timestamp);
    $jam = date('H:i', $timestamp);

    if ($withTime) {
        return "{$tgl} {$namaBulan} {$thn}, {$jam} WIB";
    }
    return "{$tgl} {$namaBulan} {$thn}";
}

/**
 * Render Badge Status Gangguan Air dengan Icon SVG
 */
function renderStatusBadge($status) {
    switch (strtolower(trim((string)$status))) {
        case 'investigasi':
            return '<span class="badge-status investigasi">' . getIcon('search') . ' Investigasi</span>';
        case 'perbaikan':
            return '<span class="badge-status perbaikan">' . getIcon('tool') . ' Dalam Perbaikan</span>';
        case 'normalisasi':
            return '<span class="badge-status normalisasi">' . getIcon('clock') . ' Normalisasi Aliran</span>';
        case 'selesai':
            return '<span class="badge-status selesai">' . getIcon('check') . ' Selesai</span>';
        default:
            return '<span class="badge-status">' . clean($status) . '</span>';
    }
}

/**
 * Render Badge Dampak Aliran Air dengan Icon SVG
 */
function renderDampakBadge($dampak) {
    switch (strtolower(trim((string)$dampak))) {
        case 'mati_total':
            return '<span class="badge-dampak mati">' . getIcon('warning') . ' Aliran Padam</span>';
        case 'aliran_kecil':
            return '<span class="badge-dampak kecil">' . getIcon('warning') . ' Debit Kecil</span>';
        case 'bertekanan_rendah':
            return '<span class="badge-dampak rendah">' . getIcon('warning') . ' Tekanan Rendah</span>';
        default:
            return '<span class="badge-dampak">' . clean($dampak) . '</span>';
    }
}

/**
 * Generate Nomor Tiket Otomatis (Format: GNG-YYYYMM-XXX)
 */
function generateNomorTiket($pdo) {
    $prefix = 'GNG-' . date('Ym') . '-';
    $stmt = $pdo->prepare("SELECT nomor_tiket FROM pengumuman WHERE nomor_tiket LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetch();

    if ($last && !empty($last['nomor_tiket'])) {
        $parts = explode('-', $last['nomor_tiket']);
        $lastNumber = (int) end($parts);
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '001';
    }
    return $prefix . $newNumber;
}

/**
 * Flash Messages (Notifikasi Sekali Tampil)
 */
function setFlash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'text' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

/**
 * Auth Guard & Manajemen Session Admin
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu untuk mengakses panel admin.');
        header('Location: login.php');
        exit;
    }
}

function currentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'admin',
            'nama_lengkap' => $_SESSION['nama_lengkap'] ?? 'Petugas Tirta Intan',
            'jabatan' => $_SESSION['jabatan'] ?? 'Staff Humas & Teknis',
            'role' => $_SESSION['role'] ?? 'admin'
        ];
    }
    return null;
}

function updateUserSession($userData) {
    if (isset($userData['username'])) $_SESSION['username'] = $userData['username'];
    if (isset($userData['nama_lengkap'])) $_SESSION['nama_lengkap'] = $userData['nama_lengkap'];
    if (isset($userData['jabatan'])) $_SESSION['jabatan'] = $userData['jabatan'];
    if (isset($userData['role'])) $_SESSION['role'] = $userData['role'];
}
