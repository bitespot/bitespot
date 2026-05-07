@extends('layouts.app-no-nav')

@section('content')
@include('components.navbar')

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 320px; width: 100%; border-radius: 12px; z-index: 0; }
    .leaflet-container { border-radius: 12px; }
    #location-display {
        display: flex; align-items: flex-start; gap: 8px;
        background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 10px; padding: 10px 14px;
        font-size: 0.85rem; color: #15803d; margin-top: 10px;
    }
    #location-display.unset { background: #fafafa; border-color: #e5e7eb; color: #9ca3af; }
    #search-results {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;
        background: white; border: 1px solid #d1d5db; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,.1); max-height: 200px; overflow-y: auto;
    }
    #search-results li {
        padding: 9px 12px; cursor: pointer; font-size: 0.82rem; border-bottom: 1px solid #f3f4f6;
    }
    #search-results li:last-child { border-bottom: none; }
    #search-results li:hover { background: #f0fdf4; }
</style>

<div class="min-h-screen bg-gray-50 py-10 px-4">
    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-xl p-8">

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Add an Establishment</h2>
            <p class="text-sm text-gray-500 mt-2">Know a great spot that isn't on BiteSpot yet? Add it here. Anyone can claim ownership later.</p>
        </div>

        <form id="add-form" method="POST" action="{{ route('bitespot.store') }}" class="space-y-5">
            @csrf

            @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Establishment Information --}}
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Establishment Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-1">Business Name *</label>
                        <input id="business_name" name="business_name" type="text" required value="{{ old('business_name') }}"
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                        <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
                        <select id="category_id" name="category_id" required
                            class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-4">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            {{-- Location --}}
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Location *</h3>
                <p class="text-sm text-gray-500 mb-4">Search for the establishment or tap the map to pin its exact location.</p>

                <div style="position:relative;" class="mb-3">
                    <div class="flex gap-2">
                        <input id="map-search" type="text" placeholder="Search address or place name…"
                            class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-green-500 focus:ring-green-500 shadow-sm transition"
                            autocomplete="off">
                        <button type="button" id="map-search-btn"
                            class="px-4 py-2.5 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600 transition shadow-sm">
                            Search
                        </button>
                    </div>
                    <ul id="search-results" style="display:none;"></ul>
                </div>

                <div id="map"></div>

                <div id="location-display" class="unset">
                    <svg width="16" height="16" style="flex-shrink:0;margin-top:1px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C8.686 2 6 4.686 6 8c0 4.5 6 12 6 12s6-7.5 6-12c0-3.314-2.686-6-6-6z"/>
                        <circle cx="12" cy="8" r="2.5"/>
                    </svg>
                    <span id="location-text">No location selected — click the map to pin the establishment.</span>
                </div>

                <input type="hidden" name="lat"      id="input-lat"      value="{{ old('lat') }}">
                <input type="hidden" name="lng"      id="input-lng"      value="{{ old('lng') }}">
                <input type="hidden" name="address"  id="input-address"  value="{{ old('address') }}">
                <input type="hidden" name="city"     id="input-city"     value="{{ old('city') }}">
                <input type="hidden" name="province" id="input-province" value="{{ old('province') }}">
                <input type="hidden" name="district" id="input-district" value="{{ old('district') }}">

                <x-input-error :messages="$errors->get('lat')" class="mt-2" />
            </div>

            {{-- Business Details --}}
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Details</h3>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price Tier *</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="price_tier" value="$" required {{ old('price_tier') === '$' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                            <span class="text-sm">₱ — Budget-friendly</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="price_tier" value="$$" {{ old('price_tier') === '$$' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                            <span class="text-sm">₱₱ — Moderate</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio" name="price_tier" value="$$$" {{ old('price_tier') === '$$$' ? 'checked' : '' }}
                                class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                            <span class="text-sm">₱₱₱ — Premium</span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('price_tier')" class="mt-2" />
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition shadow">
                    Add Establishment
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const DEFAULT_LAT = 11.2456;
    const DEFAULT_LNG = 125.0015;
    const DEFAULT_ZOOM = 14;

    const map = L.map('map').setView([DEFAULT_LAT, DEFAULT_LNG], DEFAULT_ZOOM);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '© OpenStreetMap contributors © CARTO',
    }).addTo(map);

    const pinIcon = L.divIcon({
        className: '',
        html: `<div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#f97316,#fb923c);border:3px solid #fff;box-shadow:0 3px 14px rgba(249,115,22,.4);"></div>`,
        iconSize: [38, 38],
        iconAnchor: [19, 19],
        popupAnchor: [0, -22],
    });

    let marker = null;

    const oldLat = document.getElementById('input-lat').value;
    const oldLng = document.getElementById('input-lng').value;
    if (oldLat && oldLng) {
        marker = L.marker([oldLat, oldLng], { icon: pinIcon, draggable: true }).addTo(map);
        map.setView([oldLat, oldLng], 16);
        marker.on('dragend', () => reverseGeocode(marker.getLatLng().lat, marker.getLatLng().lng));
        setLocationDisplay(document.getElementById('input-address').value || `${oldLat}, ${oldLng}`);
    }

    map.on('click', function (e) {
        placePin(e.latlng.lat, e.latlng.lng);
    });

    function placePin(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(map);
            marker.on('dragend', () => reverseGeocode(marker.getLatLng().lat, marker.getLatLng().lng));
        }
        document.getElementById('input-lat').value = lat;
        document.getElementById('input-lng').value = lng;
        reverseGeocode(lat, lng);
    }

    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`, {
            headers: { 'Accept-Language': 'en' }
        })
        .then(r => r.json())
        .then(data => {
            const a = data.address || {};
            const road          = a.road || a.pedestrian || a.footway || a.path || '';
            const neighbourhood = a.neighbourhood || a.suburb || a.quarter || '';
            const city          = a.city || a.city_district || a.town || a.municipality || a.county || '';
            const province      = a.state || a.region || '';
            const district      = a.suburb || a.neighbourhood || a.quarter || '';

            const parts   = [road, neighbourhood].filter(Boolean);
            const address = parts.length ? parts.join(', ') : (data.display_name || `${lat.toFixed(5)}, ${lng.toFixed(5)}`);

            document.getElementById('input-address').value  = address;
            document.getElementById('input-city').value     = city;
            document.getElementById('input-province').value = province;
            document.getElementById('input-district').value = district;

            setLocationDisplay([address, city, province].filter(Boolean).join(', ') || data.display_name);
        })
        .catch(() => {
            const fallback = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            document.getElementById('input-address').value = fallback;
            setLocationDisplay(fallback);
        });
    }

    function setLocationDisplay(text) {
        const el = document.getElementById('location-display');
        el.classList.remove('unset');
        el.style.background = '';
        el.style.borderColor = '';
        el.style.color = '';
        document.getElementById('location-text').textContent = text;
    }

    // Search
    const searchInput = document.getElementById('map-search');
    const searchBtn   = document.getElementById('map-search-btn');
    const resultsList = document.getElementById('search-results');

    function doSearch() {
        const q = searchInput.value.trim();
        if (!q) return;
        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&countrycodes=ph&limit=5`, {
            headers: { 'Accept-Language': 'en' }
        })
        .then(r => r.json())
        .then(results => {
            resultsList.innerHTML = '';
            if (!results.length) {
                const li = document.createElement('li');
                li.textContent = 'No results found.';
                li.style.color = '#9ca3af';
                resultsList.appendChild(li);
            } else {
                results.forEach(result => {
                    const li = document.createElement('li');
                    li.textContent = result.display_name;
                    li.addEventListener('click', () => {
                        map.setView([parseFloat(result.lat), parseFloat(result.lon)], 17);
                        placePin(parseFloat(result.lat), parseFloat(result.lon));
                        searchInput.value = '';
                        resultsList.style.display = 'none';
                    });
                    resultsList.appendChild(li);
                });
            }
            resultsList.style.display = 'block';
        });
    }

    searchBtn.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });
    document.addEventListener('click', e => {
        if (!e.target.closest('#map-search') && !e.target.closest('#search-results')) {
            resultsList.style.display = 'none';
        }
    });

    // Require location before submit
    document.getElementById('add-form').addEventListener('submit', function (e) {
        if (!document.getElementById('input-lat').value) {
            e.preventDefault();
            const el = document.getElementById('location-display');
            el.classList.remove('unset');
            el.style.background = '#fef2f2';
            el.style.borderColor = '#fecaca';
            el.style.color = '#991b1b';
            document.getElementById('location-text').textContent = 'Please pin the establishment on the map before submitting.';
        }
    });
}());
</script>
@endsection
