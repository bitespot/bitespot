// public/js/explore.js — SID_08 (search+render), SID_09 (URL state), SID_10 (filters), SID_11 (list view)
// Requires: api.js (apiFetch), Leaflet

'use strict';

// ── Category icons (Lucide-style SVG, white stroke) ───────────────────────
const CAT_ICONS = {
    restaurants:  `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3m0 0v7"/></svg>`,
    cafes:         `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>`,
    'street-food': `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4H6z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>`,
    desserts:      `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-8a2 2 0 00-2-2H6a2 2 0 00-2 2v8"/><path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2 1 2 1"/><path d="M2 21h20"/><path d="M7 8v3M12 8v3M17 8v3"/></svg>`,
    drinks:        `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>`,
};

const DEFAULT_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3m0 0v7"/></svg>`;

function catIcon(slug) { return CAT_ICONS[slug] ?? DEFAULT_ICON; }

// ── Marker factory ────────────────────────────────────────────────────────
function makePin(catSlug, active = false) {
    return L.divIcon({
        className: '',
        html: `<div class="bs-pin${active ? ' active' : ''}">${catIcon(catSlug)}</div>`,
        iconSize:    [38, 38],
        iconAnchor:  [19, 19],
        popupAnchor: [0, -23],
    });
}

// ── Popup HTML ────────────────────────────────────────────────────────────
function buildPopup(v) {
    const img = v.image_url
        ? `<img src="${v.image_url}" alt="${v.name}" class="bsp-img">`
        : `<div class="bsp-noimg">🍽️</div>`;
    const stars = '★'.repeat(Math.round(v.rating || 0)) + '☆'.repeat(5 - Math.round(v.rating || 0));
    const meta  = [v.category, v.price_tier, v.city].filter(Boolean).join(' · ');
    return `<div>${img}
        <div class="bsp-body">
            <div class="bsp-name">${v.name}</div>
            ${meta ? `<div class="bsp-meta">${meta}</div>` : ''}
            <div class="bsp-stars"><span>${stars}</span><span style="color:#374151;margin-left:2px">${v.rating ? Number(v.rating).toFixed(1) : 'New'}</span></div>
            <a href="/place/${v.slug}" class="bsp-link">View place →</a>
        </div></div>`;
}

// ── S3 Image URL helper ──────────────────────────────────────────────────────
function getS3ImageUrl(photoPath) {
    if (!photoPath) return null;
    // If already a full URL, return as-is
    if (photoPath.startsWith('http')) return photoPath;
    // Build S3 URL - Laravel Storage::disk('s3')->url() returns this format
    const bucket = window.S3_BUCKET || 'bitespot';
    const region = window.S3_REGION || 'ap-southeast-2';
    return `https://${bucket}.s3.${region}.amazonaws.com/${photoPath}`;
}

// ── Normalize: handles both blade-JSON format and API response format ──────
function normalize(v) {
    return {
        id:         v.id,
        slug:       v.slug ?? String(v.id),
        name:       v.business_name ?? v.name ?? '',
        category:   v.category?.name ?? (typeof v.category === 'string' ? v.category : '') ?? '',
        cat_slug:   v.category?.slug ?? v.category_slug ?? '',
        city:       v.city ?? '',
        price_tier: v.price_tier ?? '',
        rating:     v.avg_rating != null ? parseFloat(v.avg_rating) : (v.rating != null ? parseFloat(v.rating) : null),
        image_url:  getS3ImageUrl(v.cover_photo) ?? getS3ImageUrl(v.profile_photo) ?? v.primary_photo ?? v.image_url ?? '',
        lat:        v.lat != null ? parseFloat(v.lat) : null,
        lng:        v.lng != null ? parseFloat(v.lng) : null,
    };
}

