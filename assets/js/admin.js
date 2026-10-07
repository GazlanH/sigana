/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Admin Panel JavaScript Logic (Pure Native)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Konfirmasi Hapus Data
    const deleteLinks = document.querySelectorAll('.confirm-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const itemName = link.dataset.item || 'data ini';
            const confirmed = confirm(`⚠️ Konfirmasi Penghapusan:\n\nApakah Anda yakin ingin menghapus ${itemName}?\nTindakan ini bersifat permanen dan tidak dapat dibatalkan.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // 2. Auto Dismiss Flash Alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alertEl => {
        setTimeout(() => {
            alertEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateY(-8px)';
            setTimeout(() => {
                alertEl.remove();
            }, 400);
        }, 5000);
    });
});
