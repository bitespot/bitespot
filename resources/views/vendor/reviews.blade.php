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
            <h1 class="text-white text-2xl font-bold">Customer Reviews</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4">

        {{-- ── SID_26: Reviews list with inline reply ──────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div id="vendor-reviews-container" class="divide-y divide-gray-100">
                <p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading reviews…</p>
            </div>

            <div id="vendor-reviews-load-more-wrap" class="hidden flex justify-center py-4 border-t border-gray-100">
                <button id="vendor-reviews-load-more"
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
<script src="{{ asset('js/vendor-reviews.js') }}"></script>

@endsection
