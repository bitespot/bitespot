// public/js/profile.js — SID_22 (bookmarks), SID_23 (my reviews), SID_24 (edit profile)

// ── DOM refs ─────────────────────────────────────────────────────────────────

const tabs    = document.querySelectorAll('.profile-tab');
const tabPanels = {
    bookmarks: document.getElementById('tab-bookmarks'),
    reviews:   document.getElementById('tab-reviews'),
    settings:  document.getElementById('tab-settings'),
};

// SID_22
const bookmarksContainer     = document.getElementById('bookmarks-container');
const bookmarksLoadMoreWrap  = document.getElementById('bookmarks-load-more-wrap');
const bookmarksLoadMoreBtn   = document.getElementById('bookmarks-load-more');

// SID_23
const myReviewsContainer    = document.getElementById('my-reviews-container');
const myReviewsLoadMoreWrap = document.getElementById('my-reviews-load-more-wrap');
const myReviewsLoadMoreBtn  = document.getElementById('my-reviews-load-more');

// SID_24
const profileForm          = document.getElementById('profile-form');
const profileNameInput     = document.getElementById('profile-name-input');
const profileEmailInput    = document.getElementById('profile-email-input');
const profileLocationInput = document.getElementById('profile-location-input');
const profileFormError     = document.getElementById('profile-form-error');
const profileSaveBtn       = document.getElementById('profile-save-btn');
const avatarInput          = document.getElementById('avatar-input');
const avatarPreview        = document.getElementById('avatar-preview');
const profileNameDisplay   = document.getElementById('profile-name-display');
const profileEmailDisplay  = document.getElementById('profile-email-display');
const profileAvatarHero    = document.getElementById('profile-avatar-hero');

// ── Shared helpers ────────────────────────────────────────────────────────────

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

// ── Tab switching ─────────────────────────────────────────────────────────────

let _activeTab       = 'bookmarks';
let _myReviewsLoaded = false;
let _profileLoaded   = false;

function _switchTab(name) {
    if (_activeTab === name) return;
    _activeTab = name;

    tabs.forEach(btn => {
        const active = btn.dataset.tab === name;
        btn.classList.toggle('text-orange-500',    active);
        btn.classList.toggle('border-orange-500',  active);
        btn.classList.toggle('text-gray-400',      !active);
        btn.classList.toggle('border-transparent', !active);
    });

    Object.entries(tabPanels).forEach(([key, el]) => {
        el?.classList.toggle('hidden', key !== name);
    });

    if (name === 'reviews' && !_myReviewsLoaded) loadMyReviews(1);
    if (name === 'settings' && !_profileLoaded)  loadProfile();
}

tabs.forEach(btn => btn.addEventListener('click', () => _switchTab(btn.dataset.tab)));

// ── SID_22: Saved Places (bookmarks) ─────────────────────────────────────────

let _bookmarksPage     = 1;
let _bookmarksLastPage = 1;
let _bookmarksLoading  = false;

function _renderBookmarkCard(bookmark) {
    const v   = bookmark.vendor ?? {};
    const img = v.primary_photo
        ? `<img src="${_esc(v.primary_photo)}" alt="${_esc(v.business_name)}" class="w-full h-full object-cover">`
        : `<div class="w-full h-full bg-gradient-to-br from-orange-400 to-orange-200"></div>`;

    const ratingHtml = v.avg_rating
        ? `<span class="flex items-center gap-0.5 mt-1">
               ${_starsHtml(Math.round(v.avg_rating))}
               <span class="text-xs text-gray-500 ml-1">${Number(v.avg_rating).toFixed(1)}</span>
           </span>`
        : '';

    const meta = [v.category?.name, v.price_tier].filter(Boolean).map(_esc).join(' · ');

    return `
        <a href="/place/${_esc(v.slug)}" data-bookmark-card="${bookmark.id}"
           class="flex items-center gap-3 bg-gray-50 hover:bg-orange-50 rounded-xl p-3 transition">
            <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-gray-100">${img}</div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">${_esc(v.business_name ?? '')}</p>
                ${meta ? `<p class="text-xs text-gray-400 truncate">${meta}</p>` : ''}
                ${ratingHtml}
            </div>
            <button type="button" data-action="remove-bookmark" data-vendor-id="${v.id}"
                    class="shrink-0 p-2 text-gray-300 hover:text-red-400 transition"
                    aria-label="Remove bookmark">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14H6L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4h6v2"/>
                </svg>
            </button>
        </a>`;
}

