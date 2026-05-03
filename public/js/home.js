// public/js/home.js — SID_05 (hero search dropdown) + SID_07 (trending spots)
// Depends on: api.js (apiFetch), ui.js (showToast, renderSkeleton)

// ---------------------------------------------------------------------------
// Shared
// ---------------------------------------------------------------------------

function debounce(fn, delay) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

// ---------------------------------------------------------------------------
// SID_05: Hero search — live suggestion dropdown
// ---------------------------------------------------------------------------

const searchBar      = document.getElementById('hero-search-bar');
const searchInput    = document.getElementById('hero-search-input');
const searchDropdown = document.getElementById('search-dropdown');

function renderDropdown(vendors) {
    if (!vendors.length) {
        searchDropdown.innerHTML = '<p class="px-4 py-3 text-sm text-gray-400">No results found.</p>';
    } else {
        searchDropdown.innerHTML = vendors.map(v => `
            <a href="/place/${v.slug}"
               class="flex items-center gap-3 px-4 py-3 hover:bg-orange-50 border-b border-gray-100 last:border-0 transition">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">${v.business_name}</p>
                    <p class="text-xs text-gray-400">${v.category?.name ?? ''} &middot; ${v.city ?? ''}</p>
                </div>
            </a>
        `).join('');
    }
    searchDropdown.classList.add('is-open');
}

function clearDropdown() {
    searchDropdown.innerHTML = '';
    searchDropdown.classList.remove('is-open');
}

const fetchSuggestions = debounce((query) => {
    if (query.length < 2) { clearDropdown(); return; }

    apiFetch(`/api/vendors?q=${encodeURIComponent(query)}&limit=5`)
        .then(json => renderDropdown(json.data ?? []))
        .catch(err  => {
            console.error('[SID_05] Suggestion fetch failed:', err);
            clearDropdown();
        });
}, 400);

searchInput?.addEventListener('input', e => fetchSuggestions(e.target.value.trim()));

document.addEventListener('click', e => {
    if (!searchBar?.contains(e.target)) clearDropdown();
});

// ---------------------------------------------------------------------------
// SID_07: Trending spots
// ---------------------------------------------------------------------------

const trendingContainer = document.getElementById('trending-container');

function renderVendorCard(vendor) {
    const rating = vendor.avg_rating ? Number(vendor.avg_rating).toFixed(1) : 'New';
    const photo  = vendor.primary_photo
        ? `<img src="${vendor.primary_photo}" alt="${vendor.business_name}" class="w-full h-full object-cover">`
        : `<div class="w-full h-full flex items-center justify-center text-gray-300 text-sm">No photo</div>`;

    return `
        <a href="/place/${vendor.slug}"
           class="block bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
            <div class="h-40 bg-gray-100 overflow-hidden">
                ${photo}
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 text-sm truncate">${vendor.business_name}</h3>
                <p class="text-xs text-gray-400 mt-1 truncate">${vendor.category?.name ?? ''} &middot; ${vendor.city ?? ''}</p>
                <div class="flex items-center gap-1 mt-2">
                    <span class="text-yellow-400 text-sm">&#9733;</span>
                    <span class="text-xs text-gray-600">${rating}</span>
                    <span class="text-xs text-gray-400 ml-auto">${vendor.price_tier ?? ''}</span>
                </div>
            </div>
        </a>
    `;
}

function loadTrendingSpots() {
    if (!trendingContainer) return;

    renderSkeleton(trendingContainer, 6);

    apiFetch('/mock/trending.json')
        .then(json => {
            const vendors = json.data ?? [];
            if (!vendors.length) {
                trendingContainer.innerHTML =
                    '<p class="text-gray-400 col-span-full text-center py-8">No trending spots yet.</p>';
                return;
            }
            trendingContainer.innerHTML = vendors.map(renderVendorCard).join('');
        })
        .catch(err => {
            console.error('[SID_07] Trending fetch failed:', err);
            trendingContainer.innerHTML =
                '<p class="text-gray-400 col-span-full text-center py-8">Could not load trending spots.</p>';
        });
}

loadTrendingSpots();
