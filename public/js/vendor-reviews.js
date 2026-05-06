// public/js/vendor-reviews.js — SID_26: review reply inline form

// ── DOM refs ──────────────────────────────────────────────────────────────────

const reviewsContainer = document.getElementById('vendor-reviews-container');
const loadMoreWrap     = document.getElementById('vendor-reviews-load-more-wrap');
const loadMoreBtn      = document.getElementById('vendor-reviews-load-more');

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

// ── Render helpers ────────────────────────────────────────────────────────────

function _renderReplyStatic(reviewId, replyBody) {
    return `
        <div data-reply-display="${reviewId}"
             class="mt-3 ml-4 pl-3 border-l-2 border-orange-200 space-y-1">
            <p class="text-xs font-medium text-orange-500">Your reply</p>
            <p class="text-sm text-gray-600">${_esc(replyBody)}</p>
            <button type="button" data-action="open-reply" data-id="${reviewId}"
                    class="text-xs text-gray-400 hover:text-orange-500 transition">
                Edit reply
            </button>
        </div>
        <div data-reply-form="${reviewId}" class="hidden mt-2"></div>`;
}

function _renderReplyPrompt(reviewId) {
    return `
        <div data-reply-display="${reviewId}" class="mt-3">
            <button type="button" data-action="open-reply" data-id="${reviewId}"
                    class="text-xs font-medium text-orange-500 hover:text-orange-600 transition">
                Reply to this review
            </button>
        </div>
        <div data-reply-form="${reviewId}" class="hidden mt-2"></div>`;
}

function _renderReviewCard(review) {
    const user   = review.user ?? {};
    const avatar = user.avatar
        ? `<img src="${_esc(user.avatar)}" alt="${_esc(user.name)}"
                class="w-8 h-8 rounded-full object-cover">`
        : `<div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center
                       text-orange-500 text-sm font-bold shrink-0">
               ${_esc((user.name ?? '?').charAt(0).toUpperCase())}
           </div>`;

    const date = review.created_at
        ? new Date(review.created_at).toLocaleDateString('en-PH', {
              year: 'numeric', month: 'short', day: 'numeric',
          })
        : '';

    const replyHtml = review.reply
        ? _renderReplyStatic(review.id, review.reply.body)
        : _renderReplyPrompt(review.id);

    return `
        <div data-vendor-review-id="${review.id}" class="p-4">
            <div class="flex items-start gap-3">
                <div class="shrink-0">${avatar}</div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-800 truncate">
                            ${_esc(user.name ?? 'Anonymous')}
                        </span>
                        <span class="text-xs text-gray-400 shrink-0">${_esc(date)}</span>
                    </div>
                    <div class="flex items-center gap-0.5 mt-0.5">${_starsHtml(review.rating)}</div>
                    ${review.body
                        ? `<p class="text-sm text-gray-600 mt-1 leading-relaxed">${_esc(review.body)}</p>`
                        : ''}
                    ${replyHtml}
                </div>
            </div>
        </div>`;
}

// ── State ─────────────────────────────────────────────────────────────────────

let _page     = 1;
let _lastPage = 1;
let _loading  = false;
const _cache  = new Map();

// ── Load reviews ──────────────────────────────────────────────────────────────

function loadReviews(page) {
    if (_loading) return;
    _loading = true;

    if (page === 1) {
        reviewsContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading reviews…</p>';
    }

    apiFetch(`/api/vendor/reviews?page=${page}`)
        .then(res => {
            const reviews = res.data ?? [];
            _lastPage     = res.last_page ?? 1;
            _page         = res.current_page ?? page;

            if (page === 1) reviewsContainer.innerHTML = '';

            if (!reviews.length && page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-gray-400 py-8 text-center">No reviews yet.</p>';
            } else {
                reviews.forEach(r => _cache.set(r.id, r));
                reviewsContainer.insertAdjacentHTML(
                    'beforeend', reviews.map(_renderReviewCard).join('')
                );
            }

            loadMoreWrap?.classList.toggle('hidden', _page >= _lastPage);
        })
        .catch(err => {
            console.error('[SID_26] Reviews fetch failed:', err);
            if (page === 1) {
                reviewsContainer.innerHTML =
                    '<p class="text-sm text-red-400 py-8 text-center">Could not load reviews.</p>';
            }
        })
        .finally(() => { _loading = false; });
}

loadMoreBtn?.addEventListener('click', () => loadReviews(_page + 1));

// ── Inline reply form ─────────────────────────────────────────────────────────

function _getCard(reviewId) {
    return reviewsContainer.querySelector(`[data-vendor-review-id="${reviewId}"]`);
}

