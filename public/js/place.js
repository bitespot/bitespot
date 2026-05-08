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

// ---------------------------------------------------------------------------
// SID_16: Share link — Web Share API → clipboard → execCommand fallback
// ---------------------------------------------------------------------------

const shareBtn = document.getElementById('share-btn');

shareBtn?.addEventListener('click', async () => {
    const url = window.location.href;

    // 1. Native share sheet (mobile / supported desktop)
    if (navigator.share) {
        try {
            await navigator.share({ url });
            return;
        } catch (e) {
            if (e.name === 'AbortError') return; // user cancelled — do nothing
        }
    }

    // 2. Clipboard API (requires HTTPS)
    if (navigator.clipboard?.writeText) {
        try {
            await navigator.clipboard.writeText(url);
            showToast('Link copied to clipboard!', 'success');
            return;
        } catch {}
    }

    // 3. execCommand fallback (works on plain HTTP)
    try {
        const tmp = document.createElement('input');
        tmp.value = url;
        document.body.appendChild(tmp);
        tmp.select();
        document.execCommand('copy');
        document.body.removeChild(tmp);
        showToast('Link copied to clipboard!', 'success');
    } catch {
        showToast('Share link: ' + url, 'info', 8000);
    }
});

// ---------------------------------------------------------------------------
// SID_17 + SID_18 + SID_19: Reviews — submit, edit/delete, paginated list
// ---------------------------------------------------------------------------

const reviewsContainer = document.getElementById('reviews-container');
const reviewsTotalEl   = document.getElementById('reviews-total');
const loadMoreBtn      = document.getElementById('load-more-btn');
const reviewFormCard   = document.getElementById('review-form-card');
const reviewForm       = document.getElementById('review-form');
const starPicker       = document.getElementById('star-picker');
const ratingInput      = document.getElementById('review-rating');
const reviewBodyEl     = document.getElementById('review-body');
const reviewSubmitBtn  = document.getElementById('review-submit-btn');
const reviewFormErr    = document.getElementById('review-form-error');

let reviewsPage     = 1;
let reviewsLastPage = 1;
let reviewsLoading  = false;
let userReviewId    = null;

const _reviewCache = new Map(); // id (number) → review object

// ── Helpers ──

function _esc(str) {
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _starsHtml(rating, size = 12) {
    return [1,2,3,4,5].map(i => `
        <svg width="${size}" height="${size}" viewBox="0 0 24 24"
             fill="${i <= rating ? '#FBBF24' : 'none'}"
             stroke="${i <= rating ? '#FBBF24' : '#d1d5db'}"
             stroke-width="2" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>`).join('');
}

function _fmtDate(iso) {
    return new Date(iso).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric',
    });
}

// ── Star picker helper (shared by submit form + edit form) ──

function _applyStarPicker(picker, hiddenInput, value) {
    if (hiddenInput) hiddenInput.value = value;
    picker?.querySelectorAll('[data-star]').forEach(b => {
        b.style.color = Number(b.dataset.star) <= value ? '#f97316' : '#d1d5db';
    });
}

function _wireStarPicker(picker, hiddenInput) {
    picker.addEventListener('click', e => {
        const b = e.target.closest('[data-star]');
        if (b) _applyStarPicker(picker, hiddenInput, Number(b.dataset.star));
    });
    picker.addEventListener('mouseover', e => {
        const b = e.target.closest('[data-star]');
        if (b) _applyStarPicker(picker, null, Number(b.dataset.star));
    });
    picker.addEventListener('mouseleave', () => {
        _applyStarPicker(picker, null, Number(hiddenInput?.value ?? 0));
    });
}

if (starPicker && ratingInput) _wireStarPicker(starPicker, ratingInput);

// ── Review card ──

function _renderReviewCard(review) {
    _reviewCache.set(review.id, review);
    const isOwn = window.IS_AUTH && review.user_id === window.USER_ID;
    const name  = _esc(review.user?.name ?? 'Anonymous');
    return `
        <div class="review-item border-b border-gray-100 py-4 last:border-0" data-review-id="${review.id}">
            <div class="flex items-start gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-800">${name}</span>
                        <span class="text-xs text-gray-400">${_fmtDate(review.created_at)}</span>
                    </div>
                    <div class="flex gap-0.5 mb-1.5">${_starsHtml(review.rating)}</div>
                    ${review.body ? `<p class="text-sm text-gray-600">${_esc(review.body)}</p>` : ''}
                </div>
                ${isOwn ? `
                <div class="flex gap-1 shrink-0 mt-0.5">
                    <button data-action="edit-review" data-id="${review.id}"
                            class="text-xs text-blue-500 hover:text-blue-700 px-2 py-1 rounded hover:bg-blue-50 transition">Edit</button>
                    <button data-action="delete-review" data-id="${review.id}"
                            class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded hover:bg-red-50 transition">Delete</button>
                </div>` : ''}
            </div>
        </div>`;
}

