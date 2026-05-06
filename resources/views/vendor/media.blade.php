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
            <h1 class="text-white text-2xl font-bold">Photos</h1>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_28: Upload section ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Upload Photo</h2>
            </div>
            <div class="px-5 py-5 space-y-4">

                {{-- File input drop zone --}}
                <label id="photo-drop-zone"
                       class="flex flex-col items-center justify-center gap-2 border-2 border-dashed
                              border-gray-200 rounded-xl p-8 cursor-pointer hover:border-orange-300
                              hover:bg-orange-50 transition group">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                         class="text-gray-300 group-hover:text-orange-400 transition">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                    <span class="text-sm text-gray-400 group-hover:text-orange-500 transition">
                        Click to select a photo
                    </span>
                    <span class="text-xs text-gray-300">JPG, PNG, WebP — max 5 MB</span>
                    <input id="photo-file-input" type="file" accept="image/*" class="hidden">
                </label>

                {{-- Instant preview --}}
                <div id="photo-preview-wrap" class="hidden">
                    <img id="photo-preview"
                         class="w-full max-h-64 object-cover rounded-xl border border-gray-100"
                         alt="Preview">
                    <p id="photo-preview-name" class="text-xs text-gray-400 mt-1 text-center truncate"></p>
                </div>

                <p id="photo-upload-error" class="text-xs text-red-500 min-h-[1rem]"></p>

                <button id="photo-upload-btn" type="button" disabled
                        class="w-full py-2.5 bg-orange-500 text-white text-sm font-medium rounded-full
                               hover:bg-orange-600 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Upload Photo
                </button>
            </div>
        </div>

        {{-- ── SID_28: Photo gallery ───────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">Gallery</h2>
            </div>
            <div class="p-4">
                <div id="photos-container"
                     class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <p class="col-span-full text-sm text-gray-400 py-6 text-center animate-pulse">
                        Loading photos…
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/vendor-media.js') }}"></script>

@endsection
