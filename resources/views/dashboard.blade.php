<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Home — {{ config('app.name', 'BiteSpot') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-50 min-h-screen">

        {{-- NAVBAR --}}
        @include('components.navbar')

        {{-- ===================================================
             HERO — personalised greeting + search
             =================================================== --}}
        <section class="bs-hero">
            <img
                src="/images/dashboard/jepoysgrillandresto.jpg"
                alt=""
                class="bs-hero__bg"
                aria-hidden="true"
            >

            <div class="bs-hero__content">
                <p class="bs-hero__greeting">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                    {{ auth()->user()->name }}
                </p>
                <h1 class="bs-hero__title">
                    Discover your next <span>favorite bite</span>
                </h1>
                <p class="bs-hero__subtitle">
                    Find the best restaurants, cafés, and hidden street food gems in your city.
                </p>

                <div class="bs-search" id="hero-search-bar">
                    <div class="bs-search__inner flex-1 min-w-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            id="hero-search-input"
                            type="text"
                            placeholder="Find food near you..."
                            class="bs-search__input w-full min-w-0"
                            autocomplete="off"
                        >
                    </div>
                    <a href="/explore" class="bs-search__btn w-full sm:w-auto mt-2 sm:mt-0" id="hero-search-btn">Search</a>
                </div>

                <div id="search-dropdown" class="bs-search-dropdown"></div>
            </div>
        </section>

        {{-- ===================================================
             MAIN CONTENT
             =================================================== --}}
        <div class="bs-main bs-container">

            {{-- ── Categories ── --}}
            <section class="bs-categories-section">
                <h2 class="bs-section-title">What are you craving?</h2>
                <div class="bs-categories flex flex-wrap gap-3 mt-4">
                    <a href="/explore?category=restaurants" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <div class="bs-category-icon mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/>
                                <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Restaurants</span>
                    </a>
                    <a href="/explore?category=street-food" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <div class="bs-category-icon mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2 2 22h20L12 2z"/><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Street Food</span>
                    </a>
                    <a href="/explore?category=cafes" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <div class="bs-category-icon mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/>
                                <line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Cafés</span>
                    </a>
                    <a href="/explore?category=desserts" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <div class="bs-category-icon mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22l3-3-1.5-1.5L12 19l-1.5-1.5L9 19l3 3z"/>
                                <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17h8v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Desserts</span>
                    </a>
                    <a href="/explore?category=drinks" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <div class="bs-category-icon mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 11h1a3 3 0 0 1 0 6h-1"/><path d="M5 11h12v10H5z"/>
                                <path d="M5 7h14"/><path d="M7 7V5"/><path d="M11 7V5"/><path d="M15 7V5"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Drinks</span>
                    </a>
                </div>
            </section>

            {{-- ── Trending Spots ── --}}
            <section id="trending-spots">
                <div class="bs-section-header">
                    <h2 class="bs-section-title" style="margin-bottom:0;">Trending Spots</h2>
                    <a href="/explore" class="bs-see-all">
                        See all
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>

                {{-- Skeleton placeholders while JS loads real cards --}}
                <div id="trending-container" class="bs-cards-grid">
                    @foreach (range(1, 6) as $i)
                        <div class="bs-skeleton-card">
                            <div class="bs-skeleton bs-skeleton-card__img"></div>
                            <div class="bs-skeleton-card__body">
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:60%;"></div>
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:90%;"></div>
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:70%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ── Hidden Gems ── --}}
            <section id="hidden-gems" style="margin-top: 3.5rem;">
                <div class="bs-section-header">
                    <h2 class="bs-section-title" style="margin-bottom:0;">
                        Hidden Gems
                    </h2>
                    <a href="/explore?sort=hidden-gems" class="bs-see-all">
                        See all
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>

                <div id="gems-container" class="bs-cards-grid">
                    @foreach (range(1, 3) as $i)
                        <div class="bs-skeleton-card">
                            <div class="bs-skeleton bs-skeleton-card__img"></div>
                            <div class="bs-skeleton-card__body">
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:55%;"></div>
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:85%;"></div>
                                <div class="bs-skeleton bs-skeleton-card__line" style="width:65%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>{{-- /.bs-main --}}

{{--
        <footer class="bs-footer">
            <div class="bs-container bs-footer__inner">
                <div class="bs-footer__brand">
                    <img src="{{ asset('logo.png') }}" alt="BiteSpot" class="bs-footer__logo">
                    <span class="bs-footer__name">BiteSpot</span>
                </div>
                <p class="bs-footer__copy">
                    &copy; {{ date('Y') }} BiteSpot. All rights reserved.
                </p>
                <div class="bs-footer__links">
                    <a href="/explore">Explore</a>
                    <a href="/profile">Profile</a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:none; border:none; cursor:pointer; color:inherit; font:inherit; padding:0;">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </footer>
--}}
        <script>
        // ── Search redirect ──
        document.getElementById('hero-search-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const q = this.value.trim();
                window.location.href = '/explore' + (q ? '?q=' + encodeURIComponent(q) : '');
            }
        });
        document.getElementById('hero-search-btn').addEventListener('click', function(e) {
            e.preventDefault();
            const q = document.getElementById('hero-search-input').value.trim();
            window.location.href = '/explore' + (q ? '?q=' + encodeURIComponent(q) : '');
        });
        </script>

        {{-- home.js fetches and renders trending + hidden gems cards into their containers --}}
        <script src="{{ asset('js/home.js') }}"></script>

        {{-- Floating Add BiteSpot Button (only for logged-in users) --}}
        @auth
            @include('components.add-bitespot')
        @endauth
    </body>
</html>