function loadBookmarks(page) {
    if (_bookmarksLoading) return;
    _bookmarksLoading = true;

    if (page === 1) {
        bookmarksContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading saved places…</p>';
    }

    apiFetch(`/api/user/bookmarks?page=${page}`)
        .then(res => {
            const items        = res.data ?? [];
            _bookmarksLastPage = res.last_page ?? 1;
            _bookmarksPage     = res.current_page ?? page;

            if (page === 1) bookmarksContainer.innerHTML = '';

            if (!items.length && page === 1) {
                bookmarksContainer.innerHTML =
                    '<p class="text-sm text-gray-400 py-8 text-center">No saved places yet.</p>';
            } else {
                bookmarksContainer.insertAdjacentHTML('beforeend', items.map(_renderBookmarkCard).join(''));
            }

            bookmarksLoadMoreWrap?.classList.toggle('hidden', _bookmarksPage >= _bookmarksLastPage);
        })
        .catch(err => {
            console.error('[SID_22] Bookmarks fetch failed:', err);
            if (page === 1) {
                bookmarksContainer.innerHTML =
                    '<p class="text-sm text-red-400 py-8 text-center">Could not load saved places.</p>';
            }
        })
        .finally(() => { _bookmarksLoading = false; });
}

bookmarksLoadMoreBtn?.addEventListener('click', () => loadBookmarks(_bookmarksPage + 1));

bookmarksContainer?.addEventListener('click', async e => {
    const btn = e.target.closest('[data-action="remove-bookmark"]');
    if (!btn) return;
    e.preventDefault();
    e.stopPropagation();

    const vendorId = btn.dataset.vendorId;
    if (!confirm('Remove from saved places?')) return;

    try {
        await apiFetch(`/api/user/bookmarks/${vendorId}`, { method: 'DELETE' });
        btn.closest('[data-bookmark-card]')?.remove();

        if (!bookmarksContainer.querySelector('[data-bookmark-card]')) {
            bookmarksContainer.innerHTML =
                '<p class="text-sm text-gray-400 py-8 text-center">No saved places yet.</p>';
        }

        showToast('Removed from saved places.', 'info');
    } catch (err) {
        showToast(err.data?.message ?? 'Could not remove bookmark.', 'error');
        console.error('[SID_22] Remove bookmark failed:', err);
    }
});

loadBookmarks(1);

// ── SID_23: My Reviews ────────────────────────────────────────────────────────

let _myReviewsPage     = 1;
let _myReviewsLastPage = 1;
let _myReviewsLoading  = false;
const _myReviewCache   = new Map();

function _wireStarPicker(picker, hidden) {
    const btns = picker.querySelectorAll('[data-star]');

    function _apply(val) {
        btns.forEach(b => {
            b.style.color = Number(b.dataset.star) <= val ? '#f97316' : '#d1d5db';
        });
        hidden.value = val;
    }

    _apply(Number(hidden.value));
    btns.forEach(btn => btn.addEventListener('click', () => _apply(Number(btn.dataset.star))));
}

function _renderMyReviewCard(review) {
    const vendor = review.vendor ?? {};
    _myReviewCache.set(review.id, review);

    return `
        <div data-my-review-id="${review.id}" class="my-review-item bg-gray-50 rounded-xl p-4 space-y-2">
            <div class="flex items-start justify-between gap-2">
                <a href="/place/${_esc(vendor.slug)}"
                   class="font-semibold text-gray-800 text-sm hover:text-orange-500 transition truncate">
                    ${_esc(vendor.business_name ?? '')}
                </a>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" data-action="edit-my-review" data-id="${review.id}"
                            class="p-1.5 text-gray-400 hover:text-orange-500 transition"
                            aria-label="Edit review">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button type="button" data-action="delete-my-review" data-id="${review.id}"
                            class="p-1.5 text-gray-400 hover:text-red-500 transition"
                            aria-label="Delete review">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-0.5">${_starsHtml(review.rating)}</div>
            ${review.body ? `<p class="text-sm text-gray-600 leading-relaxed">${_esc(review.body)}</p>` : ''}
        </div>`;
}