function _openReplyForm(reviewId) {
    const card = _getCard(reviewId);
    if (!card) return;

    const review   = _cache.get(Number(reviewId));
    const existing = review?.reply?.body ?? '';
    const isEdit   = !!review?.reply;

    const displayEl = card.querySelector(`[data-reply-display="${reviewId}"]`);
    const formEl    = card.querySelector(`[data-reply-form="${reviewId}"]`);
    if (!displayEl || !formEl) return;

    displayEl.classList.add('hidden');
    formEl.classList.remove('hidden');
    formEl.innerHTML = `
        <form data-reply-submit-form="${reviewId}" class="space-y-2">
            <textarea data-reply-body="${reviewId}" rows="3" maxlength="1000"
                      placeholder="Write your reply…"
                      class="w-full border border-gray-200 rounded-lg p-3 text-sm resize-none
                             focus:outline-none focus:ring-2 focus:ring-orange-300">${_esc(existing)}</textarea>
            <p data-reply-error="${reviewId}" class="text-xs text-red-500 min-h-[1rem]"></p>
            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-1.5 bg-orange-500 text-white text-xs font-medium rounded-full
                               hover:bg-orange-600 transition disabled:opacity-50">
                    ${isEdit ? 'Update Reply' : 'Post Reply'}
                </button>
                <button type="button" data-action="cancel-reply" data-id="${reviewId}"
                        class="px-4 py-1.5 text-xs text-gray-500 hover:text-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>`;

    formEl.querySelector(`[data-reply-submit-form="${reviewId}"]`)
          ?.addEventListener('submit', e => {
              e.preventDefault();
              _submitReply(reviewId, isEdit);
          });

    formEl.querySelector('textarea')?.focus();
}

function _closeReplyForm(reviewId) {
    const card = _getCard(reviewId);
    if (!card) return;

    const displayEl = card.querySelector(`[data-reply-display="${reviewId}"]`);
    const formEl    = card.querySelector(`[data-reply-form="${reviewId}"]`);
    if (!displayEl || !formEl) return;

    formEl.classList.add('hidden');
    formEl.innerHTML = '';
    displayEl.classList.remove('hidden');
}

async function _submitReply(reviewId, isEdit) {
    const card   = _getCard(reviewId);
    const bodyEl = card?.querySelector(`[data-reply-body="${reviewId}"]`);
    const errEl  = card?.querySelector(`[data-reply-error="${reviewId}"]`);
    const btn    = card?.querySelector(`[data-reply-submit-form="${reviewId}"] [type="submit"]`);

    const body = bodyEl?.value.trim() ?? '';
    if (!body) {
        if (errEl) errEl.textContent = 'Reply cannot be empty.';
        return;
    }
    if (errEl) errEl.textContent = '';
    if (btn) { btn.disabled = true; btn.textContent = 'Posting…'; }

    try {
        const res = await apiFetch(`/api/vendor/reviews/${reviewId}/reply`, {
            method: isEdit ? 'PUT' : 'POST',
            body:   { body },
        });

        // Update cache
        const cached = _cache.get(Number(reviewId));
        if (cached) {
            cached.reply = res.data ?? { body };
            _cache.set(Number(reviewId), cached);
        }

        // Update the display in-place and hide the form
        const displayEl = card?.querySelector(`[data-reply-display="${reviewId}"]`);
        const formEl    = card?.querySelector(`[data-reply-form="${reviewId}"]`);
        if (displayEl) {
            displayEl.className = 'mt-3 ml-4 pl-3 border-l-2 border-orange-200 space-y-1';
            displayEl.innerHTML = `
                <p class="text-xs font-medium text-orange-500">Your reply</p>
                <p class="text-sm text-gray-600">${_esc(body)}</p>
                <button type="button" data-action="open-reply" data-id="${reviewId}"
                        class="text-xs text-gray-400 hover:text-orange-500 transition">
                    Edit reply
                </button>`;
            displayEl.classList.remove('hidden');
        }
        if (formEl) { formEl.classList.add('hidden'); formEl.innerHTML = ''; }

        showToast(isEdit ? 'Reply updated.' : 'Reply posted.', 'success');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not post reply.';
        if (errEl) errEl.textContent = msg;
        if (btn) { btn.disabled = false; btn.textContent = isEdit ? 'Update Reply' : 'Post Reply'; }
        console.error('[SID_26] Reply submit failed:', err);
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

reviewsContainer?.addEventListener('click', e => {
    const openBtn   = e.target.closest('[data-action="open-reply"]');
    const cancelBtn = e.target.closest('[data-action="cancel-reply"]');

    if (openBtn)   _openReplyForm(openBtn.dataset.id);
    if (cancelBtn) _closeReplyForm(cancelBtn.dataset.id);
});

// ── Init ──────────────────────────────────────────────────────────────────────

loadReviews(1);
