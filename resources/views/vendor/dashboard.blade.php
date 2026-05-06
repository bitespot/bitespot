@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50 pb-24">

    {{-- ── HEADER ──────────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-400 pt-12 pb-20 px-4">
        <div class="max-w-3xl mx-auto">
            <p class="text-orange-100 text-xs font-medium uppercase tracking-widest mb-1">Vendor Dashboard</p>
            <h1 class="text-white text-2xl font-bold leading-snug">{{ auth()->user()->name }}</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_25: KPI Cards ───────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Overview</h2>
            </div>
            <div id="kpi-container"
                 class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-gray-100">
                <div class="col-span-2 sm:col-span-4 p-6 text-center text-sm text-gray-400 animate-pulse">
                    Loading metrics…
                </div>
            </div>
        </div>

        {{-- ── Quick nav ────────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Manage</h2>
            </div>
            <div class="divide-y divide-gray-100">

                <a href="{{ route('vendor.reviews') }}"
                   class="flex items-center gap-3 px-5 py-4 hover:bg-orange-50 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span class="flex-1 text-sm text-gray-700">Reviews &amp; Replies</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 shrink-0">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>

                <a href="{{ route('vendor.menu') }}"
                   class="flex items-center gap-3 px-5 py-4 hover:bg-orange-50 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0">
                        <path d="M3 5h18M3 12h18M3 19h18"/>
                    </svg>
                    <span class="flex-1 text-sm text-gray-700">Menu</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 shrink-0">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>

                <a href="{{ route('vendor.media') }}"
                   class="flex items-center gap-3 px-5 py-4 hover:bg-orange-50 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="flex-1 text-sm text-gray-700">Photos</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 shrink-0">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>

                <a href="{{ route('vendor.settings') }}"
                   class="flex items-center gap-3 px-5 py-4 hover:bg-orange-50 transition">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06
                                 a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09
                                 A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83
                                 l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09
                                 A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83
                                 l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09
                                 a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83
                                 l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09
                                 a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    <span class="flex-1 text-sm text-gray-700">Settings</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 shrink-0">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>

            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/vendor-dashboard.js') }}"></script>

@endsection
