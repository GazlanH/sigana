/**
 * ==========================================================
 * SIGANA - PERUMDA TIRTA INTAN GARUT
 * Main Frontend JavaScript (Bootstrap 5 Compatible)
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const filterKecamatan = document.getElementById('filterKecamatan');
    const tabButtons = document.querySelectorAll('.tab-nav-btn, .tab-btn, .tab-pill-btn');
    const noticeEntries = document.querySelectorAll('.bulletin-card, .notice-entry, .notice-card');
    const emptyState = document.getElementById('emptyState');

    let currentTab = 'aktif'; // 'aktif' | 'semua' | 'selesai'

    function filterNotices() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const selectedKecamatan = (filterKecamatan ? filterKecamatan.value : '').toLowerCase().trim();
        let visibleCount = 0;

        noticeEntries.forEach(entry => {
            const title = (entry.getAttribute('data-title') || '').toLowerCase();
            const wilayah = (entry.getAttribute('data-wilayah') || '').toLowerCase();
            const kecamatan = (entry.getAttribute('data-kecamatan') || '').toLowerCase();
            const status = (entry.getAttribute('data-status') || '').toLowerCase();
            const ticket = (entry.getAttribute('data-ticket') || '').toLowerCase();

            // Match tab filter
            let matchTab = true;
            if (currentTab === 'aktif') {
                matchTab = (status !== 'selesai');
            } else if (currentTab === 'selesai') {
                matchTab = (status === 'selesai');
            }

            // Match search query
            const matchSearch = !query || 
                title.includes(query) || 
                wilayah.includes(query) || 
                kecamatan.includes(query) ||
                ticket.includes(query);

            // Match kecamatan dropdown
            const matchKecamatan = !selectedKecamatan || kecamatan === selectedKecamatan;

            if (matchTab && matchSearch && matchKecamatan) {
                entry.style.display = 'block';
                visibleCount++;
            } else {
                entry.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Input Search Listener
    if (searchInput) {
        searchInput.addEventListener('input', filterNotices);
        searchInput.addEventListener('keyup', filterNotices);
    }

    // Dropdown Kecamatan Listener
    if (filterKecamatan) {
        filterKecamatan.addEventListener('change', filterNotices);
    }

    // Tab Button Listeners
    tabButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            tabButtons.forEach(b => {
                b.classList.remove('active');
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            btn.classList.add('active');
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-primary');
            currentTab = btn.getAttribute('data-tab') || 'aktif';
            filterNotices();
        });
    });

    // Share to WhatsApp Function
    window.shareToWA = function(ticket, title, kecamatan, wilayah, status, estimasi) {
        const currentUrl = window.location.origin + window.location.pathname.replace('index.php', '') + 'detail.php?tiket=' + encodeURIComponent(ticket);
        const text = `INFO GANGGUAN AIR PERUMDA TIRTA INTAN GARUT\n\n` +
            `No. Tiket: ${ticket}\n` +
            `Perihal: ${title}\n` +
            `Kecamatan: ${kecamatan}\n` +
            `Wilayah Terdampak: ${wilayah}\n` +
            `Status Penanganan: ${status.toUpperCase()}\n` +
            `Estimasi Selesai: ${estimasi}\n\n` +
            `Informasi resmi selengkapnya di:\n${currentUrl}`;

        const waUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`;
        window.open(waUrl, '_blank');
    };

    // Copy Ticket Link
    window.copyLink = function(ticket) {
        const url = window.location.origin + window.location.pathname.replace('index.php', '') + 'detail.php?tiket=' + encodeURIComponent(ticket);
        navigator.clipboard.writeText(url).then(() => {
            alert('Tautan pengumuman berhasil disalin ke clipboard.');
        }).catch(() => {
            prompt('Salin tautan berikut:', url);
        });
    };

    // Initial filter run
    filterNotices();
});
