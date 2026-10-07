/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Frontend JavaScript Logic (Clean Corporate & Responsive)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const filterKecamatan = document.getElementById('filterKecamatan');
    const tabButtons = document.querySelectorAll('.tab-nav-btn');
    const noticeEntries = document.querySelectorAll('.bulletin-card');
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

    // Search Input Listener
    if (searchInput) {
        searchInput.addEventListener('input', filterNotices);
        searchInput.addEventListener('keyup', filterNotices);
    }

    // Kecamatan Dropdown Listener
    if (filterKecamatan) {
        filterKecamatan.addEventListener('change', filterNotices);
    }

    // Status Tab Buttons Listener
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

    // Share to WhatsApp Handler
    window.shareToWA = function(ticket, title, kecamatan, wilayah, status, estimasi) {
        const baseUrl = window.location.origin + window.location.pathname.replace('index.php', '');
        const detailUrl = `${baseUrl}detail.php?tiket=${encodeURIComponent(ticket)}`;
        
        const message = 
            `📢 *INFORMASI GANGGUAN AIR BERSIH*\n` +
            `*PERUMDA AIR MINUM TIRTA INTAN GARUT*\n\n` +
            `🔹 *No. Tiket*: #${ticket}\n` +
            `🔹 *Perihal*: ${title}\n` +
            `🔹 *Wilayah Pelayanan*: Kec. ${kecamatan}\n` +
            `🔹 *Area Terdampak*:\n${wilayah}\n\n` +
            `🔹 *Status*: ${status.toUpperCase()}\n` +
            `🔹 *Estimasi Normal*: ${estimasi}\n\n` +
            `🌐 *Pantau rincian resmi selengkapnya di*:\n${detailUrl}\n\n` +
            `_Pusat Informasi & Pengaduan Perumda Tirta Intan Garut_`;

        const waUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
    };

    // Copy Ticket Link Handler
    window.copyLink = function(ticket) {
        const baseUrl = window.location.origin + window.location.pathname.replace('index.php', '');
        const url = `${baseUrl}detail.php?tiket=${encodeURIComponent(ticket)}`;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url).then(() => {
                alert(`✅ Tautan pengumuman tiket #${ticket} berhasil disalin ke clipboard!`);
            }).catch(() => {
                prompt('Salin tautan pengumuman berikut:', url);
            });
        } else {
            prompt('Salin tautan pengumuman berikut:', url);
        }
    };

    // Initial filter run
    filterNotices();
});
