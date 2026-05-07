(function () {

/* ═══════════════════════════════════════════
   UTILITIES
═══════════════════════════════════════════ */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

async function api(method, url, body) {
    const opts = {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, Accept: 'application/json' },
    };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(url, opts);
    if (!r.ok) throw new Error(await r.text());
    return r.json();
}

function toast(msg, type = 'success') {
    const el = document.getElementById('vd-toast');
    const msgEl = document.getElementById('vd-toast-msg');
    el.className = 'vd-toast ' + type;
    msgEl.textContent = msg;
    el.classList.add('show');
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), 3200);
}

function stars(n) {
    let s = '';
    for (let i = 1; i <= 5; i++) {
        s += `<svg width="12" height="12" viewBox="0 0 24 24" fill="${i <= n ? '#f59e0b' : 'none'}" stroke="${i <= n ? '#f59e0b' : '#d1d5db'}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
    }
    return s;
}

function deltaEl(val, suffix = '', isRating = false) {
    let cls, arrow, label;
    if (val > 0) { cls = 'up'; arrow = '↑'; label = '+' + val + suffix; }
    else if (val < 0) { cls = 'down'; arrow = '↓'; label = val + suffix; }
    else { cls = 'flat'; arrow = ''; label = 'No change'; }
    return `<span class="vd-kpi-delta ${cls}">${arrow} ${label} this week</span>`;
}

function timeAgo(dateStr) {
    const diff = (Date.now() - new Date(dateStr)) / 1000;
    if (diff < 60) return 'just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
}

/* ═══════════════════════════════════════════
   TAB SWITCHER
═══════════════════════════════════════════ */
const tabTitles  = { overview: 'Overview', reviews: 'Reviews', menu: 'Menu Items', media: 'Photos & Promos', settings: 'Settings' };
const tabActions = {
    overview: '',
    reviews:  '',
    menu:     `<button class="vd-btn vd-btn-primary" onclick="openMenuModal()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add Item</button>`,
    media:    '',
    settings: '',
};

let currentTab = 'overview';

window.switchTab = function (tab, btn) {
    document.getElementById('tab-' + currentTab).style.display = 'none';
    document.querySelectorAll('.vd-nav-btn').forEach(b => b.classList.remove('active'));

    currentTab = tab;
    document.getElementById('tab-' + tab).style.display = '';
    document.getElementById('vd-page-title').textContent = tabTitles[tab];
    document.getElementById('vd-topbar-actions').innerHTML = tabActions[tab];
    if (btn) btn.classList.add('active');

    // lazy load
    if (tab === 'overview' && !window._overviewLoaded) loadOverview();
    if (tab === 'reviews'  && !window._reviewsLoaded)  loadReviews();
    if (tab === 'menu'     && !window._menuLoaded)      loadMenu();
    if (tab === 'media'    && !window._mediaLoaded)     loadMedia();
    if (tab === 'settings' && !window._settingsLoaded)  loadSettings();
};

/* ═══════════════════════════════════════════
   SID_25 — METRICS
═══════════════════════════════════════════ */
async function loadOverview() {
    window._overviewLoaded = true;
    try {
        const data = await api('GET', '/api/vendor/metrics');
        renderKpis(data);
        renderReviewList(data.recent_reviews ?? [], 'overview-reviews', 2);
    } catch {
        // Fallback demo data so the UI is never empty
        renderKpis({ views: 12400, views_delta: 14, rating: 4.8, rating_delta: 0.2, menu_count: 24, menu_delta: 2, recent_reviews: demoReviews() });
        renderReviewList(demoReviews(), 'overview-reviews', 2);
    }
}

function renderKpis({ views, views_delta, rating, rating_delta, menu_count, menu_delta }) {
    document.getElementById('kpi-views').innerHTML  = views >= 1000 ? (views / 1000).toFixed(1) + 'K' : views;
    document.getElementById('kpi-rating').innerHTML = parseFloat(rating).toFixed(1);
    document.getElementById('kpi-menu').innerHTML   = menu_count;
    document.getElementById('kpi-views-delta').outerHTML  = deltaEl(views_delta, '%');
    document.getElementById('kpi-rating-delta').outerHTML = deltaEl(rating_delta, '', true);
    document.getElementById('kpi-menu-delta').outerHTML   = deltaEl(menu_delta);
}

/* ═══════════════════════════════════════════
   SID_26 — REVIEWS
═══════════════════════════════════════════ */
async function loadReviews() {
    window._reviewsLoaded = true;
    try {
        const data = await api('GET', '/api/vendor/reviews');
        const reviews = data.data ?? data;
        document.getElementById('reviews-count').textContent = reviews.length + ' review' + (reviews.length !== 1 ? 's' : '');
        renderReviewList(reviews, 'all-reviews', 99);
    } catch {
        const demo = demoReviews();
        document.getElementById('reviews-count').textContent = demo.length + ' reviews';
        renderReviewList(demo, 'all-reviews', 99);
    }
}

function renderReviewList(reviews, containerId, limit) {
    const el = document.getElementById(containerId);
    if (!reviews || reviews.length === 0) {
        el.innerHTML = `<div class="vd-empty"><div class="vd-empty-icon">⭐</div><div class="vd-empty-title">No reviews yet</div><div class="vd-empty-sub">When customers leave reviews, they'll appear here.</div></div>`;
        return;
    }
    el.innerHTML = reviews.slice(0, limit).map(r => `
        <div class="vd-review-item" id="review-${r.id}">
            <div class="vd-review-avatar-placeholder">${(r.user_name || r.author || 'U')[0].toUpperCase()}</div>
            <div class="vd-review-body">
                <div class="vd-review-meta">
                    <span class="vd-review-author">${escHtml(r.user_name || r.author || 'Anonymous')}</span>
                    <span class="vd-review-stars">${stars(r.rating)}</span>
                    <span class="vd-review-time">${r.created_at ? timeAgo(r.created_at) : r.time || ''}</span>
                </div>
                <p class="vd-review-text">${escHtml(r.body || r.comment || '')}</p>
                ${r.reply
                    ? `<div class="vd-reply-existing"><div class="vd-reply-existing-label">Your reply</div>${escHtml(r.reply)}</div>`
                    : `<button class="vd-btn vd-btn-ghost" style="font-size:0.75rem;padding:0.3rem 0.7rem;margin-top:0.25rem;" onclick="toggleReplyArea(${r.id})">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
                            Reply
                       </button>
                       <div class="vd-review-reply-area" id="reply-area-${r.id}">
                           <textarea class="vd-reply-input" id="reply-input-${r.id}" placeholder="Write a reply…" rows="2"></textarea>
                           <div style="display:flex;flex-direction:column;gap:0.3rem;">
                               <button class="vd-btn vd-btn-primary" style="font-size:0.78rem;padding:0.35rem 0.75rem;white-space:nowrap;" onclick="submitReply(${r.id})">Send</button>
                               <button class="vd-btn vd-btn-ghost" style="font-size:0.78rem;padding:0.35rem 0.75rem;" onclick="toggleReplyArea(${r.id})">Cancel</button>
                           </div>
                       </div>`
                }
            </div>
        </div>
    `).join('');
}

window.toggleReplyArea = function (id) {
    const a = document.getElementById('reply-area-' + id);
    if (a) a.classList.toggle('open');
};

window.submitReply = async function (id) {
    const input = document.getElementById('reply-input-' + id);
    const reply = input?.value.trim();
    if (!reply) { toast('Please write a reply first.', 'error'); return; }
    try {
        await api('POST', `/api/vendor/reviews/${id}/reply`, { reply });
        toast('Reply posted!');
        // Refresh review list
        window._reviewsLoaded = false;
        loadReviews();
    } catch {
        toast('Failed to post reply.', 'error');
    }
};

/* ═══════════════════════════════════════════
   SID_27 — MENU
═══════════════════════════════════════════ */
let menuItems = [];

async function loadMenu() {
    window._menuLoaded = true;
    try {
        const data = await api('GET', '/api/vendor/menu-items');
        menuItems = data.data ?? data;
    } catch {
        menuItems = demoMenuItems();
    }
    renderMenuTable();
}

function renderMenuTable() {
    document.getElementById('menu-count').textContent = menuItems.length + ' item' + (menuItems.length !== 1 ? 's' : '');
    const tbody = document.getElementById('menu-tbody');
    if (menuItems.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5"><div class="vd-empty"><div class="vd-empty-icon">🍽️</div><div class="vd-empty-title">No menu items yet</div><div class="vd-empty-sub">Add your first item to get started.</div></div></td></tr>`;
        return;
    }
    tbody.innerHTML = menuItems.map(item => `
        <tr>
            <td>
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    ${item.image_url
                        ? `<img src="${item.image_url}" alt="${escHtml(item.name)}" class="vd-menu-item-img">`
                        : `<div class="vd-menu-item-img-placeholder">🍴</div>`}
                    <div>
                        <div style="font-weight:600;color:#1a1612;">${escHtml(item.name)}</div>
                        ${item.description ? `<div style="font-size:0.75rem;color:#a8a29e;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escHtml(item.description)}</div>` : ''}
                    </div>
                </div>
            </td>
            <td style="color:#78716c;">${escHtml(item.category || '—')}</td>
            <td style="font-weight:600;">₱${parseFloat(item.price || 0).toFixed(2)}</td>
            <td>
                <span class="vd-badge ${item.status === 'Active' ? 'vd-badge-active' : item.status === 'Sold Out' ? 'vd-badge-soldout' : 'vd-badge-hidden'}">
                    ${escHtml(item.status || 'Active')}
                </span>
            </td>
            <td style="text-align:right;">
                <div style="display:flex;justify-content:flex-end;gap:0.25rem;">
                    <button class="vd-icon-btn" title="Edit" onclick="openMenuModal(${item.id})">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                    </button>
                    <button class="vd-icon-btn danger" title="Delete" onclick="deleteMenuItem(${item.id})">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

window.openMenuModal = function (id) {
    const modal = document.getElementById('menu-modal');
    document.getElementById('menu-photo-preview').style.display = 'none';
    document.getElementById('menu-photo-zone').style.display = '';
    if (id) {
        const item = menuItems.find(i => i.id === id);
        if (!item) return;
        document.getElementById('menu-modal-title').textContent = 'Edit Menu Item';
        document.getElementById('menu-item-id').value  = item.id;
        document.getElementById('menu-name').value     = item.name || '';
        document.getElementById('menu-desc').value     = item.description || '';
        document.getElementById('menu-price').value    = item.price || '';
        document.getElementById('menu-category').value = item.category || 'Mains';
        document.getElementById('menu-status').value   = item.status || 'Active';
        if (item.image_url) {
            const prev = document.getElementById('menu-photo-preview');
            prev.src = item.image_url; prev.style.display = 'block';
        }
    } else {
        document.getElementById('menu-modal-title').textContent = 'Add Menu Item';
        document.getElementById('menu-item-id').value  = '';
        document.getElementById('menu-name').value     = '';
        document.getElementById('menu-desc').value     = '';
        document.getElementById('menu-price').value    = '';
        document.getElementById('menu-category').value = 'Mains';
        document.getElementById('menu-status').value   = 'Active';
    }
    modal.classList.add('open');
};

window.closeMenuModal = function () {
    document.getElementById('menu-modal').classList.remove('open');
};

window.handleMenuPhotoPreview = function (input) {
    const file = input.files[0];
    if (!file) return;
    const prev = document.getElementById('menu-photo-preview');
    prev.src = URL.createObjectURL(file);
    prev.style.display = 'block';
};

window.saveMenuItem = async function () {
    const id    = document.getElementById('menu-item-id').value;
    const name  = document.getElementById('menu-name').value.trim();
    const price = document.getElementById('menu-price').value;
    if (!name) { toast('Item name is required.', 'error'); return; }
    if (!price) { toast('Price is required.', 'error'); return; }

    const payload = {
        name,
        description: document.getElementById('menu-desc').value.trim(),
        price: parseFloat(price),
        category: document.getElementById('menu-category').value,
        status:   document.getElementById('menu-status').value,
    };

    try {
        if (id) {
            await api('PUT', `/api/vendor/menu-items/${id}`, payload);
            toast('Menu item updated!');
        } else {
            await api('POST', '/api/vendor/menu-items', payload);
            toast('Menu item added!');
        }
        closeMenuModal();
        window._menuLoaded = false;
        loadMenu();
    } catch {
        toast('Could not save item.', 'error');
    }
};

window.deleteMenuItem = async function (id) {
    if (!confirm('Delete this menu item?')) return;
    try {
        await api('DELETE', `/api/vendor/menu-items/${id}`);
        toast('Item deleted.');
        window._menuLoaded = false;
        loadMenu();
    } catch {
        toast('Could not delete item.', 'error');
    }
};

/* ═══════════════════════════════════════════
   SID_28 — PHOTOS
═══════════════════════════════════════════ */
let photos = [];

async function loadMedia() {
    window._mediaLoaded = true;
    try {
        const data = await api('GET', '/api/vendor/photos');
        photos = data.data ?? data;
    } catch {
        photos = [];
    }
    renderPhotos();
    loadPromos();
}

function renderPhotos() {
    const grid = document.getElementById('photo-grid');
    const add  = grid.querySelector('.vd-photo-add');
    // remove existing photo cells
    grid.querySelectorAll('.vd-photo-cell').forEach(c => c.remove());

    photos.forEach((p, i) => {
        const cell = document.createElement('div');
        cell.className = 'vd-photo-cell';
        cell.innerHTML = `
            <img src="${p.url}" alt="Photo ${i + 1}">
            ${p.is_primary ? '<span class="vd-photo-primary-badge">Primary</span>' : ''}
            <div class="vd-photo-cell-actions">
                ${!p.is_primary ? `<button class="vd-btn vd-btn-ghost" style="font-size:0.72rem;padding:0.3rem 0.6rem;background:rgba(255,255,255,.9)" onclick="setPrimaryPhoto(${p.id})">Set Primary</button>` : ''}
                <button class="vd-icon-btn danger" style="background:rgba(255,255,255,.9)" onclick="deletePhoto(${p.id})">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </div>
        `;
        grid.insertBefore(cell, add);
    });
}

window.handlePhotoUpload = async function (input) {
    const files = Array.from(input.files);
    const oversized = files.filter(f => f.size > 5 * 1024 * 1024);
    if (oversized.length) { toast(`${oversized.length} file(s) exceed 5 MB limit.`, 'error'); return; }

    for (const file of files) {
        const fd = new FormData();
        fd.append('photo', file);
        fd.append('_token', CSRF);
        try {
            const r = await fetch('/api/vendor/photos', { method: 'POST', body: fd });
            if (!r.ok) throw new Error();
        } catch {
            toast('Upload failed for ' + file.name, 'error');
            return;
        }
    }
    toast(files.length + ' photo' + (files.length !== 1 ? 's' : '') + ' uploaded!');
    window._mediaLoaded = false;
    loadMedia();
};

window.setPrimaryPhoto = async function (id) {
    try {
        await api('PUT', `/api/vendor/photos/${id}/primary`, {});
        toast('Primary photo updated!');
        window._mediaLoaded = false;
        loadMedia();
    } catch { toast('Could not update primary.', 'error'); }
};

window.deletePhoto = async function (id) {
    if (!confirm('Delete this photo?')) return;
    try {
        await api('DELETE', `/api/vendor/photos/${id}`);
        toast('Photo deleted.');
        window._mediaLoaded = false;
        loadMedia();
    } catch { toast('Could not delete photo.', 'error'); }
};

/* ═══════════════════════════════════════════
   SID_29 — PROMOTIONS
═══════════════════════════════════════════ */
let promos = [];

async function loadPromos() {
    try {
        const data = await api('GET', '/api/vendor/promotions');
        promos = data.data ?? data;
    } catch {
        promos = demoPromos();
    }
    renderPromos();
}

function renderPromos() {
    const el = document.getElementById('promo-list');
    if (promos.length === 0) {
        el.innerHTML = `<div class="vd-empty"><div class="vd-empty-icon">🏷️</div><div class="vd-empty-title">No promotions yet</div><div class="vd-empty-sub">Create a promo to attract more customers.</div></div>`;
        return;
    }
    el.innerHTML = promos.map(p => {
        const expired = new Date(p.valid_until) < new Date();
        return `
        <div class="vd-promo-card">
            <div class="vd-promo-badge">${p.discount}%</div>
            <div class="vd-promo-info">
                <div class="vd-promo-title">${escHtml(p.title)}</div>
                <div class="vd-promo-meta">${escHtml(p.description || '')} · Valid until ${new Date(p.valid_until).toLocaleDateString()}</div>
            </div>
            <span class="vd-badge ${expired ? 'vd-badge-soldout' : 'vd-badge-active'}">${expired ? 'Expired' : 'Active'}</span>
            <button class="vd-icon-btn danger" onclick="deletePromo(${p.id})">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </button>
        </div>
        `;
    }).join('');
}

window.openPromoModal  = function () { document.getElementById('promo-modal').classList.add('open'); };
window.closePromoModal = function () { document.getElementById('promo-modal').classList.remove('open'); };

window.savePromo = async function () {
    const title    = document.getElementById('promo-title').value.trim();
    const discount = document.getElementById('promo-discount').value;
    const until    = document.getElementById('promo-valid-until').value;
    if (!title || !discount || !until) { toast('Please fill all fields.', 'error'); return; }
    try {
        await api('POST', '/api/vendor/promotions', {
            title,
            description: document.getElementById('promo-desc').value.trim(),
            discount: parseInt(discount, 10),
            valid_until: until,
        });
        toast('Promotion published!');
        closePromoModal();
        window._mediaLoaded = false;
        loadMedia();
    } catch { toast('Could not save promo.', 'error'); }
};

window.deletePromo = async function (id) {
    if (!confirm('Delete this promotion?')) return;
    try {
        await api('DELETE', `/api/vendor/promotions/${id}`);
        toast('Promotion deleted.');
        window._mediaLoaded = false;
        loadMedia();
    } catch { toast('Could not delete promo.', 'error'); }
};

/* ═══════════════════════════════════════════
   SID_30 — SETTINGS
═══════════════════════════════════════════ */
const DAYS = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

async function loadSettings() {
    window._settingsLoaded = true;
    let profile = {};
    try {
        const data = await api('GET', '/api/vendor/profile');
        profile = data.data ?? data;
    } catch {}

    document.getElementById('s-biz-name').value = profile.business_name || '';
    document.getElementById('s-location').value  = profile.location || '';
    document.getElementById('s-phone').value     = profile.phone || '';
    document.getElementById('s-desc').value      = profile.description || '';
    if (profile.category) document.getElementById('s-category').value = profile.category;
    if (profile.price_range) document.getElementById('s-price').value = profile.price_range;

    // Operating hours
    const hours = profile.hours || {};
    const grid  = document.getElementById('hours-grid');
    grid.innerHTML = DAYS.map(day => {
        const h    = hours[day.toLowerCase()] || {};
        const closed = h.closed ?? true;
        return `
        <div class="vd-hours-grid">
            <span class="vd-hours-day">${day}</span>
            <input type="time" id="hours-${day}-open"  value="${h.open || '09:00'}"  ${closed ? 'disabled' : ''} style="border:1.5px solid #e5e7eb;border-radius:8px;padding:0.45rem 0.6rem;font-family:'DM Sans',sans-serif;font-size:0.85rem;outline:none;">
            <input type="time" id="hours-${day}-close" value="${h.close || '22:00'}" ${closed ? 'disabled' : ''} style="border:1.5px solid #e5e7eb;border-radius:8px;padding:0.45rem 0.6rem;font-family:'DM Sans',sans-serif;font-size:0.85rem;outline:none;">
            <label style="display:flex;align-items:center;gap:4px;font-size:0.78rem;color:#78716c;cursor:pointer;">
                <input type="checkbox" id="hours-${day}-closed" ${closed ? 'checked' : ''} style="accent-color:var(--orange);" onchange="toggleHoursClosed('${day}', this.checked)"> Closed
            </label>
        </div>
        `;
    }).join('');
}

window.toggleHoursClosed = function (day, closed) {
    document.getElementById('hours-' + day + '-open').disabled  = closed;
    document.getElementById('hours-' + day + '-close').disabled = closed;
};

window.saveSettings = async function () {
    const hours = {};
    DAYS.forEach(day => {
        const closed = document.getElementById('hours-' + day + '-closed')?.checked;
        hours[day.toLowerCase()] = {
            closed,
            open:  document.getElementById('hours-' + day + '-open')?.value,
            close: document.getElementById('hours-' + day + '-close')?.value,
        };
    });

    const payload = {
        business_name: document.getElementById('s-biz-name').value.trim(),
        location:      document.getElementById('s-location').value.trim(),
        phone:         document.getElementById('s-phone').value.trim(),
        description:   document.getElementById('s-desc').value.trim(),
        category:      document.getElementById('s-category').value,
        price_range:   document.getElementById('s-price').value,
        hours,
    };

    try {
        await api('PUT', '/api/vendor/profile', payload);
        toast('Settings saved!');
    } catch {
        toast('Could not save settings.', 'error');
    }
};

/* ═══════════════════════════════════════════
   DEMO DATA (fallback when API not ready)
═══════════════════════════════════════════ */
function demoReviews() {
    return [
        { id: 1, user_name: 'Maria Santos',  rating: 5, body: 'Amazing food and great service! The spicy noodles are absolutely fantastic.', created_at: new Date(Date.now() - 7200000).toISOString() },
        { id: 2, user_name: 'Juan dela Cruz', rating: 4, body: 'Really good place. The portions are generous and the staff is very friendly.', created_at: new Date(Date.now() - 86400000).toISOString() },
        { id: 3, user_name: 'Ana Reyes',      rating: 5, body: 'Best restaurant in Cebu! I always recommend this place to my friends.', created_at: new Date(Date.now() - 172800000).toISOString() },
    ];
}

function demoMenuItems() {
    return [
        { id: 1, name: 'Signature Dish',  category: 'Mains',    price: 185, status: 'Active',   description: 'Our signature house specialty.' },
        { id: 2, name: 'Spicy Noodles',   category: 'Mains',    price: 140, status: 'Active',   description: 'Classic spicy broth noodle bowl.' },
        { id: 3, name: 'Classic Burger',  category: 'Mains',    price: 160, status: 'Sold Out', description: 'Juicy beef patty with fresh greens.' },
        { id: 4, name: 'Fresh Salad',     category: 'Starters', price: 120, status: 'Active',   description: 'Garden fresh daily salad.' },
    ];
}

function demoPromos() {
    return [
        { id: 1, title: 'Weekend Special', description: '20% off all mains', discount: 20, valid_until: new Date(Date.now() + 7 * 86400000).toISOString() },
        { id: 2, title: 'Lunch Deal',      description: 'Free drink with any main', discount: 10, valid_until: new Date(Date.now() - 86400000).toISOString() },
    ];
}

/* ═══════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════ */
function escHtml(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Close modals on backdrop click
['menu-modal','promo-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('open');
        }
    });
});

/* ── INIT ── */
loadOverview();

}());