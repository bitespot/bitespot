// public/js/vendor-dashboard.js — SID_25: KPI dashboard fetch/render

const kpiContainer = document.getElementById('kpi-container');

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Maps API keys → display label + formatter.
// Duplicate labels (e.g. avg_rating / average_rating) are de-duped — first wins.
const _KPI_META = [
    { key: 'avg_rating',      label: 'Avg Rating',    fmt: v => Number(v).toFixed(1) },
    { key: 'average_rating',  label: 'Avg Rating',    fmt: v => Number(v).toFixed(1) },
    { key: 'total_reviews',   label: 'Reviews',       fmt: v => v },
    { key: 'review_count',    label: 'Reviews',       fmt: v => v },
    { key: 'total_bookmarks', label: 'Bookmarks',     fmt: v => v },
    { key: 'bookmark_count',  label: 'Bookmarks',     fmt: v => v },
    { key: 'total_views',     label: 'Profile Views', fmt: v => v },
    { key: 'view_count',      label: 'Profile Views', fmt: v => v },
];

function _renderKpiCard(label, value) {
    return `
        <div class="p-5 flex flex-col gap-1 items-center text-center">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wide">${_esc(label)}</span>
            <span class="text-3xl font-bold text-gray-800">${_esc(String(value))}</span>
        </div>`;
}

// ── SID_25: Fetch and render KPI cards ────────────────────────────────────────

apiFetch('/api/vendor/dashboard')
    .then(res => {
        const data  = res.data ?? res;
        const seen  = new Set();
        const cards = [];

        for (const { key, label, fmt } of _KPI_META) {
            if (data[key] == null || seen.has(label)) continue;
            seen.add(label);
            cards.push(_renderKpiCard(label, fmt(data[key])));
        }

        if (!cards.length) {
            kpiContainer.innerHTML =
                '<p class="col-span-full p-6 text-center text-sm text-gray-400">No metrics available yet.</p>';
            return;
        }

        kpiContainer.innerHTML = cards.join('');
    })
    .catch(err => {
        console.error('[SID_25] Dashboard metrics fetch failed:', err);
        kpiContainer.innerHTML =
            '<p class="col-span-full p-6 text-center text-sm text-red-400">Could not load metrics.</p>';
    });