// ── Map singleton ─────────────────────────────────────────────────────────
window.exploreMap = {
    _map:      null,
    _markers:  {},
    _activeId: null,

    init() {
        if (this._map) return;

        // Request user location first, then create map
        this._requestUserLocationAndInitMap();
    },

    _requestUserLocationAndInitMap() {
        const fallbackCenter = [12.8797, 121.7740]; // Tacloban
        const createMapWithCenter = (center) => {
            this._map = L.map('explore-map', { center, zoom: 15 });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '© OpenStreetMap contributors © CARTO',
            }).addTo(this._map);

            if (window.INITIAL_MAP_SPOTS?.length) {
                this._placeMarkers(window.INITIAL_MAP_SPOTS.map(normalize));
                this._fitBounds(window.INITIAL_MAP_SPOTS.map(normalize));
            }

            this._placeUserLocation();
        };

        if (!navigator.geolocation) {
            createMapWithCenter(fallbackCenter);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                const userCenter = [pos.coords.latitude, pos.coords.longitude];
                createMapWithCenter(userCenter);
            },
            err => {
                console.debug('[explore] Geolocation unavailable:', err.code);
                createMapWithCenter(fallbackCenter);
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    },

    _placeUserLocation() {
        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                
                const userIcon = L.divIcon({
                    className: '',
                    html: `<div style="width:32px;height:32px;border-radius:50%;background:red;border:3px solid #fff;box-shadow:0 2px 8px rgba(240, 124, 78, 0.4);display:flex;align-items:center;justify-content:center;"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg></div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                });
                
                L.marker([lat, lng], { icon: userIcon })
                    .addTo(this._map)
                    .bindTooltip('You are here');
            },
            err => {
                console.debug('[explore] User marker placement failed:', err.code);
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    },

    setVendors(rawVendors) {
        Object.values(this._markers).forEach(({ marker }) => marker.remove());
        this._markers = {};
        this._activeId = null;

        const vendors = rawVendors.map(normalize).filter(v => v.lat != null && v.lng != null);
        this._placeMarkers(vendors);
        this._fitBounds(vendors);
    },

    _placeMarkers(vendors) {
        vendors.forEach(v => {
            const marker = L.marker([v.lat, v.lng], { icon: makePin(v.cat_slug) })
                .addTo(this._map)
                .bindPopup(buildPopup(v), { maxWidth: 250, minWidth: 224 });

            marker.on('click', () => {
                this._activate(v.id);
                const card = document.querySelector(`[data-vendor-id="${v.id}"]`);
                card?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });

            this._markers[v.id] = { marker, vendor: v };
        });
    },

    _fitBounds(vendors) {
        const pts = vendors.filter(v => v.lat && v.lng).map(v => [v.lat, v.lng]);
        if (pts.length > 1) {
            this._map.fitBounds(L.latLngBounds(pts), { padding: [50, 50], maxZoom: 14 });
        } else if (pts.length === 1) {
            this._map.setView(pts[0], 15);
        }
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

// ── View toggle (global — called from inline onclick in blade) ─────────────
function setExploreView(view) {
    const sidebar  = document.getElementById('sidebar-pane');
    const mapPane  = document.getElementById('map-pane');
    const listPane = document.getElementById('list-pane');

    if (view === 'list') {
        sidebar?.classList.add('hidden');
        mapPane?.classList.add('hidden');
        listPane?.classList.remove('hidden');
        renderGrid(window._exploreVendors ?? window.INITIAL_VENDORS ?? []);
    } else {
        sidebar?.classList.remove('hidden');
        mapPane?.classList.remove('hidden');
        listPane?.classList.add('hidden');
        // Give the browser one frame to restore layout before invalidating
        requestAnimationFrame(() => window.exploreMap?.invalidateSize());
    }

    document.querySelectorAll('[data-view-btn]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.viewBtn === view);
    });
}

// ── Card renderers ────────────────────────────────────────────────────────
function renderListCard(v) {
    const r     = normalize(v);
    const photo = r.image_url
        ? `<img src="${r.image_url}" alt="${r.name}" class="bs-card__thumb">`
        : `<div class="bs-card__noimg">🍽️</div>`;
    const sub = [r.category, r.price_tier].filter(Boolean).join(' • ');
    return `<a href="/place/${r.slug}" class="bs-card" data-vendor-id="${r.id}">
        ${photo}
        <div class="bs-card__body">
            <div class="bs-card__name">${r.name}</div>
            ${sub  ? `<div class="bs-card__sub">${sub}</div>`   : ''}
            ${r.city ? `<div class="bs-card__city">${r.city}</div>` : ''}
            <div class="bs-card__stars">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                ${r.rating != null ? r.rating.toFixed(1) : 'New'}
            </div>
        </div>
    </a>`;
}

function renderGridCard(v) {
    const r     = normalize(v);
    const photo = r.image_url
        ? `<img src="${r.image_url}" alt="${r.name}" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">`
        : `<div class="w-full h-44 bg-gradient-to-br from-orange-400 to-orange-300 flex items-center justify-center text-white text-4xl">🍽️</div>`;
    return `<a href="/place/${r.slug}" data-vendor-id="${r.id}"
              class="bg-white rounded-xl shadow hover:shadow-md transition-shadow flex flex-col overflow-hidden group">
        ${photo}
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-bold text-base text-gray-900 mb-0.5 leading-snug">${r.name}</h3>
            <p class="text-gray-500 text-sm mb-3">${[r.category, r.city].filter(Boolean).join(' • ')}</p>
            <div class="flex items-center text-sm text-amber-500 font-semibold mt-auto">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" class="mr-1">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                ${r.rating != null ? r.rating.toFixed(1) : 'New'}
                <span class="ml-auto text-gray-400 font-normal text-xs">${r.price_tier}</span>
            </div>
        </div>
    </a>`;
}

function renderEmptyList(msg = 'No places found') {
    return `<div class="flex flex-col items-center justify-center py-16 px-6 text-center text-gray-400">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 opacity-40">
            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
        </svg>
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
    const el = document.getElementById('result-count');
    if (el) el.textContent = `${n} ${n === 1 ? 'place' : 'places'} found`;
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

// ── Fetch vendors and update both list and map ─────────────────────────────
function fetchVendors() {
    const list = document.getElementById('explore-list');
    pushState();

    const p = new URLSearchParams();
    if (filters.q)        p.set('q',        filters.q);
    if (filters.category) p.set('category', filters.category);
    if (filters.price)    p.set('price',    filters.price);
    if (filters.rating)   p.set('rating',   filters.rating);

    apiFetch(`/api/vendors${p.toString() ? '?' + p : ''}`)
        .then(json => {
            const vendors = json.data ?? [];
            window._exploreVendors = vendors;

            if (list) {
                list.innerHTML = vendors.length
                    ? vendors.map(renderListCard).join('')
                    : renderEmptyList();
            }
            setResultCount(vendors.length);

            window.exploreMap.setVendors(vendors);

            const listPane = document.getElementById('list-pane');
            if (!listPane?.classList.contains('hidden')) renderGrid(vendors);
        })
        .catch(err => {
            console.error('[explore] fetch error:', err);
            if (list) list.innerHTML = renderEmptyList('Could not load results.');
        });
}

function debounce(fn, ms) {
    let t;
    return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
}

const debouncedFetch = debounce(fetchVendors, 380);

// ── Category pills ────────────────────────────────────────────────────────
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
    fetchVendors();
}

// ── Init ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Size the root to exactly fill the viewport below the sticky navbar
    const navH = document.querySelector('.bs-navbar')?.offsetHeight ?? 0;
    const root  = document.getElementById('explore-root');
    if (root) root.style.height = `calc(100vh - ${navH}px)`;

    window.exploreMap.init();
    setResultCount(window.INITIAL_VENDORS?.length ?? 0);

    readURLParams();
    loadCategories();

    // Sync active category pill from URL params
    if (filters.category) {
        document.querySelector(`.cat-pill[data-category="${filters.category}"]`)?.classList.add('active');
        document.querySelector('.cat-pill[data-category=""]')?.classList.remove('active');
    }

    // Search
    document.getElementById('explore-search-input')
        ?.addEventListener('input', e => { filters.q = e.target.value.trim(); debouncedFetch(); });
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
        fetchVendors();
    });

    document.getElementById('filter-clear')?.addEventListener('click', () => {
        filters.price  = '';
        filters.rating = '';
        document.querySelectorAll('[data-filter="price"]').forEach(b => b.classList.remove('is-active'));
        const ratingEl = document.getElementById('filter-rating');
        if (ratingEl) ratingEl.value = '';
        document.getElementById('explore-filter-panel')?.classList.add('hidden');
        fetchVendors();
    });

    // Sidebar hover → map highlight (event delegation)
    const list = document.getElementById('explore-list');
    list?.addEventListener('mouseover', e => {
        const card = e.target.closest('[data-vendor-id]');
        if (card) window.exploreMap?.highlight(Number(card.dataset.vendorId));
    });
    list?.addEventListener('mouseout', e => {
        const card = e.target.closest('[data-vendor-id]');
        if (card) window.exploreMap?.highlight(null);
    });

    // Re-fetch if URL has filter params
    if (filters.q || filters.category || filters.price || filters.rating) {
        fetchVendors();
    } else {
        window._exploreVendors = window.INITIAL_VENDORS ?? [];
    }
});
