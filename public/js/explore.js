// public/js/explore.js — SID_08 (search+render), SID_09 (URL state), SID_10 (filters), SID_11 (list view)
// Requires: api.js (apiFetch), Leaflet

'use strict';

// ── Category icons ─────────────────────────────────────────────────────────
const CAT_ICONS = {
    restaurants:  `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3m0 0v7"/></svg>`,
    cafes:         `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>`,
    'street-food': `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4H6z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>`,
    desserts:      `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-8a2 2 0 00-2-2H6a2 2 0 00-2 2v8"/><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2 1 2 1"/><path d="M2 21h20"/><path d="M7 8v3M12 8v3M17 8v3"/></svg>`,
    drinks:        `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>`,
};
const DEFAULT_ICON = CAT_ICONS.restaurants;
function catIcon(slug) { return CAT_ICONS[slug] ?? DEFAULT_ICON; }

const PRICE_LABEL = { '$': '₱', '$$': '₱₱', '$$$': '₱₱₱' };

// ── Normalize — run ONCE per vendor on page load ───────────────────────────
function normalize(v) {
    const raw = v.price_tier ?? '';
    return {
        id:              v.id,
        slug:            v.slug ?? String(v.id),
        name:            v.business_name ?? v.name ?? '',
        category:        v.category?.name ?? (typeof v.category === 'string' ? v.category : '') ?? '',
        cat_slug:        v.category?.slug ?? v.category_slug ?? v.cat_slug ?? '',
        city:            v.city ?? '',
        price_tier:      v.price_tier_label ?? PRICE_LABEL[raw] ?? raw,
        price_tier_raw:  raw,
        rating:          v.avg_rating != null ? parseFloat(v.avg_rating)
                       : v.rating    != null ? parseFloat(v.rating) : null,
        image_url:       v.image_url ?? v.primary_photo ?? null,
        lat:             v.lat != null ? parseFloat(v.lat) : null,
        lng:             v.lng != null ? parseFloat(v.lng) : null,
    };
}

// Normalize the full dataset once — all subsequent operations use this
const ALL_VENDORS = (window.INITIAL_VENDORS ?? []).map(normalize);

// ── Marker factory ────────────────────────────────────────────────────────
function makePin(catSlug, active = false) {
    return L.divIcon({
        className: '',
        html: `<div class="bs-pin${active ? ' active' : ''}">${catIcon(catSlug)}</div>`,
        iconSize: [38, 38], iconAnchor: [19, 19], popupAnchor: [0, -23],
    });
}

// ── Popup HTML ────────────────────────────────────────────────────────────
function buildPopup(v) {
    const img   = v.image_url
        ? `<img src="${v.image_url}" alt="${v.name}" class="bsp-img" loading="lazy">`
        : `<div class="bsp-noimg">🍽️</div>`;
    const stars = '★'.repeat(Math.round(v.rating || 0)) + '☆'.repeat(5 - Math.round(v.rating || 0));
    const meta  = [v.category, v.price_tier, v.city].filter(Boolean).join(' · ');
    return `<div>${img}<div class="bsp-body">
        <div class="bsp-name">${v.name}</div>
        ${meta ? `<div class="bsp-meta">${meta}</div>` : ''}
        <div class="bsp-stars"><span>${stars}</span><span style="color:#374151;margin-left:2px">${v.rating ? Number(v.rating).toFixed(1) : 'New'}</span></div>
        <a href="/place/${v.slug}" class="bsp-link">View place →</a>
    </div></div>`;
}

