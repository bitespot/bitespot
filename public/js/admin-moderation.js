// public/js/admin-moderation.js — SID_32: Review moderation UI

// ── DOM refs ──────────────────────────────────────────────────────────────────

const vendorSearchInput  = document.getElementById('admin-mod-vendor-search');
const vendorSuggestions  = document.getElementById('admin-mod-suggestions');
const selectedVendorWrap = document.getElementById('admin-mod-selected-vendor');
const selectedVendorName = document.getElementById('admin-mod-vendor-name');
const clearVendorBtn     = document.getElementById('admin-mod-clear-vendor');
const reviewsContainer   = document.getElementById('admin-mod-reviews');
const loadMoreWrap       = document.getElementById('admin-mod-load-more-wrap');
const loadMoreBtn        = document.getElementById('admin-mod-load-more');

// ── Helpers ───────────────────────────────────────────────────────────────────

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _starsHtml(rating) {
    return [1, 2, 3, 4, 5].map(i =>
        `<svg width="12" height="12" viewBox="0 0 24 24" fill="${i <= rating ? '#FBBF24' : '#E5E7EB'}">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
         </svg>`
    ).join('');
}

function _formatDate(str) {
    if (!str) return '';
    return new Date(str).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric',
    });
}

// ── Render ────────────────────────────────────────────────────────────────────

function _renderReviewCard(review) {
    const user   = review.user ?? {};
    const avatar = user.avatar
        ? `<img src="${_esc(user.avatar)}" alt="${_esc(user.name)}"
                class="w-8 h-8 rounded-full object-cover">`
        : `<div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center
                       text-orange-500 text-sm font-bold shrink-0">
               ${_esc((user.name ?? '?').charAt(0).toUpperCase())}
           </div>`;

    const banBtn = user.id
        ? `<button type="button" data-action="ban-user"
                   data-user-id="${user.id}" data-user-name="${_esc(user.name ?? 'this user')}"
                   class="px-3 py-1 text-xs font-medium text-gray-500 border border-gray-200
                          rounded-full hover:bg-gray-50 transition disabled:opacity-50">
               Ban User
           </button>`
        : '';

    return `
        <div data-review-card="${review.id}" class="p-4">
            <div class="flex items-start gap-3">
                <div class="shrink-0">${avatar}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-800">
                            ${_esc(user.name ?? 'Anonymous')}
                        </span>
                        <span class="text-xs text-gray-400">${_formatDate(review.created_at)}</span>
                    </div>
                    <div class="flex items-center gap-0.5 mt-0.5">${_starsHtml(review.rating)}</div>
                    ${review.body
                        ? `<p class="text-sm text-gray-600 mt-1 leading-relaxed">${_esc(review.body)}</p>`
                        : ''}
                </div>
                <div class="flex flex-col gap-1.5 shrink-0 ml-2">
                    <button type="button" data-action="remove-review" data-id="${review.id}"
                            class="px-3 py-1 text-xs font-medium text-red-500 border border-red-200
                                   rounded-full hover:bg-red-50 transition disabled:opacity-50">
                        Remove
                    </button>
                    ${banBtn}
                </div>
            </div>
        </div>`;
}

// ── Vendor search state ───────────────────────────────────────────────────────

let _searchDebounce = null;
let _selectedVendor = null;

// ── Vendor search ─────────────────────────────────────────────────────────────

function _searchVendors(q) {
    if (!q.trim()) {
        vendorSuggestions.classList.add('hidden');
        vendorSuggestions.innerHTML = '';
        return;
    }

    apiFetch(`/api/vendors?q=${encodeURIComponent(q)}&limit=5`)
        .then(res => {
            const vendors = Array.isArray(res.data)
                ? res.data
                : (res.data?.data ?? []);

            if (!vendors.length) {
                vendorSuggestions.innerHTML =
                    '<p class="px-4 py-3 text-sm text-gray-400">No vendors found.</p>';
            } else {
                vendorSuggestions.innerHTML = vendors
                    .map(v => `
                        <button type="button" data-action="select-vendor"
                                data-id="${v.id}" data-name="${_esc(v.name)}"
                                class="w-full text-left px-4 py-3 text-sm hover:bg-orange-50 transition">
                            <span class="font-medium text-gray-800">${_esc(v.name)}</span>
                            <span class="text-gray-400 ml-1">&middot; ${_esc(v.category ?? v.type ?? '')}</span>
                        </button>`)
                    .join('<div class="border-t border-gray-100"></div>');
            }
            vendorSuggestions.classList.remove('hidden');
        })
        .catch(() => {
            vendorSuggestions.innerHTML =
                '<p class="px-4 py-3 text-sm text-red-400">Search failed.</p>';
            vendorSuggestions.classList.remove('hidden');
        });
}

vendorSearchInput?.addEventListener('input', e => {
    clearTimeout(_searchDebounce);
    _searchDebounce = setTimeout(() => _searchVendors(e.target.value), 300);
});

vendorSearchInput?.addEventListener('keydown', e => {
    if (e.key === 'Escape') vendorSuggestions.classList.add('hidden');
});

