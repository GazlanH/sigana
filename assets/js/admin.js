/**
 * ==========================================================
 * SIGANA - PERUMDA TIRTA INTAN GARUT
 * Admin Panel JavaScript
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // Konfirmasi Hapus Data
    const deleteLinks = document.querySelectorAll('.confirm-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const itemName = link.dataset.item || 'data ini';
            const confirmed = confirm(`⚠️ Apakah Anda yakin ingin menghapus ${itemName}?\nTindakan ini tidak dapat dibatalkan.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // Otomatis isi tanggal estimasi jika kosong saat status diubah
    const statusSelect = document.getElementById('statusSelect');
    const estimasiInput = document.getElementById('estimasiSelesai');

    if (statusSelect && estimasiInput) {
        statusSelect.addEventListener('change', () => {
            if (statusSelect.value === 'selesai') {
                const now = new Date();
                const localISO = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                // Jika selesai, kita bisa set estimasi atau biarkan sesuai data
            }
        });
    }

    // Auto dismiss alert flash after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});
