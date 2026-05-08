@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50">

    {{-- ── HERO ─────────────────────────────────────────────────────────────── --}}
    <div class="relative bg-gray-900 h-64 sm:h-80 overflow-hidden">

        @if($vendor->primary_photo)
            <img src="{{ $vendor->primary_photo }}" alt="{{ $vendor->business_name }}"
                 class="absolute inset-0 w-full h-full object-cover opacity-55">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-orange-300 opacity-80"></div>
        @endif

        <div class="absolute inset-0 flex flex-col justify-between p-4 sm:p-6">

            {{-- Back + Share + Bookmark --}}
            <div class="flex items-center justify-between">
                <a href="javascript:history.back()"
                   class="flex items-center justify-center w-9 h-9 rounded-full bg-black/30 text-white hover:bg-black/50 transition backdrop-blur-sm"
                   aria-label="Go back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </a>

                <div class="flex items-center gap-2">
                    {{-- SID_16: Share button --}}
                    <button id="share-btn"
                            aria-label="Share this BiteSpot"
                            class="flex items-center justify-center w-9 h-9 rounded-full bg-black/30 text-white hover:bg-black/50 transition backdrop-blur-sm">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
                            <polyline points="16 6 12 2 8 6"/>
                            <line x1="12" y1="2" x2="12" y2="15"/>
                        </svg>
                    </button>

                    <button id="bookmark-btn"
                            aria-label="Save this BiteSpot"
                            aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                            data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full bg-black/30 text-white hover:bg-black/50 transition backdrop-blur-sm">
                        <svg data-bookmark-icon width="18" height="18" viewBox="0 0 24 24"
                             fill="{{ $isBookmarked ? '#f97316' : 'none' }}"
                             stroke="{{ $isBookmarked ? '#f97316' : 'currentColor' }}"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Vendor name + meta --}}
            <div>
                <p class="text-orange-300 text-sm font-medium mb-1">
                    {{ $vendor->category?->name ?? '' }}
                    @if($vendor->price_tier) &middot; {{ $vendor->price_tier_label }} @endif
                </p>
                <h1 class="text-white text-2xl sm:text-3xl font-bold leading-tight">
                    {{ $vendor->business_name }}
                </h1>
                @if($vendor->avg_rating)
                <div class="flex items-center gap-1.5 mt-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#FBBF24">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span class="text-white text-sm font-semibold">{{ number_format($vendor->avg_rating, 1) }}</span>
                    @if($vendor->review_count)
                        <span class="text-gray-300 text-xs">
                            ({{ $vendor->review_count }} {{ $vendor->review_count === 1 ? 'review' : 'reviews' }})
                        </span>
                    @endif
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ── CONTENT ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-5xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: About + Menu --}}
            <div class="lg:col-span-2 space-y-5">

                @if($vendor->description)
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">About</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $vendor->description }}</p>
                </div>
                @endif

                {{-- SID_13: Menu highlights — rendered by place.js --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Menu</h2>
                    <div id="menu-container"></div>
                </div>

                {{-- SID_17: Submit review form (authenticated users only) --}}
                @auth
                <div id="review-form-card" class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-4">Leave a Review</h2>
                    <form id="review-form" novalidate>
                        <div class="mb-3">
                            <div id="star-picker" class="flex gap-1 cursor-pointer" role="group" aria-label="Star rating">
                                @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-star="{{ $i }}"
                                        style="color:#d1d5db;font-size:1.75rem;line-height:1"
                                        aria-label="{{ $i }} star{{ $i > 1 ? 's' : '' }}">&#9733;</button>
                                @endfor
                            </div>
                            <input type="hidden" id="review-rating" value="0">
                        </div>
                        <textarea id="review-body" rows="3" maxlength="1000"
                                  class="w-full border border-gray-200 rounded-lg p-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-orange-300"
                                  placeholder="Share your experience (optional)…"></textarea>
                        <p id="review-form-error" class="text-xs text-red-500 mt-1 min-h-[1rem]"></p>
                        <button id="review-submit-btn" type="submit"
                                class="mt-3 px-5 py-2 bg-orange-500 text-white text-sm font-medium rounded-full hover:bg-orange-600 transition disabled:opacity-50">
                            Submit Review
                        </button>
                    </form>
                </div>
                @endauth

                {{-- SID_19: Reviews list — rendered by place.js --}}
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Reviews</h2>
                        <span id="reviews-total" class="text-xs text-gray-400"></span>
                    </div>
                    <div id="reviews-container"></div>
                    <button id="load-more-btn" hidden
                            class="mt-4 w-full py-2 text-sm text-orange-600 border border-orange-200 rounded-lg hover:bg-orange-50 transition">
                        Load more reviews
                    </button>
                </div>

            </div>

            {{-- RIGHT: Info sidebar --}}
            <div class="space-y-4">
                <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Info</h2>

                    @if($vendor->address)
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="mt-0.5 shrink-0 text-orange-400">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>{{ $vendor->address }}{{ $vendor->city ? ', ' . $vendor->city : '' }}</span>
                    </div>
                    @endif

                    @if($vendor->phone)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="shrink-0 text-orange-400">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.9 14.26a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.82 3.5h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 11.1a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17.92z"/>
                        </svg>
                        <a href="tel:{{ $vendor->phone }}" class="hover:text-orange-500 transition">{{ $vendor->phone }}</a>
                    </div>
                    @endif

                    @if($vendor->website)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="shrink-0 text-orange-400">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <a href="{{ $vendor->website }}" target="_blank" rel="noopener"
                           class="hover:text-orange-500 transition truncate">{{ $vendor->website }}</a>
                    </div>
                    @endif

                    @if($vendor->hours && count($vendor->hours))
                    <div class="flex items-start gap-2 text-sm text-gray-600">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="mt-0.5 shrink-0 text-orange-400">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <div class="space-y-0.5">
                            @foreach($vendor->hours as $day => $time)
                                <div><span class="capitalize font-medium text-gray-700">{{ $day }}</span>: {{ $time }}</div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>

                @auth
                    @if(auth()->id() !== $vendor->user_id && !auth()->user()->isVendor())
                    <button onclick="document.getElementById('claim-modal').classList.remove('hidden')"
                            class="block w-full px-4 py-3 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition text-center">
                        Claim Ownership
                    </button>

                    {{-- Claim confirmation modal --}}
                    <div id="claim-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm mx-4 w-full">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Claim Ownership</h3>
                            <p class="text-sm text-gray-600 mb-6">
                                Are you sure you want to claim ownership of <strong>{{ $vendor->name }}</strong>?
                                You will immediately become the owner of this establishment.
                            </p>
                            <div class="flex gap-3">
                                <button onclick="document.getElementById('claim-modal').classList.add('hidden')"
                                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                    Cancel
                                </button>
                                <form method="POST" action="{{ route('place.claim.submit', $vendor->slug) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition">
                                        Yes, Claim It
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                @else
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                    <p class="text-sm text-orange-700 mb-3">Sign in to save this BiteSpot.</p>
                    <a href="/login"
                       class="inline-block px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-full hover:bg-orange-600 transition">
                        Sign In
                    </a>
                </div>
                @endauth

                @guest
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-center">
                    <p class="text-sm text-orange-700 mb-3">Sign in to save this BiteSpot or claim ownership.</p>
                    <a href="/login"
                       class="inline-block px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-full hover:bg-orange-600 transition">
                        Sign In
                    </a>
                </div>
                @endguest
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
window.VENDOR_ID     = {{ $vendor->id }};
window.IS_BOOKMARKED = {{ $isBookmarked ? 'true' : 'false' }};
window.IS_AUTH       = {{ auth()->check() ? 'true' : 'false' }};
window.USER_ID       = {{ auth()->check() ? auth()->id() : 'null' }};
window.AUTH_NAME     = @json(auth()->check() ? auth()->user()->name : null);
</script>
<script src="{{ asset('js/place.js') }}"></script>
@endpush

@endsection
