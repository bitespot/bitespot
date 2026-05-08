@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50 pb-24">

    {{-- ── HERO ──────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-400 pt-12 pb-20 px-4">
        <div class="max-w-lg mx-auto flex flex-col items-center gap-3 text-center">

            @if($user->avatar_url)
                <img id="profile-avatar-hero"
                     src="{{ $user->avatar_url }}"
                     alt="{{ $user->name }}"
                     class="w-20 h-20 rounded-full object-cover border-2 border-white/50 shadow">
            @else
                <div id="profile-avatar-hero"
                     class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center
                            text-white text-3xl font-bold border-2 border-white/50">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif

            <div>
                <h1 id="profile-name-display" class="text-white text-xl font-bold leading-snug">
                    {{ $user->name }}
                </h1>
                <p id="profile-email-display" class="text-orange-100 text-sm mt-0.5">
                    {{ $user->email }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── TABS + PANELS ────────────────────────────────────────────────── --}}
    <div class="max-w-2xl mx-auto -mt-10 bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- Tab bar --}}
        <div class="flex border-b border-gray-100">
            <button class="profile-tab flex-1 py-4 text-sm font-medium
                           text-orange-500 border-b-2 border-orange-500 transition-colors"
                    data-tab="bookmarks">
                Saved Places
            </button>
            <button class="profile-tab flex-1 py-4 text-sm font-medium
                           text-gray-400 border-b-2 border-transparent transition-colors"
                    data-tab="reviews">
                My Reviews
            </button>
            <button class="profile-tab flex-1 py-4 text-sm font-medium
                           text-gray-400 border-b-2 border-transparent transition-colors"
                    data-tab="settings">
                Edit Profile
            </button>
        </div>

        {{-- ── SID_22: Saved Places panel ──────────────────────────────── --}}
        <div id="tab-bookmarks" class="tab-panel p-4 space-y-2">
            <div id="bookmarks-container"></div>
            <div id="bookmarks-load-more-wrap" class="hidden flex justify-center pt-2">
                <button id="bookmarks-load-more"
                        class="px-5 py-2 text-sm text-orange-500 border border-orange-300
                               rounded-full hover:bg-orange-50 transition">
                    Load more
                </button>
            </div>
        </div>

        {{-- ── SID_23: My Reviews panel ─────────────────────────────────── --}}
        <div id="tab-reviews" class="tab-panel hidden p-4 space-y-2">
            <div id="my-reviews-container"></div>
            <div id="my-reviews-load-more-wrap" class="hidden flex justify-center pt-2">
                <button id="my-reviews-load-more"
                        class="px-5 py-2 text-sm text-orange-500 border border-orange-300
                               rounded-full hover:bg-orange-50 transition">
                    Load more
                </button>
            </div>
        </div>

        {{-- ── SID_24: Edit Profile panel ──────────────────────────────── --}}
        <div id="tab-settings" class="tab-panel hidden p-5">
            <form id="profile-form" class="space-y-5" novalidate>

                {{-- Avatar upload --}}
                <div class="flex flex-col items-center gap-3 pb-5 border-b border-gray-100">
                    <div id="avatar-preview"
                         class="w-20 h-20 rounded-full overflow-hidden bg-orange-100
                                flex items-center justify-center text-orange-500 text-2xl font-bold">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <label class="text-sm text-orange-500 font-medium cursor-pointer hover:text-orange-600 transition">
                        Change photo
                        <input type="file" id="avatar-input" accept="image/*" class="hidden">
                    </label>
                </div>

                {{-- Fields --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                        <input id="profile-name-input" type="text" name="name" maxlength="255"
                               value="{{ $user->name }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                        <input id="profile-email-input" type="email" name="email" maxlength="255"
                               value="{{ $user->email }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Location</label>
                        <input id="profile-location-input" type="text" name="location" maxlength="255"
                               value="{{ $user->location ?? '' }}"
                               placeholder="e.g. Tacloban City"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-orange-300">
                    </div>
                </div>

                <p id="profile-form-error" class="text-xs text-red-500 min-h-[1rem]"></p>

                <button type="submit" id="profile-save-btn"
                        class="w-full py-3 bg-orange-500 text-white text-sm font-semibold
                               rounded-xl hover:bg-orange-600 transition disabled:opacity-50">
                    Save Changes
                </button>
            </form>
        </div>

    </div>
</div>

<script>
window.USER_ID   = {{ auth()->id() }};
window.AUTH_NAME = @json($user->name);
</script>
<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/profile.js') }}"></script>

@endsection