// ── Map singleton ─────────────────────────────────────────────────────────
window.exploreMap = {
    _map:      null,
    _markers:  {},
    _activeId: null,

    init() {
        if (this._map) return;
        const TACLOBAN = [11.2543, 125.0000];

        const boot = (center, userCoords = null) => {
            this._map = L.map('explore-map', { center, zoom: 15 });
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '© OpenStreetMap contributors © CARTO',
            }).addTo(this._map);

            // Place user marker using the position we already have
            if (userCoords) {
                L.marker(userCoords, {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="width:32px;height:32px;border-radius:50%;background:#ef4444;border:3px solid #fff;box-shadow:0 2px 8px rgba(239,68,68,.4);display:flex;align-items:center;justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="white"><circle cx="12" cy="12" r="10"/></svg></div>`,
                        iconSize: [32, 32], iconAnchor: [16, 16],
                    }),
                }).addTo(this._map).bindTooltip('You are here');
            }

            const withCoords = ALL_VENDORS.filter(v => v.lat != null && v.lng != null);
            this._placeMarkers(withCoords);
            this._fitBounds(withCoords);
        };

        if (!navigator.geolocation) { boot(TACLOBAN); return; }

        navigator.geolocation.getCurrentPosition(
            pos  => boot([pos.coords.latitude, pos.coords.longitude], [pos.coords.latitude, pos.coords.longitude]),
            _err => boot(TACLOBAN),
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 }
        );
    },

    // Accepts pre-normalized vendors
    setVendors(vendors) {
        Object.values(this._markers).forEach(({ marker }) => marker.remove());
        this._markers  = {};
        this._activeId = null;
        const withCoords = vendors.filter(v => v.lat != null && v.lng != null);
        this._placeMarkers(withCoords);
        this._fitBounds(withCoords);
    },

    _placeMarkers(vendors) {
        vendors.forEach(v => {
            const marker = L.marker([v.lat, v.lng], { icon: makePin(v.cat_slug) })
                .addTo(this._map)
                .bindPopup(buildPopup(v), { maxWidth: 250, minWidth: 224 });

            marker.on('click', () => {
                this._activate(v.id);
                document.querySelector(`[data-vendor-id="${v.id}"]`)
                    ?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });

            this._markers[v.id] = { marker, vendor: v };
        });
    },

    _fitBounds(vendors) {
        const pts = vendors.filter(v => v.lat && v.lng).map(v => [v.lat, v.lng]);
        if (pts.length > 1)      this._map.fitBounds(L.latLngBounds(pts), { padding: [50, 50], maxZoom: 14 });
        else if (pts.length === 1) this._map.setView(pts[0], 15);
    },

    _activate(id) {
        if (this._activeId != null) {
            const prev = this._markers[this._activeId];
            if (prev) prev.marker.setIcon(makePin(prev.vendor.cat_slug, false));
            document.querySelector(`[data-vendor-id="${this._activeId}"]`)?.classList.remove('active');
        }
        this._activeId = id;
        if (id != null && this._markers[id]) {
            this._markers[id].marker.setIcon(makePin(this._markers[id].vendor.cat_slug, true));
            document.querySelector(`[data-vendor-id="${id}"]`)?.classList.add('active');
        }
    },

    highlight(id) { this._activate(id); },

    panTo(id) {
        const entry = this._markers[id];
        if (!entry) return;
        this._map.panTo(entry.marker.getLatLng(), { animate: true });
        entry.marker.openPopup();
        this._activate(id);
    },

    invalidateSize() { this._map?.invalidateSize(); },
};

// ── View toggle ───────────────────────────────────────────────────────────
function isMobile() { return window.innerWidth < 768; }

function setExploreView(view) {
    const sidebar  = document.getElementById('sidebar-pane');
    const mapPane  = document.getElementById('map-pane');
    const listPane = document.getElementById('list-pane');

    if (view === 'list') {
        // Sidebar is hidden on mobile via CSS; on desktop we hide it in list view
        sidebar?.classList.add('hidden');
        mapPane?.classList.add('hidden');
        listPane?.classList.remove('hidden');
        renderGrid(window._exploreVendors ?? ALL_VENDORS);
    } else {
        // Map view: on desktop show sidebar; CSS hides it on mobile automatically
        if (!isMobile()) sidebar?.classList.remove('hidden');
        mapPane?.classList.remove('hidden');
        listPane?.classList.add('hidden');
        requestAnimationFrame(() => window.exploreMap?.invalidateSize());
    }

    document.querySelectorAll('[data-view-btn]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.viewBtn === view);
    });
}

// ── Card renderers (accept pre-normalized vendors) ─────────────────────────
function renderListCard(v) {
    const photo = v.image_url
        ? `<img src="${v.image_url}" alt="${v.name}" class="bs-card__thumb" loading="lazy">`
        : `<div class="bs-card__noimg">🍽️</div>`;
    const sub = [v.category, v.price_tier].filter(Boolean).join(' • ');
    return `<a href="/place/${v.slug}" class="bs-card" data-vendor-id="${v.id}">
        ${photo}
        <div class="bs-card__body">
            <div class="bs-card__name">${v.name}</div>
            ${sub   ? `<div class="bs-card__sub">${sub}</div>`    : ''}
            ${v.city ? `<div class="bs-card__city">${v.city}</div>` : ''}
            <div class="bs-card__stars">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                ${v.rating != null ? v.rating.toFixed(1) : 'New'}
            </div>
        </div>
    </a>`;
}

