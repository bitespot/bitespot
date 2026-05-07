{{-- C:\Software Projects\bitespot\resources\views\bitespot\create.blade.php --}}
@extends('layouts.app-no-nav')

@section('content')
@include('components.navbar')

{{-- Leaflet CSS and JS for the Map --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .create-root { background: #f9fafb; min-height: calc(100vh - 64px); padding: 2rem 1rem; }
    .create-container { max-width: 680px; margin: 0 auto; background: #fff; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
    .create-header { padding: 1.5rem 2rem; border-bottom: 1px solid #f3f4f6; }
    .create-body { padding: 2rem; }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.95rem; }
    .form-input, .form-textarea { width: 100%; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; font-family: inherit; transition: border-color 0.2s; outline: none; }
    .form-input:focus, .form-textarea:focus { border-color: var(--color-primary); }
    
    /* Upload Zone */
    .upload-zone { border: 2px dashed #d1d5db; border-radius: 1rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.2s; background: #f9fafb; position: relative; overflow: hidden; }
    .upload-zone:hover { border-color: var(--color-primary); background: var(--color-cream); }
    .upload-preview { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: none; z-index: 10; }
    .upload-remove { position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: none; align-items: center; justify-content: center; z-index: 11; cursor: pointer; }
    
    /* Star Rating */
    .star-rating { display: flex; gap: 0.25rem; flex-direction: row-reverse; justify-content: flex-end; }
    .star-rating input { display: none; }
    .star-rating label { cursor: pointer; color: #d1d5db; transition: color 0.2s; }
    .star-rating label svg { width: 28px; height: 28px; fill: currentColor; }
    .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #facc15; }

    /* Map Styles */
    #create-map { z-index: 10; }
    .autocomplete-dropdown { max-height: 200px; overflow-y: auto; }
</style>

<div class="create-root">
    <div class="create-container">
        <div class="create-header">
            <h1 class="text-2xl font-bold text-gray-900">Post a BiteSpot</h1>
            <p class="text-gray-500 text-sm mt-1">Share your experience with the community.</p>
        </div>

        <form action="{{ route('bitespot.store') }}" method="POST" enctype="multipart/form-data" class="create-body" id="bitespot-form">
            @csrf
            
            {{-- Hidden Inputs for Location & Vendor Association --}}
            <input type="hidden" name="latitude" id="lat-input" value="">
            <input type="hidden" name="longitude" id="lng-input" value="">
            <input type="hidden" name="vendor_id" id="vendor-id-input" value="">
            
            {{-- Establishment Name (Search Bar) --}}
            <div class="form-group relative">
                <label class="form-label">Establishment Name</label>
                <input type="text" id="spot-name-input" name="spot_name" class="form-input" placeholder="Search a known spot or type a new one..." autocomplete="off" required>
                
                {{-- Dropdown Results --}}
                <div id="autocomplete-results" class="autocomplete-dropdown absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 hidden">
                    </div>
            </div>

            {{-- General Photo --}}
            <div class="form-group">
                <label class="form-label">Add a Photo of the Place</label>
                <div class="upload-zone" onclick="document.getElementById('general-photo').click()">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" class="mx-auto mb-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    <span class="text-sm text-gray-500 font-medium">Click or tap to open camera/gallery</span>
                    <input type="file" id="general-photo" name="general_photo" accept="image/*" class="hidden" onchange="previewImage(this, 'general-preview', 'general-remove')">
                    <img id="general-preview" class="upload-preview" alt="Preview">
                    <button type="button" id="general-remove" class="upload-remove" onclick="event.stopPropagation(); removeImage('general-photo', 'general-preview', 'general-remove')">&times;</button>
                </div>
            </div>

            {{-- Map Location Panel (Appears conditionally) --}}
            <div id="location-panel" class="form-group bg-gray-50 p-4 rounded-xl border border-gray-100" style="display: none;">
                <div class="flex items-center justify-between mb-3">
                    <label class="form-label mb-0">Pinpoint Location <span class="text-orange-500 text-xs ml-1 font-bold">NEW ESTABLISHMENT</span></label>
                    <button type="button" id="use-location-btn" class="flex items-center gap-1.5 text-xs font-semibold text-white bg-gray-900 px-3 py-1.5 rounded-md hover:bg-gray-800 transition-colors">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                        Use My Location
                    </button>
                </div>
                
                <div id="create-map" class="w-full h-48 rounded-lg shadow-inner mb-2"></div>
                <p class="text-xs text-gray-500 text-center">Drag the marker to the exact location of the establishment.</p>
            </div>

            {{-- Ratings & Reviews --}}
            <div class="form-group">
                <label class="form-label">Overall Rating</label>
                <div class="star-rating">
                    <input type="radio" id="spot-star5" name="spot_rating" value="5"><label for="spot-star5"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                    <input type="radio" id="spot-star4" name="spot_rating" value="4"><label for="spot-star4"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                    <input type="radio" id="spot-star3" name="spot_rating" value="3"><label for="spot-star3"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                    <input type="radio" id="spot-star2" name="spot_rating" value="2"><label for="spot-star2"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                    <input type="radio" id="spot-star1" name="spot_rating" value="1" checked><label for="spot-star1"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Review the Place</label>
                <textarea name="spot_review" class="form-textarea" rows="3" placeholder="How was the ambiance? The service?"></textarea>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold text-lg shadow-lg transition-colors">
                    Post BiteSpot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- Image Preview Logic ---
    function previewImage(input, previewId, removeId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).style.display = 'block';
                document.getElementById(removeId).style.display = 'flex';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(inputId, previewId, removeId) {
        document.getElementById(inputId).value = '';
        document.getElementById(previewId).style.display = 'none';
        document.getElementById(previewId).src = '';
        document.getElementById(removeId).style.display = 'none';
    }

    // --- Leaflet Map Logic ---
    let map, marker;
    const locationPanel = document.getElementById('location-panel');
    const latInput = document.getElementById('lat-input');
    const lngInput = document.getElementById('lng-input');
    const vendorIdInput = document.getElementById('vendor-id-input');

    function initMap() {
        // Default Center (Approximate Philippines)
        const defaultLat = 11.248;
        const defaultLng = 125.007;

        map = L.map('create-map').setView([defaultLat, defaultLng], 14);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(map);

        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        // Update hidden inputs when marker is dragged
        marker.on('dragend', function() {
            const position = marker.getLatLng();
            latInput.value = position.lat;
            lngInput.value = position.lng;
        });

        // Set initial values
        latInput.value = defaultLat;
        lngInput.value = defaultLng;
    }

    function toggleMapPanel(show) {
        if (show) {
            locationPanel.style.display = 'block';
            // Important: Leaflet requires a resize trigger when a hidden map becomes visible
            setTimeout(() => { if (map) map.invalidateSize(); }, 100);
        } else {
            locationPanel.style.display = 'none';
        }
    }

    document.getElementById('use-location-btn').addEventListener('click', function() {
        if (navigator.geolocation) {
            const btn = this;
            btn.innerHTML = 'Locating...';
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    btn.innerHTML = 'Use My Location';
                },
                err => {
                    alert("Location access denied. Please drag the pin manually.");
                    btn.innerHTML = 'Use My Location';
                }
            );
        }
    });

    // --- Autocomplete Search Logic ---
    const searchInput = document.getElementById('spot-name-input');
    const resultsBox = document.getElementById('autocomplete-results');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        // Every time they type, we assume it's a NEW spot until they click a dropdown item
        vendorIdInput.value = ''; 
        toggleMapPanel(true);

        if (query.length < 2) {
            resultsBox.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/vendors?q=${encodeURIComponent(query)}&limit=5`)
                .then(res => res.json())
                .then(json => {
                    const vendors = json.data || [];
                    if (vendors.length === 0) {
                        resultsBox.classList.add('hidden');
                        return;
                    }

                    resultsBox.innerHTML = vendors.map(v => `
                        <div class="px-4 py-3 cursor-pointer hover:bg-orange-50 border-b border-gray-100 last:border-0" 
                             onclick="selectVendor('${v.id}', '${v.business_name.replace(/'/g, "\\'")}', '${v.latitude}', '${v.longitude}')">
                            <p class="text-sm font-semibold text-gray-800">${v.business_name}</p>
                            <p class="text-xs text-gray-500">${v.city || 'Vendor Database'}</p>
                        </div>
                    `).join('');
                    resultsBox.classList.remove('hidden');
                }).catch(err => console.log('Search error:', err));
        }, 300);
    });

    window.selectVendor = function(id, name, lat, lng) {
        // Fill input and ID
        searchInput.value = name;
        vendorIdInput.value = id;
        
        // Populate coordinates if they exist in DB
        if(lat && lng && lat !== 'null') {
            latInput.value = lat;
            lngInput.value = lng;
        }

        resultsBox.classList.add('hidden');
        
        // Hide the map because the user selected an existing spot
        toggleMapPanel(false);
    };

    // Close dropdown if clicked outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.add('hidden');
        }
    });

    // Initialize Map on load
    window.addEventListener('DOMContentLoaded', () => {
        initMap();
        // Since input is empty on load, it's considered "New", so show map
        toggleMapPanel(true);
    });

</script>
@endsection