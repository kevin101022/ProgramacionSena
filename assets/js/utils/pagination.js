/**
 * renderPaginationCarousel
 * Renders a sliding window of page buttons (max 5 visible at a time).
 * When there are more pages, shows "..." ellipsis dots on each side.
 *
 * @param {HTMLElement} container   - The element with id="paginationNumbers"
 * @param {number}      currentPage - The currently active page (1-based)
 * @param {number}      totalPages  - Total number of pages
 * @param {Function}    onPageClick - Callback invoked with the selected page number
 */
function renderPaginationCarousel(container, currentPage, totalPages, onPageClick) {
    if (!container) return;
    container.innerHTML = '';

    if (totalPages <= 1) return;

    const WINDOW = 5; // Max page buttons visible at once

    // Calculate the sliding window
    let startPage = Math.max(1, currentPage - Math.floor(WINDOW / 2));
    let endPage   = startPage + WINDOW - 1;

    // Clamp to totalPages
    if (endPage > totalPages) {
        endPage   = totalPages;
        startPage = Math.max(1, endPage - WINDOW + 1);
    }

    const makeBtn = (label, page, isActive, isEllipsis) => {
        const btn = document.createElement('button');
        if (isEllipsis) {
            btn.textContent = '...';
            btn.disabled = true;
            btn.className = 'pagination-number pagination-ellipsis';
            return btn;
        }
        btn.textContent = label;
        btn.className   = `pagination-number${isActive ? ' active' : ''}`;
        btn.addEventListener('click', () => onPageClick(page));
        return btn;
    };

    // First page + ellipsis if window doesn't start at 1
    if (startPage > 1) {
        container.appendChild(makeBtn(1, 1, currentPage === 1, false));
        if (startPage > 2) {
            container.appendChild(makeBtn('...', null, false, true));
        }
    }

    // Window pages
    for (let i = startPage; i <= endPage; i++) {
        container.appendChild(makeBtn(i, i, i === currentPage, false));
    }

    // Ellipsis + last page if window doesn't reach the end
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            container.appendChild(makeBtn('...', null, false, true));
        }
        container.appendChild(makeBtn(totalPages, totalPages, currentPage === totalPages, false));
    }
}
