// public/js/vendor-promotions.js — SID_29: Promotions form fetch + validation

// ── DOM refs ──────────────────────────────────────────────────────────────────

const promosContainer = document.getElementById('promotions-container');
const formPanel       = document.getElementById('promo-form-panel');
const promoForm       = document.getElementById('promo-form');
const editIdInput     = document.getElementById('promo-edit-id');
const titleInput      = document.getElementById('promo-title');
const descInput       = document.getElementById('promo-description');
const discountInput   = document.getElementById('promo-discount');
const startDateInput  = document.getElementById('promo-start-date');
const endDateInput    = document.getElementById('promo-end-date');
const activeInput     = document.getElementById('promo-active');
const titleErrEl      = document.getElementById('promo-title-error');
const formErrEl       = document.getElementById('promo-form-error');
const submitBtn       = document.getElementById('promo-submit-btn');
const addBtn          = document.getElementById('promo-add-btn');
const cancelBtn       = document.getElementById('promo-cancel-btn');

// ── Helpers ───────────────────────────────────────────────────────────────────

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric',
    });
}

// ── State ─────────────────────────────────────────────────────────────────────

const _cache = new Map(); // id → promo

// ── Render ────────────────────────────────────────────────────────────────────

function _renderPromo(promo) {
    const dates = [promo.start_date, promo.end_date].filter(Boolean).map(_formatDate).join(' – ');

    return `
        <div data-promo-id="${promo.id}" class="flex items-start gap-3 px-5 py-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-800">${_esc(promo.title)}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                 ${promo.is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'}">
                        ${promo.is_active ? 'Active' : 'Inactive'}
                    </span>
                </div>
                ${promo.discount
                    ? `<p class="text-xs font-medium text-orange-500 mt-0.5">${_esc(promo.discount)}</p>`
                    : ''}
                ${promo.description
                    ? `<p class="text-xs text-gray-500 mt-0.5 leading-relaxed">${_esc(promo.description)}</p>`
                    : ''}
                ${dates ? `<p class="text-xs text-gray-400 mt-1">${_esc(dates)}</p>` : ''}
            </div>
            <div class="flex items-center gap-3 shrink-0 pt-0.5">
                <button type="button" data-action="edit-promo" data-id="${promo.id}"
                        class="text-xs text-gray-500 hover:text-orange-500 transition font-medium">
                    Edit
                </button>
                <button type="button" data-action="delete-promo" data-id="${promo.id}"
                        class="text-xs text-gray-500 hover:text-red-500 transition font-medium">
                    Delete
                </button>
            </div>
        </div>`;
}

function _renderPromos(promos) {
    if (!promos.length) {
        promosContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center">No promotions yet. Add one to attract more customers!</p>';
        return;
    }
    promosContainer.innerHTML = promos.map(_renderPromo).join('');
}

// ── Load promotions ───────────────────────────────────────────────────────────

function loadPromos() {
    promosContainer.innerHTML =
        '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading promotions…</p>';

    apiFetch('/api/vendor/promotions')
        .then(res => {
            const promos = Array.isArray(res) ? res : (Array.isArray(res.data) ? res.data : []);
            _cache.clear();
            promos.forEach(p => _cache.set(p.id, p));
            _renderPromos(promos);
        })
        .catch(err => {
            console.error('[SID_29] Promotions load failed:', err);
            promosContainer.innerHTML =
                '<p class="text-sm text-red-400 py-8 text-center">Could not load promotions.</p>';
        });
}

// ── Form open / close ─────────────────────────────────────────────────────────

function _clearErrors() {
    titleErrEl.textContent = '';
    formErrEl.textContent  = '';
}

function openForm(promo = null) {
    _clearErrors();
    editIdInput.value    = promo ? promo.id : '';
    titleInput.value     = promo?.title       ?? '';
    descInput.value      = promo?.description ?? '';
    discountInput.value  = promo?.discount    ?? '';
    startDateInput.value = promo?.start_date  ?? '';
    endDateInput.value   = promo?.end_date    ?? '';
    activeInput.checked  = promo ? !!promo.is_active : true;
    submitBtn.disabled    = false;
    submitBtn.textContent = 'Save Promo';

    formPanel.classList.remove('hidden');
    titleInput.focus();
}

function closeForm() {
    formPanel.classList.add('hidden');
    promoForm.reset();
    _clearErrors();
}

// ── Validation ────────────────────────────────────────────────────────────────

function _validate() {
    _clearErrors();
    if (!titleInput.value.trim()) {
        titleErrEl.textContent = 'Title is required.';
        return false;
    }
    const start = startDateInput.value;
    const end   = endDateInput.value;
    if (start && end && end < start) {
        formErrEl.textContent = 'End date must be on or after the start date.';
        return false;
    }
    return true;
}

// ── Submit (add or edit) ──────────────────────────────────────────────────────

promoForm.addEventListener('submit', async e => {
    e.preventDefault();
    if (!_validate()) return;

    const id     = editIdInput.value;
    const isEdit = !!id;
    const body   = {
        title:       titleInput.value.trim(),
        description: descInput.value.trim()    || null,
        discount:    discountInput.value.trim() || null,
        start_date:  startDateInput.value       || null,
        end_date:    endDateInput.value         || null,
        is_active:   activeInput.checked,
    };

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Saving…';

    try {
        const res  = await apiFetch(
            isEdit ? `/api/vendor/promotions/${id}` : '/api/vendor/promotions',
            { method: isEdit ? 'PUT' : 'POST', body }
        );

        const saved  = (!res.data || Array.isArray(res.data)) ? res : res.data;
        const promoId = saved.id ?? (isEdit ? Number(id) : Date.now());
        const merged  = { ...(isEdit ? _cache.get(Number(id)) : {}), ...body, id: promoId, ...saved };

        _cache.set(promoId, merged);
        _renderPromos([..._cache.values()]);
        closeForm();
        showToast(isEdit ? 'Promo updated.' : 'Promo added.', 'success');
    } catch (err) {
        const errs  = err.data?.errors ?? {};
        const first = Object.values(errs)[0]?.[0];
        const msg   = first ?? err.data?.message ?? 'Could not save promo.';
        if (errs.title) titleErrEl.textContent = errs.title[0];
        else formErrEl.textContent = msg;
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Save Promo';
        console.error('[SID_29] Promo save failed:', err);
    }
});

// ── Delete ────────────────────────────────────────────────────────────────────

async function _deletePromo(id) {
    if (!confirm('Delete this promotion?')) return;

    try {
        await apiFetch(`/api/vendor/promotions/${id}`, { method: 'DELETE' });
        _cache.delete(Number(id));
        _renderPromos([..._cache.values()]);
        showToast('Promo deleted.', 'success');
    } catch (err) {
        showToast(err.data?.message ?? 'Could not delete promo.', 'error');
        console.error('[SID_29] Promo delete failed:', err);
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

promosContainer.addEventListener('click', e => {
    const editBtn   = e.target.closest('[data-action="edit-promo"]');
    const deleteBtn = e.target.closest('[data-action="delete-promo"]');
    if (editBtn)   openForm(_cache.get(Number(editBtn.dataset.id)));
    if (deleteBtn) _deletePromo(deleteBtn.dataset.id);
});

addBtn.addEventListener('click', () => openForm());
cancelBtn.addEventListener('click', closeForm);

// ── Init ──────────────────────────────────────────────────────────────────────

loadPromos();
