@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50 pb-24">

    {{-- ── HEADER ──────────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-400 pt-12 pb-20 px-4">
        <div class="max-w-3xl mx-auto flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="text-white/70 hover:text-white transition shrink-0" aria-label="Back to admin dashboard">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <div>
                <p class="text-orange-100 text-xs font-medium uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-white text-2xl font-bold">Review Moderation</h1>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_32: Vendor search ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Find a Vendor</h2>
            </div>
            <div class="px-5 py-4">
                <div class="relative">
                    <div class="flex items-center gap-2 border border-gray-200 rounded-xl px-3 py-2
                                focus-within:ring-2 focus-within:ring-orange-300 transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="text-gray-400 shrink-0">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input id="admin-mod-vendor-search"
                               type="text"
                               placeholder="Search vendor by name…"
                               autocomplete="off"
                               class="flex-1 text-sm text-gray-800 placeholder-gray-400
                                      bg-transparent outline-none">
                    </div>

                    {{-- Suggestions dropdown --}}
                    <div id="admin-mod-suggestions"
                         class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200
                                rounded-xl shadow-lg z-20 overflow-hidden divide-y divide-gray-100">
                    </div>
                </div>

                {{-- Selected vendor chip --}}
                <div id="admin-mod-selected-vendor"
                     class="hidden mt-3 flex items-center gap-2 bg-orange-50 border border-orange-100
                            rounded-full px-4 py-2 w-fit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="text-orange-400 shrink-0">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span id="admin-mod-vendor-name" class="text-sm font-medium text-orange-700"></span>
                    <button id="admin-mod-clear-vendor" type="button"
                            class="text-orange-400 hover:text-orange-600 transition ml-1" aria-label="Clear selection">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── SID_32: Reviews list ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Reviews</h2>
            </div>

            <div id="admin-mod-reviews" class="divide-y divide-gray-100">
                <p class="text-sm text-gray-400 py-8 text-center">
                    Search for a vendor above to see their reviews.
                </p>
            </div>

            <div id="admin-mod-load-more-wrap" class="hidden flex justify-center py-4 border-t border-gray-100">
                <button id="admin-mod-load-more"
                        class="px-5 py-2 text-sm text-orange-500 border border-orange-300
                               rounded-full hover:bg-orange-50 transition">
                    Load more
                </button>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/admin-moderation.js') }}"></script>

@endsection