function renderGridCard(v) {
    const photo = v.image_url
        ? `<img src="${v.image_url}" alt="${v.name}" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">`
        : `<div class="w-full h-44 bg-gradient-to-br from-orange-400 to-orange-300 flex items-center justify-center text-white text-4xl">🍽️</div>`;
    return `<a href="/place/${v.slug}" data-vendor-id="${v.id}"
              class="bg-white rounded-xl shadow hover:shadow-md transition-shadow flex flex-col overflow-hidden group">
        ${photo}
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-bold text-base text-gray-900 mb-0.5 leading-snug">${v.name}</h3>
            <p class="text-gray-500 text-sm mb-3">${[v.category, v.city].filter(Boolean).join(' • ')}</p>
            <div class="flex items-center text-sm text-amber-500 font-semibold mt-auto">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" class="mr-1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                ${v.rating != null ? v.rating.toFixed(1) : 'New'}
                <span class="ml-auto text-gray-400 font-normal text-xs">${v.price_tier}</span>
            </div>
        </div>
    </a>`;
}

function renderEmptyList(msg = 'No places found') {
    return `<div class="flex flex-col items-center justify-center py-16 px-6 text-center text-gray-400">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 opacity-40"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/></svg>
        <p class="font-medium text-sm">${msg}</p>
        <p class="text-xs mt-1">Try different search terms or filters.</p>
    </div>`;
}

function renderGrid(vendors) {
    const grid = document.getElementById('explore-grid');
    if (!grid) return;
    grid.innerHTML = vendors.length
        ? vendors.map(renderGridCard).join('')
        : `<div class="col-span-full py-16 text-center text-gray-400"><p class="font-medium">No places found.</p></div>`;
}

function setResultCount(n) {
    const label = `${n} ${n === 1 ? 'place' : 'places'} found`;
    const el     = document.getElementById('result-count');
    const elList = document.getElementById('result-count-list');
    if (el)     el.textContent     = label;
    if (elList) elList.textContent = label;
}

function renderResults(vendors) {
    window._exploreVendors = vendors;
    const list = document.getElementById('explore-list');
    if (list) list.innerHTML = vendors.length ? vendors.map(renderListCard).join('') : renderEmptyList();
    setResultCount(vendors.length);
    window.exploreMap.setVendors(vendors);
    const listPane = document.getElementById('list-pane');
    if (!listPane?.classList.contains('hidden')) renderGrid(vendors);
}

// ── Filter state ──────────────────────────────────────────────────────────
const filters = { q: '', category: '', price: '', rating: '' };

function readURLParams() {
    const p = new URLSearchParams(window.location.search);
    filters.q        = p.get('q')        ?? '';
    filters.category = p.get('category') ?? '';
    filters.price    = p.get('price')    ?? '';
    filters.rating   = p.get('rating')   ?? '';
    const inp = document.getElementById('explore-search-input');
    if (inp && filters.q) inp.value = filters.q;
}

function pushState() {
    const p = new URLSearchParams();
    if (filters.q)        p.set('q',        filters.q);
    if (filters.category) p.set('category', filters.category);
    if (filters.price)    p.set('price',    filters.price);
    if (filters.rating)   p.set('rating',   filters.rating);
    history.pushState(null, '', p.toString() ? `/explore?${p}` : '/explore');
}

// ── Client-side filter — instant, no network ───────────────────────────────
function filterLocally() {
    const q      = filters.q.toLowerCase().trim();
    const minRating = filters.rating ? parseFloat(filters.rating) : null;

    const results = ALL_VENDORS.filter(v => {
        if (q && !v.name.toLowerCase().includes(q) && !v.category.toLowerCase().includes(q)) return false;
        if (filters.category && v.cat_slug !== filters.category) return false;
        if (filters.price    && v.price_tier_raw !== filters.price) return false;
        if (minRating        && (v.rating == null || v.rating < minRating)) return false;
        return true;
    });

    pushState();
    renderResults(results);
}

