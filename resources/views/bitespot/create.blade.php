@extends('layouts.app-no-nav')

@section('content')
@include('components.navbar')

<style>
    .create-root { background: #f9fafb; min-height: calc(100vh - 64px); padding: 2rem 1rem; }
    .create-container { max-width: 640px; margin: 0 auto; background: #fff; border-radius: 1rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
    .create-header { padding: 1.5rem 2rem; border-bottom: 1px solid #f3f4f6; }
    .create-body { padding: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.95rem; }
    .form-label span { font-weight: 400; color: #9ca3af; font-size: 0.85rem; }
    .form-input, .form-select, .form-textarea { width: 100%; border: 1.5px solid #e5e7eb; border-radius: 0.75rem; padding: 0.75rem 1rem; font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s; outline: none; background: #fff; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--color-primary); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }
</style>

<div class="create-root">
    <div class="create-container">
        <div class="create-header">
            <h1 class="text-2xl font-bold text-gray-900">Add an Establishment</h1>
            <p class="text-gray-500 text-sm mt-1">Know a great spot that isn't on BiteSpot yet? Add it here.</p>
        </div>

        <form action="{{ route('bitespot.store') }}" method="POST" class="create-body" id="add-establishment-form">
            @csrf

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">Establishment Name</label>
                <input type="text" name="business_name" class="form-input" placeholder="e.g. Jepoy's Grill & Resto" value="{{ old('business_name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">— Select a category —</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-input" placeholder="Street address" value="{{ old('address') }}" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-input" placeholder="e.g. Tacloban" value="{{ old('city') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Province</label>
                    <input type="text" name="province" class="form-input" placeholder="e.g. Leyte" value="{{ old('province') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description <span>(optional)</span></label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="What kind of food or vibe does this place have?">{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Phone <span>(optional)</span></label>
                    <input type="text" name="phone" class="form-input" placeholder="+63 912 345 6789" value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Price Range <span>(optional)</span></label>
                    <select name="price_tier" class="form-select">
                        <option value="">—</option>
                        <option value="$"  {{ old('price_tier') === '$'   ? 'selected' : '' }}>$ — Budget</option>
                        <option value="$$" {{ old('price_tier') === '$$'  ? 'selected' : '' }}>$$ — Moderate</option>
                        <option value="$$$"{{ old('price_tier') === '$$$' ? 'selected' : '' }}>$$$ — Upscale</option>
                    </select>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full py-3.5 bg-primary hover:bg-primary-hover text-white rounded-xl font-bold text-lg shadow-lg transition-colors">
                    Add Establishment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
