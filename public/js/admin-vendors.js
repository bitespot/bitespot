// public/js/admin-vendors.js — SID_31: Vendor approval UI

// ── DOM refs ──────────────────────────────────────────────────────────────────

const vendorList       = document.getElementById('admin-vendor-list');
const emptyState       = document.getElementById('admin-vendor-empty');
const rejectModal      = document.getElementById('admin-reject-modal');
const rejectForm       = document.getElementById('admin-reject-form');
const rejectIdInput    = document.getElementById('admin-reject-vendor-id');
const rejectReasonInput= document.getElementById('admin-reject-reason');
const rejectError      = document.getElementById('admin-reject-error');
const rejectSubmitBtn  = document.getElementById('admin-reject-submit');
const rejectCancelBtn  = document.getElementById('admin-reject-cancel');

// ── Helpers ───────────────────────────────────────────────────────────────────

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _formatDate(str) {
    if (!str) return '';
    return new Date(str).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'short', day: 'numeric',
    });
}

// ── Render ────────────────────────────────────────────────────────────────────

function _renderVendorCard(vendor) {
    const owner = vendor.owner ?? vendor.user ?? {};
    return `
        <div data-vendor-card="${vendor.id}"
             class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">
                    ${_esc(vendor.name)}
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    ${_esc(vendor.category ?? vendor.type ?? 'Uncategorized')}
                    &middot; ${_esc(owner.name ?? 'Unknown owner')}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Submitted ${_formatDate(vendor.created_at)}
                </p>
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="button" data-action="approve" data-id="${vendor.id}"
                        class="px-4 py-1.5 bg-green-500 text-white text-xs font-medium rounded-full
                               hover:bg-green-600 transition disabled:opacity-50">
                    Approve
                </button>
                <button type="button" data-action="reject" data-id="${vendor.id}"
                        class="px-4 py-1.5 bg-red-500 text-white text-xs font-medium rounded-full
                               hover:bg-red-600 transition disabled:opacity-50">
                    Reject
                </button>
            </div>
        </div>`;
}

// ── State ─────────────────────────────────────────────────────────────────────

let _loading = false;
const _cache = new Map();

// ── Load pending vendors ──────────────────────────────────────────────────────

function loadPendingVendors() {
    if (_loading) return;
    _loading = true;

    vendorList.innerHTML =
        '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading pending vendors…</p>';
    emptyState?.classList.add('hidden');

    apiFetch('/api/admin/vendors/pending')
        .then(res => {
            const vendors = res.data ?? [];
            _cache.clear();
            vendors.forEach(v => _cache.set(v.id, v));

            vendorList.innerHTML = '';

            if (!vendors.length) {
                emptyState?.classList.remove('hidden');
                return;
            }

            vendorList.innerHTML = vendors
                .map(_renderVendorCard)
                .join('<div class="border-t border-gray-100"></div>');
        })
        .catch(err => {
            console.error('[SID_31] Failed to load pending vendors:', err);
            vendorList.innerHTML =
                '<p class="text-sm text-red-400 py-8 text-center">Could not load vendors. Try refreshing.</p>';
        })
        .finally(() => { _loading = false; });
}

// ── Approve ───────────────────────────────────────────────────────────────────

async function _approveVendor(vendorId) {
    const card = vendorList.querySelector(`[data-vendor-card="${vendorId}"]`);
    const btn  = card?.querySelector('[data-action="approve"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Approving…'; }

    try {
        await apiFetch(`/api/admin/vendors/${vendorId}/approve`, { method: 'POST' });
        _removeCard(vendorId);
        showToast('Vendor approved.', 'success');
    } catch (err) {
        const msg = err.data?.message ?? 'Could not approve vendor.';
        showToast(msg, 'error');
        if (btn) { btn.disabled = false; btn.textContent = 'Approve'; }
        console.error('[SID_31] Approve failed:', err);
    }
}

// ── Reject modal ──────────────────────────────────────────────────────────────

function _openRejectModal(vendorId) {
    rejectIdInput.value     = vendorId;
    rejectReasonInput.value = '';
    if (rejectError) rejectError.textContent = '';
    rejectModal?.classList.remove('hidden');
    rejectReasonInput?.focus();
}

function _closeRejectModal() {
    rejectModal?.classList.add('hidden');
    rejectIdInput.value = '';
}

rejectCancelBtn?.addEventListener('click', _closeRejectModal);

rejectModal?.addEventListener('click', e => {
    if (e.target === rejectModal) _closeRejectModal();
});

rejectForm?.addEventListener('submit', async e => {
    e.preventDefault();
    const vendorId = rejectIdInput.value;
    const reason   = rejectReasonInput.value.trim();

    if (rejectError) rejectError.textContent = '';
    if (rejectSubmitBtn) { rejectSubmitBtn.disabled = true; rejectSubmitBtn.textContent = 'Rejecting…'; }

    try {
        await apiFetch(`/api/admin/vendors/${vendorId}/reject`, {
            method: 'POST',
            body:   reason ? { reason } : {},
        });
        _closeRejectModal();
        _removeCard(vendorId);
        showToast('Vendor rejected.', 'warning');
    } catch (err) {
        const msg = err.data?.message
            ?? Object.values(err.data?.errors ?? {})[0]?.[0]
            ?? 'Could not reject vendor.';
        if (rejectError) rejectError.textContent = msg;
        console.error('[SID_31] Reject failed:', err);
    } finally {
        if (rejectSubmitBtn) {
            rejectSubmitBtn.disabled = false;
            rejectSubmitBtn.textContent = 'Reject Vendor';
        }
    }
});

// ── Remove card from DOM ──────────────────────────────────────────────────────

function _removeCard(vendorId) {
    const card = vendorList.querySelector(`[data-vendor-card="${vendorId}"]`);
    const sep  = card?.previousElementSibling ?? card?.nextElementSibling;
    if (sep && !sep.hasAttribute('data-vendor-card')) sep.remove();
    card?.remove();
    _cache.delete(Number(vendorId));

    if (!vendorList.querySelector('[data-vendor-card]')) {
        vendorList.innerHTML = '';
        emptyState?.classList.remove('hidden');
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

vendorList?.addEventListener('click', e => {
    const approveBtn = e.target.closest('[data-action="approve"]');
    const rejectBtn  = e.target.closest('[data-action="reject"]');

    if (approveBtn) _approveVendor(approveBtn.dataset.id);
    if (rejectBtn)  _openRejectModal(rejectBtn.dataset.id);
});

// ── Init ──────────────────────────────────────────────────────────────────────

loadPendingVendors();