// ── SID_19: Paginated reviews list ──

function loadReviews(page) {
    if (reviewsLoading || !reviewsContainer) return;
    reviewsLoading = true;

    if (page === 1) {
        reviewsContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-4 text-center animate-pulse">Loading reviews…</p>';
        userReviewId = null;
    }

    apiFetch(`/api/vendors/${vendorId}/reviews?page=${page}`)
        .then(res => {
            const reviews   = res.data ?? [];
            reviewsLastPage = res.last_page ?? 1;
            reviewsPage     = res.current_page ?? page;

            if (reviewsTotalEl) {
                reviewsTotalEl.textContent = res.total
                    ? `${res.total} review${res.total !== 1 ? 's' : ''}`
                    : '';
            }

            if (page === 1) reviewsContainer.innerHTML = '';

            if (!reviews.length && page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-gray-400 py-4 text-center">No reviews yet. Be the first!</p>';
            } else {
                reviewsContainer.insertAdjacentHTML(
                    'beforeend',
                    reviews.map(_renderReviewCard).join('')
                );
                const mine = reviews.find(r => r.user_id === window.USER_ID);
                if (mine) {
                    userReviewId = mine.id;
                    reviewFormCard?.classList.add('hidden');
                }
            }

            if (loadMoreBtn) loadMoreBtn.hidden = (reviewsPage >= reviewsLastPage);
        })
        .catch(err => {
            console.error('[SID_19] Reviews fetch failed:', err);
            if (page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-red-400 py-4 text-center">Could not load reviews.</p>';
            }
        })
        .finally(() => { reviewsLoading = false; });
}

loadMoreBtn?.addEventListener('click', () => loadReviews(reviewsPage + 1));
if (reviewsContainer) loadReviews(1);

// ── SID_17: Submit review ──

reviewForm?.addEventListener('submit', async e => {
    e.preventDefault();

    const rating = Number(ratingInput?.value ?? 0);
    const body   = reviewBodyEl?.value.trim() ?? '';

    if (reviewFormErr) reviewFormErr.textContent = '';

    if (!rating) {
        if (reviewFormErr) reviewFormErr.textContent = 'Please select a star rating.';
        return;
    }

    if (reviewSubmitBtn) { reviewSubmitBtn.disabled = true; reviewSubmitBtn.textContent = 'Submitting…'; }

    try {
        const review = await apiFetch('/api/reviews', {
            method: 'POST',
            body: { vendor_id: vendorId, rating, body: body || null },
        });

        review.user = { id: window.USER_ID, name: window.AUTH_NAME ?? 'You' };

        if (reviewsContainer) {
            const emptyMsg = reviewsContainer.querySelector('p');
            if (emptyMsg) reviewsContainer.innerHTML = '';
            reviewsContainer.insertAdjacentHTML('afterbegin', _renderReviewCard(review));
        }

        userReviewId = review.id;
        reviewFormCard?.classList.add('hidden');

        if (reviewsTotalEl) {
            const prev     = parseInt(reviewsTotalEl.textContent) || 0;
            const newCount = prev + 1;
            reviewsTotalEl.textContent = `${newCount} review${newCount !== 1 ? 's' : ''}`;
        }

        showToast('Review submitted!', 'success');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not submit review.';
        if (reviewFormErr) reviewFormErr.textContent = msg;
        console.error('[SID_17] Submit review failed:', err);
    } finally {
        if (reviewSubmitBtn) {
            reviewSubmitBtn.disabled    = false;
            reviewSubmitBtn.textContent = 'Submit Review';
        }
    }
});

// ── SID_18: Edit / delete (event delegation on reviewsContainer) ──

reviewsContainer?.addEventListener('click', e => {
    const editBtn   = e.target.closest('[data-action="edit-review"]');
    const deleteBtn = e.target.closest('[data-action="delete-review"]');
    const cancelBtn = e.target.closest('[data-action="cancel-edit"]');

    if (editBtn)   _startEditReview(editBtn.dataset.id);
    if (deleteBtn) _deleteReview(deleteBtn.dataset.id);
    if (cancelBtn) _cancelEditReview(cancelBtn.dataset.id);
});

