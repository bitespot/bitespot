// public/js/place.js — SID_13 (menu highlights) + SID_15 (bookmark toggle)
// Depends on: api.js (apiFetch), ui.js (showToast, renderSkeleton)

const vendorId = window.VENDOR_ID;

// ---------------------------------------------------------------------------
// SID_13: Menu highlights — fetch & render from /api/vendors/{id}/menu
// The API returns items grouped by category:
//   { "Mains": [{id, name, description, price, ...}], "Drinks": [...] }
// ---------------------------------------------------------------------------

const menuContainer = document.getElementById('menu-container');

function _formatPrice(price) {
    return '₱' + Number(price).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

function _renderMenuCategory(category, items) {
    const rows = items.map(item => `
        <div class="flex items-start justify-between gap-4 py-3 border-b border-gray-100 last:border-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800">${item.name}</p>
                ${item.description
                    ? `<p class="text-xs text-gray-400 mt-0.5 line-clamp-2">${item.description}</p>`
                    : ''}
            </div>
            <span class="text-sm font-semibold text-orange-600 shrink-0 tabular-nums">
                ${_formatPrice(item.price)}
            </span>
        </div>
    `).join('');

    return `
        <div class="mb-5 last:mb-0">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">${category}</p>
            ${rows}
        </div>
    `;
}

function loadMenu() {
    if (!menuContainer || !vendorId) return;

    renderSkeleton(menuContainer, 4);

    apiFetch(`/api/vendors/${vendorId}/menu`)
        .then(grouped => {
            const categories = Object.keys(grouped);
            if (!categories.length) {
                menuContainer.innerHTML =
                    '<p class="text-sm text-gray-400 py-4 text-center">No menu listed yet.</p>';
                return;
            }
            menuContainer.innerHTML = categories
                .map(cat => _renderMenuCategory(cat, grouped[cat]))
                .join('');
        })
        .catch(err => {
            console.error('[SID_13] Menu fetch failed:', err);
            menuContainer.innerHTML =
                '<p class="text-sm text-red-400 py-4 text-center">Could not load menu.</p>';
        });
}

loadMenu();

// ---------------------------------------------------------------------------
// SID_15: Bookmark toggle — heart icon, POST/DELETE /api/user/bookmarks/{id}
// Optimistic update: flip state immediately, roll back on error.
// Guests see a "Sign in" toast instead of hitting the API.
// ---------------------------------------------------------------------------

const bookmarkBtn = document.getElementById('bookmark-btn');
let   isBookmarked = !!(window.IS_BOOKMARKED);
let   bookmarkBusy = false;

function _applyBookmarkState(bookmarked) {
    isBookmarked = bookmarked;
    if (!bookmarkBtn) return;

    bookmarkBtn.setAttribute('aria-pressed', String(bookmarked));
    bookmarkBtn.dataset.bookmarked = String(bookmarked);

    const icon = bookmarkBtn.querySelector('[data-bookmark-icon]');
    if (icon) {
        icon.style.fill   = bookmarked ? '#f97316' : 'none';
        icon.style.stroke = bookmarked ? '#f97316' : 'currentColor';
    }
}

async function _toggleBookmark() {
    if (bookmarkBusy) return;

    if (!window.IS_AUTH) {
        showToast('Sign in to save this BiteSpot.', 'info');
        return;
    }

    bookmarkBusy = true;
    const prev = isBookmarked;
    _applyBookmarkState(!prev);          // optimistic flip

    try {
        if (prev) {
            await apiFetch(`/api/user/bookmarks/${vendorId}`, { method: 'DELETE' });
            showToast('Removed from saved places.', 'info');
        } else {
            await apiFetch(`/api/user/bookmarks/${vendorId}`, { method: 'POST' });
            showToast('Saved to your places!', 'success');
        }
    } catch (err) {
        _applyBookmarkState(prev);       // roll back on failure
        const msg = err.data?.message ?? 'Something went wrong. Try again.';
        showToast(msg, 'error');
        console.error('[SID_15] Bookmark toggle failed:', err);
    } finally {
        bookmarkBusy = false;
    }
}

// Sync icon with server-rendered initial state, then wire the click handler
_applyBookmarkState(isBookmarked);
bookmarkBtn?.addEventListener('click', _toggleBookmark);
