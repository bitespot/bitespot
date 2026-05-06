@extends('layouts.app-no-nav')

@section('content')

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
/* ── Page root — height set by JS after measuring sticky navbar ─────────── */
#explore-root { display: flex; flex-direction: column; overflow: hidden; background: #fff; }

/* ── Map — flex child so its height is always definite ─────────────────── */
#map-pane    { display: flex; flex-direction: column; }
#explore-map { flex: 1; z-index: 0; min-height: 0; }

/* ── Markers ───────────────────────────────────────────────────────────── */
.bs-pin { width:38px; height:38px; border-radius:50%; border:3px solid #fff;
          box-shadow:0 3px 14px rgba(249,115,22,.4);
          background:linear-gradient(135deg,#f97316,#fb923c);
          display:flex; align-items:center; justify-content:center;
          cursor:pointer; transition:transform .18s, background .18s, box-shadow .18s; }
.bs-pin.active { background:#111827; box-shadow:0 4px 18px rgba(0,0,0,.36); transform:scale(1.2); }
.bs-pin:hover  { transform:scale(1.1); }

/* ── Popups ────────────────────────────────────────────────────────────── */
.leaflet-popup-content-wrapper { border-radius:14px; box-shadow:0 6px 28px rgba(0,0,0,.13); border:none; padding:0; overflow:hidden; }
.leaflet-popup-content { margin:0; width:224px !important; }
.bsp-img    { width:100%; height:112px; object-fit:cover; display:block; }
.bsp-noimg  { width:100%; height:112px; background:linear-gradient(135deg,#f97316,#fb923c);
              display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.9rem; }
.bsp-body   { padding:11px 13px 13px; }
.bsp-name   { font-weight:700; font-size:.88rem; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px; }
.bsp-meta   { font-size:.73rem; color:#6B7280; margin-bottom:5px; }
.bsp-stars  { display:flex; align-items:center; gap:3px; font-size:.78rem; color:#F59E0B; font-weight:600; margin-bottom:9px; }
.bsp-link   { display:block; text-align:center; background:linear-gradient(90deg,#f97316,#fb923c);
              color:#fff; font-size:.78rem; font-weight:600; padding:6px 0; border-radius:8px;
              text-decoration:none; transition:opacity .15s; }
.bsp-link:hover { opacity:.88; color:#fff; text-decoration:none; }
.leaflet-popup-tip { background:#fff; }

/* ── Sidebar cards ─────────────────────────────────────────────────────── */
.bs-card         { display:flex; gap:12px; padding:12px 16px; text-decoration:none; color:inherit;
                   border-bottom:1px solid #F3F4F6; transition:background .12s; cursor:pointer; }
.bs-card:hover   { background:#FFF7ED; text-decoration:none; }
.bs-card.active  { background:#FEF3C7; border-left:3px solid #f97316; padding-left:13px; }
.bs-card__thumb  { width:72px; height:72px; object-fit:cover; border-radius:10px; flex-shrink:0; }
.bs-card__noimg  { width:72px; height:72px; border-radius:10px; flex-shrink:0;
                   background:linear-gradient(135deg,#fb923c,#fcd34d);
                   display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
.bs-card__body   { flex:1; min-width:0; }
.bs-card__name   { font-weight:600; font-size:.875rem; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bs-card__sub    { font-size:.75rem; color:#6B7280; margin-top:2px; }
.bs-card__city   { font-size:.72rem; color:#9CA3AF; margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bs-card__stars  { display:flex; align-items:center; gap:3px; font-size:.78rem; font-weight:600; color:#F59E0B; margin-top:5px; }

/* ── Category pills ────────────────────────────────────────────────────── */
.cat-pill       { padding:5px 14px; border-radius:9999px; font-size:.82rem; font-weight:500;
                  border:1.5px solid #E5E7EB; background:#fff; color:#374151;
                  cursor:pointer; white-space:nowrap; transition:all .14s; flex-shrink:0; }
.cat-pill:hover { border-color:#f97316; color:#ea580c; }
.cat-pill.active{ background:#f97316; border-color:#f97316; color:#fff; box-shadow:0 2px 8px rgba(249,115,22,.3); }

/* ── View toggle ───────────────────────────────────────────────────────── */
.view-btn        { display:flex; align-items:center; gap:5px; padding:6px 14px; border-radius:9999px;
                   font-size:.82rem; font-weight:500; color:#6B7280; transition:all .14s; }
.view-btn.active { background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.12); color:#ea580c; }

/* ── Price filter active ───────────────────────────────────────────────── */
[data-filter="price"].is-active { border-color:#f97316; background:#FFF7ED; color:#ea580c; font-weight:600; }

/* ── Hide scrollbar on pills row ───────────────────────────────────────── */
#cat-pills-row { -ms-overflow-style:none; scrollbar-width:none; }
#cat-pills-row::-webkit-scrollbar { display:none; }
</style>

{{-- NAVBAR --}}
@include('components.navbar')

{{-- EXPLORE ROOT --}}
<div id="explore-root">

    {{-- ── TOP BAR ──────────────────────────────────────────────────────── --}}
    <div class="flex-shrink-0 bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3 shadow-sm" style="z-index:20">
        {{-- Search --}}
        <form data-action="explore-search" class="flex items-center flex-1 max-w-lg bg-gray-100 rounded-full px-4 py-2 gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 flex-shrink-0">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input id="explore-search-input" type="text" placeholder="Search by name or cuisine..."
                   class="bg-transparent border-none focus:outline-none w-full text-sm" autocomplete="off">
        </form>

        {{-- Filters button --}}
        <button data-action="explore-filters-toggle"
                class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium hover:bg-gray-50 shadow-sm transition">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
            </svg>
            Filters
        </button>

        {{-- Map / List toggle --}}
        <div class="flex-shrink-0 flex bg-gray-100 p-1 rounded-full">
            <button data-view-btn="map" class="view-btn active" onclick="setExploreView('map')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                    <line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>
                </svg>
                Map
            </button>
            <button data-view-btn="list" class="view-btn" onclick="setExploreView('list')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                List
            </button>
        </div>
    </div>

    {{-- ── CATEGORY PILLS ROW ───────────────────────────────────────────── --}}
    <div id="cat-pills-row" class="flex-shrink-0 bg-white border-b border-gray-100 px-4 py-2.5 flex items-center gap-2 overflow-x-auto" style="z-index:19">
        <button class="cat-pill active" data-category="">All</button>
        {{-- Additional pills injected by JS from /api/categories --}}
        <span id="cat-loading" class="text-xs text-gray-400 px-2 flex-shrink-0">Loading…</span>
    </div>

    {{-- ── FILTER DRAWER ─────────────────────────────────────────────────── --}}
    <div id="explore-filter-panel" class="hidden flex-shrink-0 bg-white border-b border-gray-200 px-6 py-5 shadow-sm" style="z-index:18">
        <div class="max-w-xl flex flex-col gap-5">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Price</p>
                <div class="flex gap-2">
                    <button data-filter="price" data-value="$"   class="cat-pill">₱</button>
                    <button data-filter="price" data-value="$$"  class="cat-pill">₱₱</button>
                    <button data-filter="price" data-value="$$$" class="cat-pill">₱₱₱</button>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Minimum Rating</p>
                <select id="filter-rating"
                        class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">
                    <option value="">Any</option>
                    <option value="3">3+ ★</option>
                    <option value="4">4+ ★</option>
                    <option value="4.5">4.5+ ★</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button id="filter-apply"
                        class="px-5 py-2 bg-orange-500 text-white rounded-full text-sm font-medium hover:bg-orange-600 transition">
                    Apply Filters
                </button>
                <button id="filter-clear"
                        class="px-5 py-2 border border-gray-200 text-gray-600 rounded-full text-sm font-medium hover:bg-gray-50 transition">
                    Clear
                </button>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT AREA ─────────────────────────────────────────────── --}}
    <div id="explore-content" class="flex-1 flex overflow-hidden min-h-0">

        {{-- LEFT SIDEBAR (visible in "map" view) --}}
        <div id="sidebar-pane" class="flex flex-col bg-white border-r border-gray-200 overflow-hidden flex-shrink-0" style="width:380px">
            <div class="flex-shrink-0 px-4 py-2.5 bg-gray-50 border-b border-gray-200">
                <p id="result-count" class="text-sm font-semibold text-gray-700">
                    {{ $bitespots->count() }} {{ $bitespots->count() === 1 ? 'place' : 'places' }} found
                </p>
            </div>
            <div id="explore-list" class="flex-1 overflow-y-auto overscroll-contain">
                {{-- Server-rendered initial cards --}}
                @forelse($bitespots as $s)
                    <a href="/place/{{ $s['slug'] }}" class="bs-card" data-vendor-id="{{ $s['id'] }}">
                        @if(!empty($s['image_url']))
                            <img src="{{ $s['image_url'] }}" alt="{{ $s['name'] }}" class="bs-card__thumb">
                        @else
                            <div class="bs-card__noimg">🍽️</div>
                        @endif
                        <div class="bs-card__body">
                            <div class="bs-card__name">{{ $s['name'] }}</div>
                            <div class="bs-card__sub">
                                {{-- category • price_tier --}}
                                {{ implode(' • ', array_filter([$s['category'], $s['price_tier']])) }}
                            </div>
                            @if(!empty($s['city']))
                                <div class="bs-card__city">{{ $s['city'] }}</div>
                            @endif
                            <div class="bs-card__stars">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                {{ $s['rating'] !== null ? number_format($s['rating'], 1) : 'New' }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400 px-6 text-center">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 opacity-40">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                        </svg>
                        <p class="font-medium text-sm">No places yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MAP PANE (visible in "map" view) --}}
        <div id="map-pane" class="flex-1 min-w-0">
            <div id="explore-map"></div>
        </div>

        {{-- FULL LIST PANE (visible in "list" view) --}}
        <div id="list-pane" class="flex-1 overflow-y-auto p-6 hidden">
            <div id="explore-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                {{-- Populated by JS when list view is activated --}}
            </div>
        </div>

    </div>
</div>

@auth
    @include('components.add-bitespot')
@endauth

{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- Initial data from PHP --}}
<script>
window.INITIAL_VENDORS    = {!! json_encode($allVendorsJson) !!};
window.INITIAL_MAP_SPOTS  = {!! json_encode($mapspotsJson) !!};
</script>

<script src="{{ asset('js/explore.js') }}"></script>
@endsection
