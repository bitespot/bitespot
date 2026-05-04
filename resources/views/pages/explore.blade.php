@extends('layouts.app-no-nav')

@section('content')
    <style>
    /* Leaflet */
    #explore-map { width: 100%; height: 100%; z-index: 0; }
    .leaflet-popup-content-wrapper {
        border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: none; padding: 0; overflow: hidden;
    }
    .leaflet-popup-content { margin: 0; width: 220px !important; }
    .bs-map-popup__img { width: 100%; height: 120px; object-fit: cover; display: block; }
    .bs-map-popup__img-placeholder {
        width: 100%; height: 120px;
        background: linear-gradient(135deg, #ff8800 0%, #ffb347 100%);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 2rem;
    }
    .bs-map-popup__body { padding: 12px 14px 14px; }
    .bs-map-popup__name { font-weight: 700; font-size: 0.95rem; color: #111827; margin-bottom: 2px; }
    .bs-map-popup__meta { font-size: 0.78rem; color: #6B7280; margin-bottom: 6px; }
    .bs-map-popup__rating {
        display: flex; align-items: center; gap: 4px;
        font-size: 0.82rem; color: #F59E0B; font-weight: 600; margin-bottom: 10px;
    }
    .bs-map-popup__link {
        display: block; text-align: center;
        background: linear-gradient(90deg, #ff8800, #ffb347);
        color: white; font-size: 0.82rem; font-weight: 600;
        padding: 7px 0; border-radius: 8px; text-decoration: none; transition: opacity 0.15s;
    }
    .bs-map-popup__link:hover { opacity: 0.88; color: white; text-decoration: none; }
    .leaflet-popup-tip { background: white; }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    {{-- NAVBAR --}}
    @include('components.navbar')

    {{-- MAIN EXPLORE CONTENT --}}
    <div x-data="{ tab: 'map' }" class="min-h-screen bg-gray-50 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-4 z-10 shadow-sm">
            <form data-action="explore-search" class="flex items-center w-full sm:w-auto flex-1 max-w-md bg-gray-100 rounded-full px-4 py-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 mr-2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input id="explore-search-input" type="text" placeholder="Search by name or cuisine..." class="bg-transparent border-none focus:outline-none w-full text-sm" autocomplete="off">
            </form>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button data-action="explore-filters-toggle" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-full text-sm font-medium hover:bg-gray-50 shadow-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                        <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                    </svg> Filters
                </button>
                <div class="flex bg-gray-100 p-1 rounded-full">
                    <button @click="tab = 'map'; $nextTick(() => initMap())"
                            :class="tab === 'map' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:text-gray-900'"
                            class="flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                            <line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/>
                        </svg> Map
                    </button>
                    <button @click="tab = 'grid'" data-action="show-grid"
                            :class="tab === 'grid' ? 'bg-white shadow-sm text-orange-600' : 'text-gray-600 hover:text-gray-900'"
                            class="flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all duration-200">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg> Grid
                    </button>
                </div>
            </div>
        </div>

        {{-- FILTER PANEL — SID_10 --}}
        <div id="explore-filter-panel" class="hidden bg-white border-b border-gray-200 px-6 py-5 shadow-sm z-10">
            <div class="max-w-3xl mx-auto flex flex-col gap-5">

                {{-- Categories --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Category</p>
                    <div id="filter-categories" class="flex flex-wrap gap-2">
                        <span class="text-sm text-gray-400">Loading…</span>
                    </div>
                </div>

                {{-- Price tier --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Price</p>
                    <div class="flex gap-2">
                        <button data-filter="price" data-value="$"
                                class="px-4 py-1.5 rounded-full border border-gray-200 text-sm hover:border-orange-400 transition">$</button>
                        <button data-filter="price" data-value="$$"
                                class="px-4 py-1.5 rounded-full border border-gray-200 text-sm hover:border-orange-400 transition">$$</button>
                        <button data-filter="price" data-value="$$$"
                                class="px-4 py-1.5 rounded-full border border-gray-200 text-sm hover:border-orange-400 transition">$$$</button>
                    </div>
                </div>

                {{-- Min rating --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Minimum Rating</p>
                    <select id="filter-rating" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-orange-400">
                        <option value="">Any</option>
                        <option value="3">3+ ★</option>
                        <option value="4">4+ ★</option>
                        <option value="4.5">4.5+ ★</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-1">
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

        <div class="flex-1 flex flex-col">

            {{-- MAP TAB --}}
            <div x-show="tab === 'map'" class="w-full h-[70vh] relative" x-cloak>
                <div id="explore-map"></div>
            </div>

            {{-- GRID TAB --}}
            <div x-show="tab === 'grid'" class="w-full p-6" x-cloak>
                <div id="explore-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @forelse($bitespots as $spot)
                        <a href="{{ route('place.show', $spot['id']) }}" class="bg-white rounded-xl shadow hover:shadow-md transition-shadow duration-200 flex flex-col overflow-hidden group">
                            @if(!empty($spot['image_url']))
                                <img src="{{ $spot['image_url'] }}" alt="{{ $spot['name'] }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-orange-400 to-orange-300 flex items-center justify-center text-white text-3xl">
                                    🍽️
                                </div>
                            @endif
                            <div class="p-4 flex flex-col flex-1">
                                <h3 class="font-bold text-lg mb-1 text-gray-900">{{ $spot['name'] }}</h3>
                                <p class="text-gray-500 text-sm mb-3">{{ $spot['category'] }} • {{ $spot['location'] }}</p>
                                <div class="flex items-center text-sm text-amber-500 font-semibold mt-auto">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none" class="mr-1">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    {{ number_format($spot['rating'], 1) }}
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-3 text-center py-16 text-gray-400">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mx-auto mb-3 opacity-40">
                                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                            </svg>
                            <p class="text-lg font-medium">No BiteSpots found yet.</p>
                            <p class="text-sm mt-1">Be the first to add one!</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    @auth
        @include('components.add-bitespot')
    @endauth

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WLcE=" crossorigin=""></script>

    <script>
    
    const bitespots = {!! json_encode($mapspotsJson) !!};

    let map = null;

    function initMap() {
        if (map) { map.invalidateSize(); return; }

        map = L.map('explore-map', { center: [12.8797, 121.7740], zoom: 6 });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const biteIcon = L.divIcon({
            className: '',
            html: `<div style="width:36px;height:36px;background:linear-gradient(135deg,#ff8800,#ffb347);
                border-radius:50% 50% 50% 0;transform:rotate(-45deg);
                border:3px solid white;box-shadow:0 3px 10px rgba(0,0,0,0.25);">
                <span style="display:flex;align-items:center;justify-content:center;
                    width:100%;height:100%;transform:rotate(45deg);font-size:14px;">🍽️</span>
            </div>`,
            iconSize: [36, 36], iconAnchor: [18, 36], popupAnchor: [0, -38],
        });

        bitespots.forEach(function(spot) {
            var imageHtml = spot.image_url
                ? '<img src="' + spot.image_url + '" alt="' + spot.name + '" class="bs-map-popup__img">'
                : '<div class="bs-map-popup__img-placeholder">🍽️</div>';

            var stars = '★'.repeat(Math.round(spot.rating)) + '☆'.repeat(5 - Math.round(spot.rating));
            var popupHtml = '<div class="bs-map-popup">'
                + imageHtml
                + '<div class="bs-map-popup__body">'
                + '<div class="bs-map-popup__name">' + spot.name + '</div>'
                + '<div class="bs-map-popup__meta">' + spot.category + ' · ' + spot.location + '</div>'
                + '<div class="bs-map-popup__rating"><span>' + stars + '</span>'
                + '<span style="color:#374151;">' + Number(spot.rating).toFixed(1) + '</span></div>'
                + '<a href="/bitespot/' + spot.id + '" class="bs-map-popup__link">View BiteSpot →</a>'
                + '</div></div>';

            L.marker([spot.lat, spot.lng], { icon: biteIcon })
                .addTo(map)
                .bindPopup(popupHtml, { maxWidth: 240, minWidth: 220 });
        });

        if (bitespots.length > 0) {
            var bounds = L.latLngBounds(bitespots.map(function(s) { return [s.lat, s.lng]; }));
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                if (bitespots.length === 0) {
                    map.setView([pos.coords.latitude, pos.coords.longitude], 13);
                }
                L.circle([pos.coords.latitude, pos.coords.longitude], {
                    color: '#4F46E5', fillColor: '#818CF8',
                    fillOpacity: 0.25, radius: 200, weight: 2,
                }).addTo(map).bindTooltip('You are here');
            }, function() {});
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
    });
    </script>

    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/ui.js') }}"></script>
    <script src="{{ asset('js/explore.js') }}"></script>
@endsection
