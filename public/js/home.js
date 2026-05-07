// --- Custom API Fetch Wrapper ---
window.apiFetch = async function(endpoint, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const defaultHeaders = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };

    if (csrfToken) {
        defaultHeaders['X-CSRF-TOKEN'] = csrfToken;
    }

    const config = {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    };

    const response = await fetch(endpoint, config);
    if (!response.ok) throw new Error(`API Error: ${response.status}`);
    return response.json();
};

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
        .catch(err => {
            console.error('[SID_05] Suggestion fetch failed:', err);
            clearDropdown();
        });
}, 400);

searchInput?.addEventListener('input', e => fetchSuggestions(e.target.value.trim()));

document.addEventListener('click', e => {
    if (!searchBar?.contains(e.target)) clearDropdown();
});

// ---------------------------------------------------------------------------
// SID_07: Trending Spots & Hidden Gems — grid card renderer
// ---------------------------------------------------------------------------

// These IDs match dashboard.blade.php exactly
const trendingContainer = document.getElementById('trending-container');
const gemsContainer     = document.getElementById('gems-container');

function renderGridCard(vendor) {
    const name        = vendor.business_name || 'Unknown Spot';
    const slug        = vendor.slug || '#';
    const category    = vendor.category?.name ?? vendor.category ?? '';
    const city        = vendor.city ?? '';
    const rating      = vendor.avg_rating ? Number(vendor.avg_rating).toFixed(1) : null;
    const reviewCount = vendor.reviews_count || 0;
    const priceTier   = vendor.price_tier_label ?? vendor.price_tier ?? '';

    const photoUrl = vendor.primary_photo
        || (vendor.photos && vendor.photos.length > 0
            ? `/storage/${vendor.photos[0].photo_path}`
            : null);

    const imgHtml = photoUrl
        ? `<img src="${photoUrl}" alt="${name}" class="bs-card__img">`
        : `<div class="bs-card__img bs-card__img--placeholder">🍽️</div>`;

    const ratingHtml = rating
        ? `<svg class="w-3.5 h-3.5 text-yellow-400 fill-current" viewBox="0 0 20 20">
               <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
           </svg>
           <span>${rating}</span>
           <span class="text-gray-400">(${reviewCount})</span>`
        : `<span class="text-gray-400 text-xs">New</span>`;

    const meta = [category, priceTier].filter(Boolean).join(' · ');

    return `
        <a href="/place/${slug}" class="bs-card">
            <div class="bs-card__img-wrap">
                ${imgHtml}
            </div>
            <div class="bs-card__body">
                <h3 class="bs-card__name">${name}</h3>
                ${meta  ? `<p class="bs-card__meta">${meta}</p>` : ''}
                ${city  ? `<p class="bs-card__city">${city}</p>` : ''}
                <div class="bs-card__rating">
                    ${ratingHtml}
                </div>
            </div>
        </a>
    `;
}

function loadDashboardSpots() {
    if (!trendingContainer && !gemsContainer) return;

    // Use the two real endpoints from routes/api.php:
    //   GET /api/trending  → top-rated / featured spots
    //   GET /api/vendors   → full listing pool for hidden gems
    Promise.all([
        apiFetch('/api/trending'),
        apiFetch('/api/vendors?limit=50'),
    ])
    .then(([trendingJson, vendorsJson]) => {
        // /api/trending returns an array or {data:[...]}
        const trendingVendors = Array.isArray(trendingJson)
            ? trendingJson
            : (trendingJson.data ?? []);

        // /api/vendors returns {data:[...]} (standard Laravel paginator)
        const allVendors = Array.isArray(vendorsJson)
            ? vendorsJson
            : (vendorsJson.data ?? []);

        // ── Trending Spots (up to 6 from /api/trending) ──
        if (trendingContainer) {
            if (!trendingVendors.length) {
                trendingContainer.innerHTML =
                    '<p class="text-sm text-gray-400 col-span-full">No trending spots found.</p>';
            } else {
                const shuffled = [...trendingVendors].sort(() => 0.5 - Math.random());
                trendingContainer.innerHTML = shuffled.slice(0, 6).map(renderGridCard).join('');
            }
        }

        // ── Hidden Gems (3 random picks from the full vendor pool) ──
        if (gemsContainer) {
            if (!allVendors.length) {
                gemsContainer.innerHTML =
                    '<p class="text-sm text-gray-400 col-span-full">No gems found.</p>';
            } else {
                const shuffled = [...allVendors].sort(() => 0.5 - Math.random());
                gemsContainer.innerHTML = shuffled.slice(0, 3).map(renderGridCard).join('');
            }
        }
    })
    .catch(err => {
        console.error('[SID_07] Dashboard spots fetch failed:', err);
        const errorHtml = '<p class="text-sm text-red-400 col-span-full">Could not load spots.</p>';
        if (trendingContainer) trendingContainer.innerHTML = errorHtml;
        if (gemsContainer)     gemsContainer.innerHTML     = errorHtml;
    });
}

loadDashboardSpots();