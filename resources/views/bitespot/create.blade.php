{{-- C:\Software Projects\bitespot\resources\views\bitespot\create.blade.php --}}
@extends('layouts.app-no-nav')

@section('content')
@include('components.navbar')

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

    /* Dynamic Food Items */
    .food-item-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; margin-top: 1rem; position: relative; }
    .remove-food-btn { position: absolute; top: 1rem; right: 1rem; color: #ef4444; background: #fef2f2; border: none; border-radius: 0.5rem; padding: 0.4rem; cursor: pointer; display: flex; }
</style>

<div class="create-root">
    <div class="create-container">
        <div class="create-header">
            <h1 class="text-2xl font-bold text-gray-900">Post a BiteSpot</h1>
            <p class="text-gray-500 text-sm mt-1">Share your experience. Location will be auto-detected.</p>
        </div>

        <form action="{{ route('bitespot.store') }}" method="POST" enctype="multipart/form-data" class="create-body" id="bitespot-form">
            @csrf
            
            {{-- General Spot Information --}}
            <div class="form-group">
                <label class="form-label">Establishment Name</label>
                <input type="text" name="spot_name" class="form-input" placeholder="e.g. Jepoy's Grill & Resto" required>
            </div>

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

            <hr class="my-8 border-gray-200">

            {{-- Dynamic Food/Drinks Section --}}
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">What did you eat?</h2>
                <span class="text-sm text-gray-500">(Optional)</span>
            </div>

            <div id="food-items-container">
                {{-- Food Items injected here via JS --}}
            </div>

            <button type="button" onclick="addFoodItem()" class="w-full py-3 mt-4 border-2 border-dashed border-primary text-primary rounded-xl font-semibold hover:bg-cream transition-colors flex items-center justify-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Food or Drink
            </button>

            <div class="mt-8">
                <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-lg shadow-lg transition-colors">
                    Post BiteSpot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let foodCount = 0;

    function addFoodItem() {
        foodCount++;
        const container = document.getElementById('food-items-container');
        const id = `food_${foodCount}`;
        
        const html = `
            <div class="food-item-card" id="${id}">
                <button type="button" class="remove-food-btn" onclick="document.getElementById('${id}').remove()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                
                <div class="form-group pr-10">
                    <label class="form-label">Food/Drink Name</label>
                    <input type="text" name="foods[${foodCount}][name]" class="form-input" placeholder="e.g. Spicy Ramen" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Photo</label>
                    <div class="upload-zone" style="padding: 1.5rem;" onclick="document.getElementById('photo_${id}').click()">
                        <span class="text-sm text-gray-500 font-medium">+ Add Photo</span>
                        <input type="file" id="photo_${id}" name="foods[${foodCount}][photo]" accept="image/*" class="hidden" onchange="previewImage(this, 'preview_${id}', 'remove_${id}')">
                        <img id="preview_${id}" class="upload-preview" alt="Preview">
                        <button type="button" id="remove_${id}" class="upload-remove" onclick="event.stopPropagation(); removeImage('photo_${id}', 'preview_${id}', 'remove_${id}')">&times;</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rating</label>
                    <div class="star-rating">
                        <input type="radio" id="star5_${id}" name="foods[${foodCount}][rating]" value="5"><label for="star5_${id}"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                        <input type="radio" id="star4_${id}" name="foods[${foodCount}][rating]" value="4"><label for="star4_${id}"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                        <input type="radio" id="star3_${id}" name="foods[${foodCount}][rating]" value="3"><label for="star3_${id}"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                        <input type="radio" id="star2_${id}" name="foods[${foodCount}][rating]" value="2"><label for="star2_${id}"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                        <input type="radio" id="star1_${id}" name="foods[${foodCount}][rating]" value="1" checked><label for="star1_${id}"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label">Review</label>
                    <textarea name="foods[${foodCount}][review]" class="form-textarea" rows="2" placeholder="Was it tasty?"></textarea>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', html);
    }

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

    // Capture location silently on load
    window.onload = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    // Inject into form
                    const form = document.getElementById('bitespot-form');
                    form.insertAdjacentHTML('beforeend', `<input type="hidden" name="latitude" value="${lat}">`);
                    form.insertAdjacentHTML('beforeend', `<input type="hidden" name="longitude" value="${lng}">`);
                },
                err => console.log("Location denied or unavailable.")
            );
        }
    };
</script>
@endsection