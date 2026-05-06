// public/js/vendor-menu.js — SID_27: Menu CRUD fetch + form validation

// ── DOM refs ──────────────────────────────────────────────────────────────────

const itemsContainer = document.getElementById('menu-items-container');
const formPanel      = document.getElementById('menu-form-panel');
const formTitle      = document.getElementById('menu-form-title');
const itemForm       = document.getElementById('menu-item-form');
const editIdInput    = document.getElementById('menu-edit-id');
const nameInput      = document.getElementById('menu-name');
const priceInput     = document.getElementById('menu-price');
const categoryInput  = document.getElementById('menu-category');
const descInput      = document.getElementById('menu-description');
const availInput     = document.getElementById('menu-available');
const nameErr        = document.getElementById('menu-name-error');
const priceErr       = document.getElementById('menu-price-error');
const formErr        = document.getElementById('menu-form-error');
const submitBtn      = document.getElementById('menu-submit-btn');
const addBtn         = document.getElementById('menu-add-btn');

// ── Helpers ───────────────────────────────────────────────────────────────────

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function _formatPrice(price) {
    return '₱' + Number(price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// ── State ─────────────────────────────────────────────────────────────────────

const _cache = new Map(); // id → item

// ── Render ────────────────────────────────────────────────────────────────────

function _renderItem(item) {
    return `
        <div data-menu-item-id="${item.id}" class="flex items-start gap-3 px-5 py-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-800">${_esc(item.name)}</span>
                    ${item.is_available
                        ? ''
                        : '<span class="text-xs bg-gray-100 text-gray-400 px-2 py-0.5 rounded-full">Unavailable</span>'}
                </div>
                ${item.description
                    ? `<p class="text-xs text-gray-500 mt-0.5 leading-relaxed">${_esc(item.description)}</p>`
                    : ''}
                <span class="text-sm font-medium text-orange-500 mt-1 block">${_formatPrice(item.price)}</span>
            </div>
            <div class="flex items-center gap-3 shrink-0 pt-0.5">
                <button type="button" data-action="edit-item" data-id="${item.id}"
                        class="text-xs text-gray-500 hover:text-orange-500 transition font-medium">
                    Edit
                </button>
                <button type="button" data-action="delete-item" data-id="${item.id}"
                        class="text-xs text-gray-500 hover:text-red-500 transition font-medium">
                    Delete
                </button>
            </div>
        </div>`;
}

function _renderItems(items) {
    if (!items.length) {
        itemsContainer.innerHTML =
            '<p class="text-sm text-gray-400 py-8 text-center">No menu items yet. Add your first item!</p>';
        return;
    }

    const groups = new Map();
    for (const item of items) {
        const cat = item.category || 'Uncategorized';
        if (!groups.has(cat)) groups.set(cat, []);
        groups.get(cat).push(item);
    }

    let html = '';
    for (const [cat, catItems] of groups) {
        html += `<div class="px-5 pt-3 pb-1">
                     <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">${_esc(cat)}</p>
                 </div>
                 <div class="divide-y divide-gray-100">${catItems.map(_renderItem).join('')}</div>`;
    }
    itemsContainer.innerHTML = html;
}

// ── Load items ────────────────────────────────────────────────────────────────

function loadItems() {
    itemsContainer.innerHTML =
        '<p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading menu…</p>';

    apiFetch('/api/vendor/menu')
        .then(res => {
            const items = Array.isArray(res) ? res : (res.data ?? []);
            _cache.clear();
            items.forEach(i => _cache.set(i.id, i));
            _renderItems(items);
        })
        .catch(err => {
            console.error('[SID_27] Menu load failed:', err);
            itemsContainer.innerHTML =
                '<p class="text-sm text-red-400 py-8 text-center">Could not load menu items.</p>';
        });
}

// ── Form open / close ─────────────────────────────────────────────────────────

function _clearErrors() {
    nameErr.textContent  = '';
    priceErr.textContent = '';
    formErr.textContent  = '';
}

function openForm(item = null) {
    _clearErrors();
    editIdInput.value    = item ? item.id : '';
    nameInput.value      = item?.name        ?? '';
    priceInput.value     = item?.price       ?? '';
    categoryInput.value  = item?.category    ?? '';
    descInput.value      = item?.description ?? '';
    availInput.checked   = item ? !!item.is_available : true;
    formTitle.textContent = item ? 'Edit Item' : 'Add Item';
    submitBtn.disabled    = false;
    submitBtn.textContent = 'Save Item';

    formPanel.classList.remove('hidden');
    formPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    nameInput.focus();
}

function closeForm() {
    formPanel.classList.add('hidden');
    itemForm.reset();
    _clearErrors();
}

// ── Validation ────────────────────────────────────────────────────────────────

function _validate() {
    let valid = true;
    _clearErrors();

    if (!nameInput.value.trim()) {
        nameErr.textContent = 'Item name is required.';
        valid = false;
    }

    const price = parseFloat(priceInput.value);
    if (priceInput.value.trim() === '' || isNaN(price) || price < 0) {
        priceErr.textContent = 'Enter a valid price (0 or more).';
        valid = false;
    }

    return valid;
}

// ── Submit (add or edit) ──────────────────────────────────────────────────────

async function _submitForm(e) {
    e.preventDefault();
    if (!_validate()) return;

    const id     = editIdInput.value;
    const isEdit = !!id;
    const body   = {
        name:         nameInput.value.trim(),
        price:        parseFloat(priceInput.value),
        category:     categoryInput.value.trim() || null,
        description:  descInput.value.trim()     || null,
        is_available: availInput.checked,
    };

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Saving…';

    try {
        const res = await apiFetch(
            isEdit ? `/api/vendor/menu/${id}` : '/api/vendor/menu',
            { method: isEdit ? 'PUT' : 'POST', body }
        );

        const saved   = (!res.data || Array.isArray(res.data)) ? res : res.data;
        const itemId  = saved.id ?? (isEdit ? Number(id) : Date.now());
        const merged  = { ...(isEdit ? _cache.get(Number(id)) : {}), ...body, id: itemId, ...saved };

        _cache.set(itemId, merged);
        _renderItems([..._cache.values()]);
        closeForm();
        showToast(isEdit ? 'Item updated.' : 'Item added.', 'success');
    } catch (err) {
        const errs  = err.data?.errors ?? {};
        const first = Object.values(errs)[0]?.[0];
        const msg   = first ?? err.data?.message ?? 'Could not save item.';
        if (errs.name)  nameErr.textContent  = errs.name[0];
        if (errs.price) priceErr.textContent = errs.price[0];
        if (!errs.name && !errs.price) formErr.textContent = msg;
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Save Item';
        console.error('[SID_27] Menu item save failed:', err);
    }
}

// ── Delete ────────────────────────────────────────────────────────────────────

async function _deleteItem(id) {
    if (!confirm('Delete this menu item?')) return;

    try {
        await apiFetch(`/api/vendor/menu/${id}`, { method: 'DELETE' });
        _cache.delete(Number(id));
        _renderItems([..._cache.values()]);
        showToast('Item deleted.', 'success');
    } catch (err) {
        showToast(err.data?.message ?? 'Could not delete item.', 'error');
        console.error('[SID_27] Menu item delete failed:', err);
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

itemsContainer.addEventListener('click', e => {
    const editBtn   = e.target.closest('[data-action="edit-item"]');
    const deleteBtn = e.target.closest('[data-action="delete-item"]');
    if (editBtn)   openForm(_cache.get(Number(editBtn.dataset.id)));
    if (deleteBtn) _deleteItem(deleteBtn.dataset.id);
});

addBtn.addEventListener('click', () => openForm());
document.getElementById('menu-form-close')?.addEventListener('click', closeForm);
document.getElementById('menu-cancel-btn')?.addEventListener('click', closeForm);
itemForm.addEventListener('submit', _submitForm);

// ── Init ──────────────────────────────────────────────────────────────────────

loadItems();