function debounce(fn, ms) {
    let t;
    return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

// Short debounce — only needed to avoid re-rendering on every keystroke
const debouncedFilter = debounce(filterLocally, 120);

// ── Category pills — derived from loaded data, no extra API call ───────────
function loadCategories() {
    apiFetch('/api/categories')
        .then(json => {
            document.getElementById('cat-loading')?.remove();
            const row = document.getElementById('cat-pills-row');
            if (!row) return;
            (json.data ?? []).forEach(c => {
                const btn = document.createElement('button');
                btn.className = 'cat-pill' + (filters.category === c.slug ? ' active' : '');
                btn.dataset.category = c.slug;
                btn.textContent = c.name;
                btn.addEventListener('click', () => activateCategory(c.slug));
                row.appendChild(btn);
            });
        })
        .catch(() => document.getElementById('cat-loading')?.remove());
}

function activateCategory(slug) {
    filters.category = slug;
    document.querySelectorAll('#cat-pills-row .cat-pill[data-category]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.category === slug);
    });
    filterLocally();
}

// ── Responsive root height ────────────────────────────────────────────────
// Uses window.innerHeight instead of 100vh — more reliable on mobile browsers
// where the address bar appearing/hiding shifts the viewport.
function fitRootHeight() {
    const navH = document.querySelector('.bs-navbar')?.offsetHeight ?? 0;
    const root = document.getElementById('explore-root');
    if (root) root.style.height = `${window.innerHeight - navH}px`;
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fitRootHeight();

    // Recalculate on resize and orientation change
    window.addEventListener('resize', debounce(() => {
        fitRootHeight();
        window.exploreMap?.invalidateSize();
        // Re-evaluate sidebar visibility when crossing the md breakpoint
        const listVisible = !document.getElementById('list-pane')?.classList.contains('hidden');
        if (!listVisible && !isMobile()) {
            document.getElementById('sidebar-pane')?.classList.remove('hidden');
        }
    }, 150));

    window.exploreMap.init();
    readURLParams();
    loadCategories();

    setResultCount(ALL_VENDORS.length);
    window._exploreVendors = ALL_VENDORS;

    if (filters.category) {
        document.querySelector('.cat-pill[data-category=""]')?.classList.remove('active');
    }

    // Search
    document.getElementById('explore-search-input')
        ?.addEventListener('input', e => { filters.q = e.target.value.trim(); debouncedFilter(); });
    document.querySelector('[data-action="explore-search"]')
        ?.addEventListener('submit', e => e.preventDefault());

    // "All" pill
    document.querySelector('.cat-pill[data-category=""]')
        ?.addEventListener('click', () => activateCategory(''));

    // Filters toggle
    document.querySelector('[data-action="explore-filters-toggle"]')?.addEventListener('click', () => {
        document.getElementById('explore-filter-panel')?.classList.toggle('hidden');
    });
    document.addEventListener('click', e => {
        const panel = document.getElementById('explore-filter-panel');
        if (!panel?.classList.contains('hidden') &&
            !panel?.contains(e.target) &&
            !e.target.closest('[data-action="explore-filters-toggle"]')) {
            panel.classList.add('hidden');
        }
    });

    // Price tier buttons
    document.querySelectorAll('[data-filter="price"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const was = btn.classList.contains('is-active');
            document.querySelectorAll('[data-filter="price"]').forEach(b => b.classList.remove('is-active'));
            if (!was) btn.classList.add('is-active');
        });
    });

    document.getElementById('filter-apply')?.addEventListener('click', () => {
        filters.price  = document.querySelector('[data-filter="price"].is-active')?.dataset.value ?? '';
        filters.rating = document.getElementById('filter-rating')?.value ?? '';
        document.getElementById('explore-filter-panel')?.classList.add('hidden');
        filterLocally();
    });

    document.getElementById('filter-clear')?.addEventListener('click', () => {
        filters.price  = '';
        filters.rating = '';
        document.querySelectorAll('[data-filter="price"]').forEach(b => b.classList.remove('is-active'));
        const ratingEl = document.getElementById('filter-rating');
        if (ratingEl) ratingEl.value = '';
        document.getElementById('explore-filter-panel')?.classList.add('hidden');
        filterLocally();
    });

    // Sidebar hover → map highlight
    const list = document.getElementById('explore-list');
    list?.addEventListener('mouseover', e => {
        const card = e.target.closest('[data-vendor-id]');
        if (card) window.exploreMap?.highlight(Number(card.dataset.vendorId));
    });
    list?.addEventListener('mouseout', e => {
        const card = e.target.closest('[data-vendor-id]');
        if (card) window.exploreMap?.highlight(null);
    });

    // Apply URL filters on load if present
    if (filters.q || filters.category || filters.price || filters.rating) {
        filterLocally();
    }
});