function _startEditReview(reviewId) {
    const cached = _reviewCache.get(Number(reviewId));
    const card   = reviewsContainer?.querySelector(`[data-review-id="${reviewId}"]`);
    if (!card) return;

    const currRating = cached?.rating ?? 0;
    const currBody   = cached?.body ?? '';

    card.innerHTML = `
        <form class="edit-review-form space-y-3" data-review-id="${reviewId}">
            <div class="flex gap-1 cursor-pointer" id="edit-star-picker-${reviewId}">
                ${[1,2,3,4,5].map(i => `
                    <button type="button" data-star="${i}"
                            style="color:${i<=currRating?'#f97316':'#d1d5db'};font-size:1.75rem;line-height:1"
                            aria-label="${i} star${i>1?'s':''}">&#9733;</button>`).join('')}
            </div>
            <input type="hidden" id="edit-rating-${reviewId}" value="${currRating}">
            <textarea id="edit-body-${reviewId}" rows="3" maxlength="1000"
                      class="w-full border border-gray-200 rounded-lg p-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orange-300">${_esc(currBody)}</textarea>
            <p class="text-xs text-red-500 edit-form-error min-h-[1rem]"></p>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-1.5 bg-orange-500 text-white text-xs font-medium rounded-full hover:bg-orange-600 transition disabled:opacity-50">
                    Save
                </button>
                <button type="button" data-action="cancel-edit" data-id="${reviewId}"
                        class="px-4 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>`;

    const editPicker = document.getElementById(`edit-star-picker-${reviewId}`);
    const editRating = document.getElementById(`edit-rating-${reviewId}`);
    if (editPicker && editRating) _wireStarPicker(editPicker, editRating);

    card.querySelector('.edit-review-form')?.addEventListener('submit', async ev => {
        ev.preventDefault();
        await _submitEditReview(reviewId);
    });
}

async function _submitEditReview(reviewId) {
    const card    = reviewsContainer?.querySelector(`[data-review-id="${reviewId}"]`);
    const rating  = Number(document.getElementById(`edit-rating-${reviewId}`)?.value ?? 0);
    const body    = document.getElementById(`edit-body-${reviewId}`)?.value.trim() ?? '';
    const errEl   = card?.querySelector('.edit-form-error');
    const saveBtn = card?.querySelector('[type="submit"]');

    if (!rating) {
        if (errEl) errEl.textContent = 'Please select a rating.';
        return;
    }

    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving…'; }

    try {
        const updated = await apiFetch(`/api/reviews/${reviewId}`, {
            method: 'PUT',
            body: { rating, body: body || null },
        });
        updated.user = { id: window.USER_ID, name: window.AUTH_NAME ?? 'You' };

        if (card) card.outerHTML = _renderReviewCard(updated);
        showToast('Review updated.', 'success');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not update review.';
        if (errEl) errEl.textContent = msg;
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }
        console.error('[SID_18] Edit review failed:', err);
    }
}

function _cancelEditReview(reviewId) {
    const cached = _reviewCache.get(Number(reviewId));
    const card   = reviewsContainer?.querySelector(`[data-review-id="${reviewId}"]`);
    if (!card) return;
    if (cached) {
        card.outerHTML = _renderReviewCard(cached);
    } else {
        loadReviews(1);
    }
}

async function _deleteReview(reviewId) {
    if (!confirm('Delete this review?')) return;

    try {
        await apiFetch(`/api/reviews/${reviewId}`, { method: 'DELETE' });

        reviewsContainer?.querySelector(`[data-review-id="${reviewId}"]`)?.remove();
        _reviewCache.delete(Number(reviewId));
        userReviewId = null;

        if (!reviewsContainer?.querySelector('.review-item')) {
            if (reviewsContainer) reviewsContainer.innerHTML =
                '<p class="text-sm text-gray-400 py-4 text-center">No reviews yet. Be the first!</p>';
        }

        if (reviewsTotalEl && reviewsTotalEl.textContent) {
            const prev     = parseInt(reviewsTotalEl.textContent) || 1;
            const newCount = Math.max(0, prev - 1);
            reviewsTotalEl.textContent = newCount
                ? `${newCount} review${newCount !== 1 ? 's' : ''}`
                : '';
        }

        reviewFormCard?.classList.remove('hidden');
        if (ratingInput)  ratingInput.value  = 0;
        if (reviewBodyEl) reviewBodyEl.value  = '';
        _applyStarPicker(starPicker, ratingInput, 0);
        if (reviewFormErr) reviewFormErr.textContent = '';

        showToast('Review deleted.', 'info');
    } catch (err) {
        const msg = err.data?.message ?? 'Could not delete review.';
        showToast(msg, 'error');
        console.error('[SID_18] Delete review failed:', err);
    }
}
