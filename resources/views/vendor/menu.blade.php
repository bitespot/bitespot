@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50 pb-24">

    {{-- ── HEADER ──────────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-400 pt-12 pb-20 px-4">
        <div class="max-w-3xl mx-auto flex items-center gap-3">
            <a href="{{ route('vendor.dashboard') }}"
               class="text-white/70 hover:text-white transition shrink-0" aria-label="Back to dashboard">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <h1 class="text-white text-2xl font-bold">Menu</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_27: Add / Edit form panel (hidden by default) ──────────────── --}}
        <div id="menu-form-panel" class="hidden bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 id="menu-form-title" class="text-sm font-semibold text-gray-700">Add Item</h2>
                <button id="menu-form-close" type="button"
                        class="text-gray-400 hover:text-gray-600 transition" aria-label="Close form">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form id="menu-item-form" class="px-5 py-5 space-y-4" novalidate>
                <input type="hidden" id="menu-edit-id" value="">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Item name <span class="text-red-500">*</span>
                    </label>
                    <input id="menu-name" type="text" maxlength="255" autocomplete="off"
                           placeholder="e.g. Sinigang na Baboy"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p id="menu-name-error" class="text-xs text-red-500 mt-1 min-h-[1rem]"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Price (₱) <span class="text-red-500">*</span>
                        </label>
                        <input id="menu-price" type="number" min="0" step="0.01"
                               placeholder="0.00"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <p id="menu-price-error" class="text-xs text-red-500 mt-1 min-h-[1rem]"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                        <input id="menu-category" type="text" maxlength="100" autocomplete="off"
                               placeholder="e.g. Mains"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea id="menu-description" rows="2" maxlength="1000"
                              placeholder="Short description (optional)"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none
                                     focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
                </div>

                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input id="menu-available" type="checkbox" checked
                           class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-300">
                    <span class="text-sm text-gray-700">Available for order</span>
                </label>

                <p id="menu-form-error" class="text-xs text-red-500 min-h-[1rem]"></p>

                <div class="flex gap-2 pt-1">
                    <button id="menu-submit-btn" type="submit"
                            class="px-5 py-2 bg-orange-500 text-white text-sm font-medium rounded-full
                                   hover:bg-orange-600 transition disabled:opacity-50">
                        Save Item
                    </button>
                    <button id="menu-cancel-btn" type="button"
                            class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        {{-- ── SID_27: Items list ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Menu Items</h2>
                <button id="menu-add-btn" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 bg-orange-500 text-white
                               text-xs font-medium rounded-full hover:bg-orange-600 transition">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Item
                </button>
            </div>
            <div id="menu-items-container" class="divide-y divide-gray-100">
                <p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading menu…</p>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/vendor-menu.js') }}"></script>

@endsection
