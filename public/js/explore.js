// public/js/explore.js — SID_08 (search+render), SID_09 (URL state), SID_10 (filters), SID_11 (grid tab)
// Depends on: api.js (apiFetch), ui.js (renderSkeleton, showToast)

function debounce(fn, delay) {
    let timer;
    return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
}

// ---------------------------------------------------------------------------
// State
// ---------------------------------------------------------------------------

const filters = { q: '', category: '', price: '', rating: '' };

const searchInput  = document.getElementById('explore-search-input');
const filterPanel  = document.getElementById('explore-filter-panel');
const resultsGrid  = document.getElementById('explore-results');

// ---------------------------------------------------------------------------
// SID_09: URL state
// ---------------------------------------------------------------------------

function readURLParams() {
    const p = new URLSearchParams(window.location.search);
    filters.q        = p.get('q')        ?? '';
    filters.category = p.get('category') ?? '';
    filters.price    = p.get('price')    ?? '';
    filters.rating   = p.get('rating')   ?? '';

    if (searchInput && filters.q) searchInput.value = filters.q;
}

function pushState() {
    const p = new URLSearchParams();
    if (filters.q)        p.set('q',        filters.q);
    if (filters.category) p.set('category', filters.category);
    if (filters.price)    p.set('price',    filters.price);
    if (filters.rating)   p.set('rating',   filters.rating);
    const qs = p.toString();
    history.pushState(null, '', qs ? `/explore?${qs}` : '/explore');
}

// ---------------------------------------------------------------------------
// SID_08: Render
// ---------------------------------------------------------------------------

function renderVendorCard(v) {
    const rating = v.avg_rating ? Number(v.avg_rating).toFixed(1) : 'New';
    const photo  = v.primary_photo
        ? `<img src="${v.primary_photo}" alt="${v.business_name}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">`
        : `<div class="w-full h-40 bg-gradient-to-br from-orange-400 to-orange-300 flex items-center justify-center text-white text-3xl">🍽️</div>`;

    return `
        <a href="/place/${v.slug ?? v.id}"
           class="bg-white rounded-xl shadow hover:shadow-md transition-shadow duration-200 flex flex-col overflow-hidden group">
            ${photo}
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-lg mb-1 text-gray-900">${v.business_name}</h3>
                <p class="text-gray-500 text-sm mb-3">${v.category?.name ?? ''} • ${v.city ?? ''}</p>
                <div class="flex items-center text-sm text-amber-500 font-semibold mt-auto">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none" class="mr-1">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    ${rating}
                    <span class="ml-auto text-gray-400 font-normal text-xs">${v.price_tier ?? ''}</span>
                </div>
            </div>
        </a>`;
}

function renderEmpty(message = 'No spots found.') {
    return `
        <div class="col-span-3 text-center py-16 text-gray-400">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 opacity-40">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
            </svg>
            <p class="text-lg font-medium">${message}</p>
            <p class="text-sm mt-1">Try different search terms or clear your filters.</p>
        </div>`;
}

// ---------------------------------------------------------------------------
// SID_08: Fetch + render
// ---------------------------------------------------------------------------

function fetchVendors() {
    if (!resultsGrid) return;

    pushState();
    renderSkeleton(resultsGrid, 6);

    const p = new URLSearchParams();
    if (filters.q)        p.set('q',        filters.q);
    if (filters.category) p.set('category', filters.category);
    if (filters.price)    p.set('price',    filters.price);
    if (filters.rating)   p.set('rating',   filters.rating);
    const qs = p.toString();

    apiFetch(`/api/vendors${qs ? '?' + qs : ''}`)
        .then(json => {
            const vendors = json.data ?? [];
            resultsGrid.innerHTML = vendors.length
                ? vendors.map(renderVendorCard).join('')
                : renderEmpty('No spots found.');
        })
        .catch(err => {
            console.error('[SID_08] Vendor fetch failed:', err);
            resultsGrid.innerHTML = renderEmpty('Could not load results.');
        });
}

const debouncedFetch = debounce(fetchVendors, 400);

// ---------------------------------------------------------------------------
// SID_10: Filter panel
// ---------------------------------------------------------------------------

function toggleFilterPanel() {
    filterPanel?.classList.toggle('hidden');
}

function loadCategories() {
    const container = document.getElementById('filter-categories');
    if (!container || container.dataset.loaded) return;

    apiFetch('/api/categories')
        .then(json => {
            const cats = json.data ?? [];
            container.innerHTML = cats.map(c => `
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" data-filter="category" value="${c.slug}"
                           ${filters.category === c.slug ? 'checked' : ''}
                           class="rounded accent-orange-500">
                    <span class="text-sm text-gray-700">${c.name}</span>
                </label>`).join('');
            container.dataset.loaded = '1';
        })
        .catch(() => {
            if (container) container.innerHTML = '<p class="text-sm text-gray-400">Could not load categories.</p>';
        });
}

function applyFilters() {
    const checked = document.querySelector('[data-filter="category"]:checked');
    filters.category = checked?.value ?? '';

    const activePrice = document.querySelector('[data-filter="price"].is-active');
    filters.price = activePrice?.dataset.value ?? '';

    const ratingSelect = document.getElementById('filter-rating');
    filters.rating = ratingSelect?.value ?? '';

    toggleFilterPanel();
    fetchVendors();
}

function clearFilters() {
    filters.category = '';
    filters.price    = '';
    filters.rating   = '';

    document.querySelectorAll('[data-filter="category"]').forEach(el => { el.checked = false; });
    document.querySelectorAll('[data-filter="price"]').forEach(el => el.classList.remove('is-active'));
    const ratingSelect = document.getElementById('filter-rating');
    if (ratingSelect) ratingSelect.value = '';

    toggleFilterPanel();
    fetchVendors();
}

// ---------------------------------------------------------------------------
// Init
// ---------------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', () => {
    // SID_09: pre-fill from URL, fire initial fetch if params present
    readURLParams();

    // SID_08: live search
    searchInput?.addEventListener('input', e => {
        filters.q = e.target.value.trim();
        debouncedFetch();
    });

    // Prevent form submit from reloading page
    document.querySelector('[data-action="explore-search"]')
        ?.addEventListener('submit', e => e.preventDefault());

    // SID_10: toggle filter panel
    document.querySelector('[data-action="explore-filters-toggle"]')
        ?.addEventListener('click', () => { toggleFilterPanel(); loadCategories(); });

    // Price tier toggle (active state)
    document.querySelectorAll('[data-filter="price"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const wasActive = btn.classList.contains('is-active');
            document.querySelectorAll('[data-filter="price"]').forEach(b => b.classList.remove('is-active'));
            if (!wasActive) btn.classList.add('is-active');
        });
    });

    document.getElementById('filter-apply')?.addEventListener('click', applyFilters);
    document.getElementById('filter-clear')?.addEventListener('click', clearFilters);

    // Close filter panel when clicking outside
    document.addEventListener('click', e => {
        if (!filterPanel?.classList.contains('hidden') &&
            !filterPanel?.contains(e.target) &&
            !e.target.closest('[data-action="explore-filters-toggle"]')) {
            filterPanel?.classList.add('hidden');
        }
    });

    // SID_11: fetch grid when switching to grid tab (if URL has active filters/query)
    document.querySelector('[data-action="show-grid"]')
        ?.addEventListener('click', () => {
            if (filters.q || filters.category || filters.price || filters.rating) {
                fetchVendors();
            }
        });

    // SID_09: fire fetch on load if URL has params
    if (filters.q || filters.category || filters.price || filters.rating) {
        fetchVendors();
    }
});
