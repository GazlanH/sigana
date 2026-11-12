/**
 * ============================================================================
 * SIGANA - SISTEM INFORMASI PENGUMUMAN GANGGUAN ALIRAN AIR
 * PERUMDA AIR MINUM TIRTA INTAN KABUPATEN GARUT
 * ============================================================================
 * Frontend JavaScript Logic (Live Filter, Pagination, Fast Search)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    const filterKecamatan = document.getElementById('filterKecamatan');
    const filterDampak = document.getElementById('filterDampak');
    const statusPillBtns = document.querySelectorAll('.status-pill-btn');
    const noticeCards = Array.from(document.querySelectorAll('.bulletin-card'));
    const emptyState = document.getElementById('emptyState');
    const paginationContainer = document.getElementById('pamPagination');
    const pageNumberContainer = document.getElementById('pageNumberContainer');
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');

    const PAGE_SIZE = 6;
    let currentPage = 1;
    let currentStatusTab = 'semua';
    let matchingCards = [...noticeCards];

    function applyFilters() {
        const selectedKecamatan = (filterKecamatan ? filterKecamatan.value : '').toLowerCase().trim();
        const selectedDampak = (filterDampak ? filterDampak.value : '').toLowerCase().trim();

        matchingCards = noticeCards.filter(card => {
            const status = (card.getAttribute('data-status') || '').toLowerCase();
            const kecamatan = (card.getAttribute('data-kecamatan') || '').toLowerCase();
            const dampak = (card.getAttribute('data-dampak') || '').toLowerCase();

            // 1. Status Filter
            const matchStatus = (currentStatusTab === 'semua') || (status === currentStatusTab);

            // 2. Kecamatan Filter
            const matchKecamatan = !selectedKecamatan || (kecamatan === selectedKecamatan);

            // 3. Dampak Aliran Filter
            const matchDampak = !selectedDampak || (dampak === selectedDampak);

            return matchStatus && matchKecamatan && matchDampak;
        });

        currentPage = 1;
        renderPaginatedView();
    }

    function renderPaginatedView() {
        const totalItems = matchingCards.length;
        const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all cards first
        noticeCards.forEach(card => card.style.display = 'none');

        // Show slice for current page
        const startIdx = (currentPage - 1) * PAGE_SIZE;
        const endIdx = startIdx + PAGE_SIZE;
        const pageItems = matchingCards.slice(startIdx, endIdx);

        pageItems.forEach(card => {
            card.style.display = 'flex';
        });

        // Toggle Empty State
        if (emptyState) {
            emptyState.style.display = (totalItems === 0) ? 'block' : 'none';
        }

        // Render Pagination UI
        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        if (!paginationContainer || !pageNumberContainer) return;

        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'flex';
        pageNumberContainer.innerHTML = '';

        if (prevPageBtn) {
            prevPageBtn.disabled = (currentPage === 1);
            prevPageBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPaginatedView();
                    scrollToGrid();
                }
            };
        }

        if (nextPageBtn) {
            nextPageBtn.disabled = (currentPage === totalPages);
            nextPageBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPaginatedView();
                    scrollToGrid();
                }
            };
        }

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `pam-page-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.onclick = () => {
                currentPage = i;
                renderPaginatedView();
                scrollToGrid();
            };
            pageNumberContainer.appendChild(btn);
        }
    }

    function scrollToGrid() {
        const grid = document.getElementById('announcementGrid');
        if (grid) {
            const topPos = grid.getBoundingClientRect().top + window.scrollY - 100;
            window.scrollTo({ top: topPos, behavior: 'smooth' });
        }
    }

    // Event Listeners
    if (filterKecamatan) {
        filterKecamatan.addEventListener('change', applyFilters);
    }

    if (filterDampak) {
        filterDampak.addEventListener('change', applyFilters);
    }

    statusPillBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            statusPillBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentStatusTab = btn.getAttribute('data-tab') || 'semua';
            applyFilters();
        });
    });

    window.resetFilters = function() {
        if (filterKecamatan) filterKecamatan.value = '';
        if (filterDampak) filterDampak.value = '';
        statusPillBtns.forEach(b => {
            if (b.getAttribute('data-tab') === 'semua') {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        currentStatusTab = 'semua';
        applyFilters();
    };

    // Initial Execution
    applyFilters();
});