function loadMyReviews(page) {
    if (_myReviewsLoading) return;
    _myReviewsLoading = true;

    if (page === 1) {
        myReviewsContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading reviews…</p>';
    }

    apiFetch(`/api/user/reviews?page=${page}`)
        .then(res => {
            const reviews      = res.data ?? [];
            _myReviewsLastPage = res.last_page ?? 1;
            _myReviewsPage     = res.current_page ?? page;
            _myReviewsLoaded   = true;

            if (page === 1) myReviewsContainer.innerHTML = '';

            if (!reviews.length && page === 1) {
                myReviewsContainer.innerHTML =
                    "<p class=\"text-sm text-gray-400 py-8 text-center\">You haven't written any reviews yet.</p>";
            } else {
                myReviewsContainer.insertAdjacentHTML('beforeend', reviews.map(_renderMyReviewCard).join(''));
            }

            myReviewsLoadMoreWrap?.classList.toggle('hidden', _myReviewsPage >= _myReviewsLastPage);
        })
        .catch(err => {
            console.error('[SID_23] My reviews fetch failed:', err);
            _myReviewsLoaded = true;
            if (page === 1) {
                myReviewsContainer.innerHTML =
                    '<p class="text-sm text-red-400 py-8 text-center">Could not load reviews.</p>';
            }
        })
        .finally(() => { _myReviewsLoading = false; });
}

myReviewsLoadMoreBtn?.addEventListener('click', () => loadMyReviews(_myReviewsPage + 1));

myReviewsContainer?.addEventListener('click', e => {
    const editBtn   = e.target.closest('[data-action="edit-my-review"]');
    const deleteBtn = e.target.closest('[data-action="delete-my-review"]');
    const cancelBtn = e.target.closest('[data-action="cancel-my-review-edit"]');

    if (editBtn)   _startEditMyReview(editBtn.dataset.id);
    if (deleteBtn) _deleteMyReview(deleteBtn.dataset.id);
    if (cancelBtn) _cancelEditMyReview(cancelBtn.dataset.id);
});

function _startEditMyReview(reviewId) {
    const cached = _myReviewCache.get(Number(reviewId));
    const card   = myReviewsContainer?.querySelector(`[data-my-review-id="${reviewId}"]`);
    if (!card) return;

    const currRating = cached?.rating ?? 0;
    const currBody   = cached?.body ?? '';

    card.innerHTML = `
        <form class="edit-my-review-form space-y-3" data-review-id="${reviewId}">
            <div class="flex gap-1 cursor-pointer" id="my-star-picker-${reviewId}">
                ${[1, 2, 3, 4, 5].map(i => `
                    <button type="button" data-star="${i}"
                            style="color:${i <= currRating ? '#f97316' : '#d1d5db'};font-size:1.75rem;line-height:1"
                            aria-label="${i} star${i > 1 ? 's' : ''}">&#9733;</button>`).join('')}
            </div>
            <input type="hidden" id="my-rating-${reviewId}" value="${currRating}">
            <textarea id="my-body-${reviewId}" rows="3" maxlength="1000"
                      class="w-full border border-gray-200 rounded-lg p-3 text-sm resize-none
                             focus:outline-none focus:ring-2 focus:ring-orange-300">${_esc(currBody)}</textarea>
            <p class="text-xs text-red-500 my-edit-form-error min-h-[1rem]"></p>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-1.5 bg-orange-500 text-white text-xs font-medium rounded-full
                               hover:bg-orange-600 transition disabled:opacity-50">
                    Save
                </button>
                <button type="button" data-action="cancel-my-review-edit" data-id="${reviewId}"
                        class="px-4 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>`;

    const picker = document.getElementById(`my-star-picker-${reviewId}`);
    const hidden = document.getElementById(`my-rating-${reviewId}`);
    if (picker && hidden) _wireStarPicker(picker, hidden);

    card.querySelector('.edit-my-review-form')?.addEventListener('submit', async ev => {
        ev.preventDefault();
        await _submitEditMyReview(reviewId);
    });
}

async function _submitEditMyReview(reviewId) {
    const card    = myReviewsContainer?.querySelector(`[data-my-review-id="${reviewId}"]`);
    const rating  = Number(document.getElementById(`my-rating-${reviewId}`)?.value ?? 0);
    const body    = document.getElementById(`my-body-${reviewId}`)?.value.trim() ?? '';
    const errEl   = card?.querySelector('.my-edit-form-error');
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

        if (card) card.outerHTML = _renderMyReviewCard(updated);
        showToast('Review updated.', 'success');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not update review.';
        if (errEl) errEl.textContent = msg;
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }
        console.error('[SID_23] Edit review failed:', err);
    }
}

function _cancelEditMyReview(reviewId) {
    const cached = _myReviewCache.get(Number(reviewId));
    const card   = myReviewsContainer?.querySelector(`[data-my-review-id="${reviewId}"]`);
    if (!card) return;

    if (cached) {
        card.outerHTML = _renderMyReviewCard(cached);
    } else {
        loadMyReviews(1);
    }
}

