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
            <h1 class="text-white text-2xl font-bold">Settings</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_30: Business profile form ──────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Business Profile</h2>
            </div>
            <form id="vendor-settings-form" class="px-5 py-5 space-y-4" novalidate>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Business name <span class="text-red-500">*</span>
                    </label>
                    <input id="settings-business-name" type="text" maxlength="255" autocomplete="organization"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-300">
                    <p id="settings-business-name-error" class="text-xs text-red-500 mt-1 min-h-[1rem]"></p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <textarea id="settings-description" rows="3" maxlength="2000"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none
                                     focus:outline-none focus:ring-2 focus:ring-orange-300"
                              placeholder="Tell customers about your place…"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                        <input id="settings-phone" type="tel" maxlength="30"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300"
                               placeholder="+63 9XX XXX XXXX">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input id="settings-email" type="email" maxlength="255"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300"
                               placeholder="contact@yourbusiness.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                    <input id="settings-address" type="text" maxlength="255"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-300"
                           placeholder="Street address">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
                        <input id="settings-city" type="text" maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300"
                               placeholder="e.g. Tacloban">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Province</label>
                        <input id="settings-province" type="text" maxlength="100"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300"
                               placeholder="e.g. Leyte">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Website</label>
                    <input id="settings-website" type="url" maxlength="255"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-orange-300"
                           placeholder="https://yourbusiness.com">
                </div>

                <p id="settings-form-error" class="text-xs text-red-500 min-h-[1rem]"></p>

                <button id="settings-submit-btn" type="submit"
                        class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-full
                               hover:bg-orange-600 transition disabled:opacity-50">
                    Save Changes
                </button>
            </form>
        </div>

        {{-- ── SID_29: Promotions ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Promotions</h2>
                <button id="promo-add-btn" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 bg-orange-500 text-white
                               text-xs font-medium rounded-full hover:bg-orange-600 transition">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Promo
                </button>
            </div>

            {{-- Inline add / edit form --}}
            <div id="promo-form-panel" class="hidden border-b border-gray-100">
                <form id="promo-form" class="px-5 py-4 space-y-3" novalidate>
                    <input type="hidden" id="promo-edit-id" value="">

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input id="promo-title" type="text" maxlength="255" autocomplete="off"
                               placeholder="e.g. Weekend Buy-One-Get-One"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                        <p id="promo-title-error" class="text-xs text-red-500 mt-1 min-h-[1rem]"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                        <textarea id="promo-description" rows="2" maxlength="1000"
                                  placeholder="Details about the promo (optional)"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none
                                         focus:outline-none focus:ring-2 focus:ring-orange-300"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Discount / Offer</label>
                        <input id="promo-discount" type="text" maxlength="100" autocomplete="off"
                               placeholder="e.g. 20% off all mains"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
                            <input id="promo-start-date" type="date"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">End date</label>
                            <input id="promo-end-date" type="date"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm
                                          focus:outline-none focus:ring-2 focus:ring-orange-300">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="promo-active" type="checkbox" checked
                               class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-300">
                        <span class="text-sm text-gray-700">Active now</span>
                    </label>

                    <p id="promo-form-error" class="text-xs text-red-500 min-h-[1rem]"></p>

                    <div class="flex gap-2 pt-1">
                        <button id="promo-submit-btn" type="submit"
                                class="px-5 py-2 bg-orange-500 text-white text-sm font-medium rounded-full
                                       hover:bg-orange-600 transition disabled:opacity-50">
                            Save Promo
                        </button>
                        <button id="promo-cancel-btn" type="button"
                                class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- Promotions list --}}
            <div id="promotions-container" class="divide-y divide-gray-100">
                <p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading promotions…</p>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/vendor-settings.js') }}"></script>
<script src="{{ asset('js/vendor-promotions.js') }}"></script>

@endsection