document.addEventListener('click', e => {
    if (!vendorSearchInput?.contains(e.target) && !vendorSuggestions?.contains(e.target)) {
        vendorSuggestions?.classList.add('hidden');
    }
});

// ── Select vendor ─────────────────────────────────────────────────────────────

function _selectVendor(id, name) {
    _selectedVendor = { id, name };
    vendorSuggestions.classList.add('hidden');
    vendorSearchInput.value = '';
    selectedVendorName.textContent = name;
    selectedVendorWrap?.classList.remove('hidden');

    _page     = 1;
    _lastPage = 1;
    reviewsContainer.innerHTML = '';
    loadMoreWrap?.classList.add('hidden');
    loadReviews(1);
}

clearVendorBtn?.addEventListener('click', () => {
    _selectedVendor = null;
    selectedVendorWrap?.classList.add('hidden');
    reviewsContainer.innerHTML =
        '<p class="text-sm text-gray-400 py-8 text-center">Search for a vendor above to see their reviews.</p>';
    loadMoreWrap?.classList.add('hidden');
    _page     = 1;
    _lastPage = 1;
});

// ── Review list state ─────────────────────────────────────────────────────────

let _page     = 1;
let _lastPage = 1;
let _revLoading = false;

// ── Load reviews ──────────────────────────────────────────────────────────────

function loadReviews(page) {
    if (!_selectedVendor || _revLoading) return;
    _revLoading = true;

    if (page === 1) {
        reviewsContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading reviews…</p>';
    }

    apiFetch(`/api/vendors/${_selectedVendor.id}/reviews?page=${page}`)
        .then(res => {
            const reviews = res.data ?? [];
            _lastPage = res.last_page ?? 1;
            _page     = res.current_page ?? page;

            if (page === 1) reviewsContainer.innerHTML = '';

            if (!reviews.length && page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-gray-400 py-8 text-center">No reviews for this vendor.</p>';
            } else {
                reviewsContainer.insertAdjacentHTML(
                    'beforeend',
                    reviews
                        .map(_renderReviewCard)
                        .join('<div class="border-t border-gray-100"></div>')
                );
            }

            loadMoreWrap?.classList.toggle('hidden', _page >= _lastPage);
        })
        .catch(err => {
            console.error('[SID_32] Reviews fetch failed:', err);
            if (page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-red-400 py-8 text-center">Could not load reviews.</p>';
            }
        })
        .finally(() => { _revLoading = false; });
}

loadMoreBtn?.addEventListener('click', () => loadReviews(_page + 1));

// ── Remove review ─────────────────────────────────────────────────────────────

async function _removeReview(reviewId) {
    const card = reviewsContainer.querySelector(`[data-review-card="${reviewId}"]`);
    const btn  = card?.querySelector('[data-action="remove-review"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Removing…'; }

    try {
        await apiFetch(`/api/admin/reviews/${reviewId}`, { method: 'DELETE' });
        card?.remove();
        showToast('Review removed.', 'success');

        if (!reviewsContainer.querySelector('[data-review-card]')) {
            reviewsContainer.innerHTML =
                '<p class="text-sm text-gray-400 py-8 text-center">No reviews for this vendor.</p>';
            loadMoreWrap?.classList.add('hidden');
        }
    } catch (err) {
        const msg = err.data?.message ?? 'Could not remove review.';
        showToast(msg, 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Remove'; }
        console.error('[SID_32] Remove review failed:', err);
    }
}

// ── Ban user ──────────────────────────────────────────────────────────────────

async function _banUser(userId, userName) {
    if (!confirm(`Ban ${userName}? They will no longer be able to use the platform.`)) return;

    const allBtns = reviewsContainer.querySelectorAll(
        `[data-action="ban-user"][data-user-id="${userId}"]`
    );
    allBtns.forEach(b => { b.disabled = true; b.textContent = 'Banning…'; });

    try {
        await apiFetch(`/api/admin/users/${userId}/ban`, { method: 'POST' });
        showToast(`${userName} has been banned.`, 'warning');
        allBtns.forEach(b => {
            b.disabled = true;
            b.textContent = 'Banned';
            b.classList.add('opacity-50', 'cursor-default');
        });
    } catch (err) {
        const msg = err.data?.message ?? 'Could not ban user.';
        showToast(msg, 'error');
        allBtns.forEach(b => { b.disabled = false; b.textContent = 'Ban User'; });
        console.error('[SID_32] Ban user failed:', err);
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

reviewsContainer?.addEventListener('click', e => {
    const removeBtn = e.target.closest('[data-action="remove-review"]');
    const banBtn    = e.target.closest('[data-action="ban-user"]');

    if (removeBtn) _removeReview(removeBtn.dataset.id);
    if (banBtn)    _banUser(banBtn.dataset.userId, banBtn.dataset.userName);
});

vendorSuggestions?.addEventListener('click', e => {
    const selectBtn = e.target.closest('[data-action="select-vendor"]');
    if (selectBtn) _selectVendor(selectBtn.dataset.id, selectBtn.dataset.name);
});