async function _deleteMyReview(reviewId) {
    if (!confirm('Delete this review?')) return;

    try {
        await apiFetch(`/api/reviews/${reviewId}`, { method: 'DELETE' });

        myReviewsContainer?.querySelector(`[data-my-review-id="${reviewId}"]`)?.remove();
        _myReviewCache.delete(Number(reviewId));

        if (!myReviewsContainer?.querySelector('.my-review-item')) {
            myReviewsContainer.innerHTML =
                "<p class=\"text-sm text-gray-400 py-8 text-center\">You haven't written any reviews yet.</p>";
        }

        showToast('Review deleted.', 'info');
    } catch (err) {
        showToast(err.data?.message ?? 'Could not delete review.', 'error');
        console.error('[SID_23] Delete review failed:', err);
    }
}

// ── SID_24: Edit Profile ──────────────────────────────────────────────────────

let _pendingAvatar = null;

avatarInput?.addEventListener('change', e => {
    const file = e.target.files?.[0];
    if (!file) return;
    _pendingAvatar = file;

    const reader = new FileReader();
    reader.onload = ev => {
        if (avatarPreview) {
            avatarPreview.innerHTML =
                `<img src="${ev.target.result}" alt="Avatar preview" class="w-full h-full object-cover">`;
        }
    };
    reader.readAsDataURL(file);
});

function loadProfile() {
    apiFetch('/api/user/profile')
        .then(res => {
            _profileLoaded = true;
            const data = res.data ?? res;

            if (profileNameInput     && data.name     != null) profileNameInput.value     = data.name;
            if (profileEmailInput    && data.email    != null) profileEmailInput.value    = data.email;
            if (profileLocationInput && data.location != null) profileLocationInput.value = data.location;

            if (data.avatar_url && avatarPreview) {
                avatarPreview.innerHTML =
                    `<img src="${_esc(data.avatar_url)}" alt="Avatar" class="w-full h-full object-cover">`;
            }
        })
        .catch(err => {
            console.error('[SID_24] Load profile failed:', err);
            _profileLoaded = true;
        });
}

profileForm?.addEventListener('submit', async e => {
    e.preventDefault();
    if (profileFormError) profileFormError.textContent = '';

    const name     = profileNameInput?.value.trim()     ?? '';
    const email    = profileEmailInput?.value.trim()    ?? '';
    const location = profileLocationInput?.value.trim() ?? '';

    if (!name) {
        if (profileFormError) profileFormError.textContent = 'Name is required.';
        return;
    }

    if (profileSaveBtn) { profileSaveBtn.disabled = true; profileSaveBtn.textContent = 'Saving…'; }

    try {
        let body;

        if (_pendingAvatar) {
            body = new FormData();
            body.append('name',     name);
            body.append('email',    email);
            body.append('location', location);
            body.append('avatar',   _pendingAvatar);
        } else {
            body = { name, email, location };
        }

        const res     = await apiFetch('/api/user/profile', { method: 'PUT', body });
        const updated = res.data ?? res;

        _pendingAvatar = null;

        if (profileNameDisplay)  profileNameDisplay.textContent  = updated.name  ?? name;
        if (profileEmailDisplay) profileEmailDisplay.textContent = updated.email ?? email;

        // Update navbar name
        const navbarName = document.querySelector('.bs-user-menu__name');
        if (navbarName) navbarName.textContent = updated.name ?? name;

        if (updated.avatar_url) {
            const imgTag = `<img src="${_esc(updated.avatar_url)}" alt="Avatar" class="w-full h-full object-cover">`;
            if (avatarPreview) avatarPreview.innerHTML = imgTag;

            // Re-select hero to ensure we have the current DOM element
            const currentHero = document.getElementById('profile-avatar-hero');
            if (currentHero) {
                if (currentHero.tagName !== 'IMG') {
                    currentHero.outerHTML =
                        `<img id="profile-avatar-hero" src="${_esc(updated.avatar_url)}" alt="${_esc(updated.name ?? name)}"
                              class="w-20 h-20 rounded-full object-cover border-2 border-white/50 shadow">`;
                } else {
                    currentHero.src = updated.avatar_url;
                }
            }
        }

        showToast('Profile updated!', 'success');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not save profile.';
        if (profileFormError) profileFormError.textContent = msg;
        console.error('[SID_24] Save profile failed:', err);
    } finally {
        if (profileSaveBtn) { profileSaveBtn.disabled = false; profileSaveBtn.textContent = 'Save Changes'; }
    }
});
