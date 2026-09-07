<?php

namespace App\Helpers;

class SiganaHelper
{
    /**
     * Real SVG Icons Helper (Zero External CDN Dependency)
     */
    public static function icon($name, $class = '')
    {
        $classAttr = $class ? ' ' . htmlspecialchars($class) : '';
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
            default:
                return '';
        }
    }

    /**
     * Format Tanggal & Waktu Bahasa Indonesia
     */
    public static function formatTanggalIndo($datetime, $withTime = true)
    {
        if (!$datetime) return '-';

        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $tgl = date('j', $timestamp);
        $namaBulan = $bulan[(int)date('n', $timestamp)];
        $thn = date('Y', $timestamp);
        $jam = date('H:i', $timestamp);

        if ($withTime) {
            return "{$tgl} {$namaBulan} {$thn}, {$jam} WIB";
        }
        return "{$tgl} {$namaBulan} {$thn}";
    }

    /**
     * Render Badge Status
     */
    public static function renderStatusBadge($status)
    {
        switch (strtolower($status)) {
            case 'investigasi':
                return '<span class="badge-status investigasi">' . self::icon('search') . ' Investigasi</span>';
            case 'perbaikan':
                return '<span class="badge-status perbaikan">' . self::icon('tool') . ' Dalam Perbaikan</span>';
            case 'normalisasi':
                return '<span class="badge-status normalisasi">' . self::icon('clock') . ' Normalisasi Aliran</span>';
            case 'selesai':
                return '<span class="badge-status selesai">' . self::icon('check') . ' Selesai</span>';
            default:
                return '<span class="badge-status">' . e($status) . '</span>';
        }
    }

    /**
     * Render Badge Dampak Aliran
     */
    public static function renderDampakBadge($dampak)
    {
        switch (strtolower($dampak)) {
            case 'mati_total':
                return '<span class="badge-dampak mati">' . self::icon('warning') . ' Aliran Padam</span>';
            case 'aliran_kecil':
                return '<span class="badge-dampak kecil">' . self::icon('warning') . ' Debit Kecil</span>';
            case 'bertekanan_rendah':
                return '<span class="badge-dampak rendah">' . self::icon('warning') . ' Tekanan Rendah</span>';
            default:
                return '<span class="badge-dampak">' . e($dampak) . '</span>';
        }
    }
}
