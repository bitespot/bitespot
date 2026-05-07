@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="vd-root" style="display:block;">

<div style="max-width:860px;margin:0 auto;padding:2.5rem 1.5rem 4rem;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-family:'Fraunces',serif;font-size:1.75rem;font-weight:700;color:#1a1612;margin:0 0 0.25rem;">
                My Establishments
            </h1>
            <p style="color:#78716c;font-size:0.9rem;margin:0;">Select an establishment to manage, or add a new one.</p>
        </div>
        <a href="/vendor/register"
           style="display:inline-flex;align-items:center;gap:6px;padding:0.65rem 1.25rem;background:var(--orange);color:#fff;border-radius:10px;font-weight:600;font-size:0.88rem;text-decoration:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Establishment
        </a>
    </div>

    {{-- Establishment list --}}
    <div id="est-list" style="display:flex;flex-direction:column;gap:1rem;">
        {{-- skeleton --}}
        <div class="vd-kpi-skeleton" style="height:110px;border-radius:16px;width:100%;"></div>
        <div class="vd-kpi-skeleton" style="height:110px;border-radius:16px;width:100%;"></div>
    </div>

    {{-- Empty state (hidden until JS decides) --}}
    <div id="est-empty" style="display:none;text-align:center;padding:4rem 1rem;">
        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
            <img src="/images/dashboard/who_uses_bitespot/vendors.png" alt="Food Vendors" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
        </span>
        <h2 style="font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;color:#1a1612;margin-bottom:0.5rem;">No establishments yet</h2>
        <p style="color:#78716c;font-size:0.9rem;margin-bottom:1.5rem;">Register your first establishment to get started.</p>
        <a href="/vendor/register"
           style="display:inline-flex;align-items:center;gap:6px;padding:0.65rem 1.4rem;background:var(--orange);color:#fff;border-radius:10px;font-weight:600;font-size:0.88rem;text-decoration:none;">
            Register Establishment
        </a>
    </div>

</div>
</div>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function statusBadge(status) {
        const map = {
            approved:  { label: 'Approved',  bg: '#d1fae5', color: '#065f46' },
            pending:   { label: 'Pending',   bg: '#fef3c7', color: '#92400e' },
            rejected:  { label: 'Rejected',  bg: '#fee2e2', color: '#991b1b' },
            suspended: { label: 'Suspended', bg: '#f3f4f6', color: '#374151' },
        };
        const s = map[status] ?? { label: status, bg: '#f3f4f6', color: '#374151' };
        return `<span style="display:inline-block;padding:0.2rem 0.65rem;border-radius:999px;font-size:0.75rem;font-weight:600;background:${s.bg};color:${s.color};">${s.label}</span>`;
    }

    function stars(n) {
        return n > 0 ? '★'.repeat(Math.round(n)) + ' ' + parseFloat(n).toFixed(1) : '—';
    }

    async function load() {
        let data = [];
        try {
            const r = await fetch('/api/vendor/establishments', {
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const json = await r.json();
            data = json.data ?? json;
        } catch {
            document.getElementById('est-list').innerHTML =
                `<p style="color:#ef4444;text-align:center;">Failed to load establishments.</p>`;
            return;
        }

        const list = document.getElementById('est-list');

        if (!data.length) {
            list.style.display = 'none';
            document.getElementById('est-empty').style.display = '';
            return;
        }

        list.innerHTML = data.map(v => {
            const photo = v.cover_photo_url || v.profile_photo_url || null;
            return `
            <div style="display:flex;align-items:center;gap:1.25rem;background:#fff;border:1px solid #ebe9e4;border-radius:16px;padding:1.25rem 1.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <div style="width:72px;height:72px;border-radius:12px;overflow:hidden;flex-shrink:0;background:#f5f4f2;">
                    ${photo
                        ? `<img src="${photo}" alt="${v.business_name}" style="width:100%;height:100%;object-fit:cover;">`
                        : `<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:1.75rem;">🏪</div>`}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.3rem;flex-wrap:wrap;">
                        <span style="font-family:'Fraunces',serif;font-size:1.1rem;font-weight:700;color:#1a1612;">${v.business_name}</span>
                        ${statusBadge(v.status)}
                    </div>
                    <div style="font-size:0.82rem;color:#78716c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${v.address || ''}${v.city ? ', ' + v.city : ''}
                    </div>
                    <div style="display:flex;gap:1.25rem;margin-top:0.4rem;font-size:0.8rem;color:#a8a29e;">
                        <span>⭐ ${stars(v.avg_rating)}</span>
                        <span>💬 ${v.review_count} review${v.review_count !== 1 ? 's' : ''}</span>
                    </div>
                </div>
                <a href="/vendor-dashboard/${v.id}"
                   style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:0.55rem 1.1rem;background:var(--orange);color:#fff;border-radius:9px;font-weight:600;font-size:0.83rem;text-decoration:none;">
                    Manage
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
            `;
        }).join('');
    }

    load();
}());
</script>

@endsection
