<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'BiteSpot') }} — Discover Your Place's Best Eats</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-50 min-h-screen">

        {{-- ===================================================
             NAVBAR — transparent over hero
             =================================================== --}}
        <nav class="bs-navbar">
            <div class="bs-navbar__logo-name">
                <a href="/">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'BiteSpot') }} logo" class="bs-navbar__logo">
                </a>
                <span class="bs-navbar__name">BiteSpot</span>
            </div>

            <div class="bs-navbar__links">
                <a href="/explore" class="bs-navbar__link">Explore</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary" style="font-size:.9rem; padding:.5rem 1.1rem;">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bs-navbar__link">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary" style="font-size:.9rem; padding:.5rem 1.1rem;">
                            Sign up free
                        </a>
                    @endif
                @endauth
            </div>
        </nav>

        {{-- ===================================================
             HERO
             =================================================== --}}
        <section class="bs-hero">
            <img
                src="/images/dashboard/jepoysgrillandresto.jpg"
                alt=""
                class="bs-hero__bg"
                aria-hidden="true"
            >

            <div class="bs-hero__content">
                <h1 class="bs-hero__title">
                    Discover your next <span>favorite bite</span>
                </h1>
                <p class="bs-hero__subtitle">
                    Your place's hyperlocal food directory — from hidden street food stalls
                    to neighbourhood eateries you won't find anywhere else.
                </p>

                <div class="bs-search" id="hero-search-bar">
                    <div class="bs-search__inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            id="hero-search-input"
                            type="text"
                            placeholder="Find food near you..."
                            class="bs-search__input"
                            autocomplete="off"
                        >
                    </div>
                    <a href="/explore" class="bs-search__btn" id="hero-search-btn">Search</a>
                </div>

                <div id="search-dropdown" class="bs-search-dropdown"></div>
            </div>
        </section>

        {{-- ===================================================
             WHAT WE ARE — Three-column value propositions
             =================================================== --}}
        <section class="bs-landing-section">
            <div class="bs-container">
                <p class="bs-landing-eyebrow">What is BiteSpot?</p>
                <h2 class="bs-landing-heading">Your place's food scene, finally online.</h2>
                <p class="bs-landing-subheading">
                    BiteSpot bridges the digital divide for micro-food businesses that can't afford
                    a spot on big platforms — and gives locals and tourists one place to find them all.
                </p>

                <div class="bs-value-grid">
                    <div class="bs-value-card">
                        <div class="bs-value-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title">Discover Hidden Gems</h3>
                        <p class="bs-value-desc">
                            Browse a searchable, map-based directory of restaurants, street food stalls,
                            cafés, and night market vendors — all local, all verified.
                        </p>
                    </div>

                    <div class="bs-value-card">
                        <div class="bs-value-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title">Community Reviews</h3>
                        <p class="bs-value-desc">
                            Real ratings and reviews from locals. Find what's trending,
                            what's a hidden gem, and what's worth the queue.
                        </p>
                    </div>

                    <div class="bs-value-card">
                        <div class="bs-value-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title">Visibility for Vendors</h3>
                        <p class="bs-value-desc">
                            Own a food stall or eatery? Claim your listing for free. Toggle open/closed
                            status, manage your menu, and get discovered — no marketing team needed.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================================================
             CATEGORY BROWSE
             =================================================== --}}
        <section class="bs-landing-section bs-landing-section--alt">
            <div class="bs-container">
                <p class="bs-landing-eyebrow">Browse by category</p>
                <h2 class="bs-landing-heading">What are you craving?</h2>

                <div class="bs-categories">
                    <a href="/explore?category=restaurants" class="bs-category-btn">
                        <div class="bs-category-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/>
                                <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Restaurants</span>
                    </a>
                    <a href="/explore?category=street-food" class="bs-category-btn">
                        <div class="bs-category-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2 2 22h20L12 2z"/><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Street Food</span>
                    </a>
                    <a href="/explore?category=cafes" class="bs-category-btn">
                        <div class="bs-category-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/>
                                <line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Cafés</span>
                    </a>
                    <a href="/explore?category=desserts" class="bs-category-btn">
                        <div class="bs-category-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22l3-3-1.5-1.5L12 19l-1.5-1.5L9 19l3 3z"/>
                                <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17h8v-2.26C17.81 13.47 19 11.38 19 9a7 7 0 0 0-7-7z"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Desserts</span>
                    </a>
                    <a href="/explore?category=drinks" class="bs-category-btn">
                        <div class="bs-category-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 11h1a3 3 0 0 1 0 6h-1"/><path d="M5 11h12v10H5z"/>
                                <path d="M5 7h14"/><path d="M7 7V5"/><path d="M11 7V5"/><path d="M15 7V5"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Drinks</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- ===================================================
             WHO IS IT FOR
             =================================================== --}}
        <section class="bs-landing-section">
            <div class="bs-container">
                <p class="bs-landing-eyebrow">Built for everyone</p>
                <h2 class="bs-landing-heading">Who uses BiteSpot?</h2>

                <div class="bs-who-grid">
                    <div class="bs-who-card">
                        <span class="bs-who-emoji">🍽️</span>
                        <h3 class="bs-who-title">Everyday Diners</h3>
                        <p class="bs-who-desc">
                            Find a nearby place to eat in seconds. Filter by category,
                            check ratings, see operating hours, and read reviews from real locals.
                        </p>
                    </div>
                    <div class="bs-who-card">
                        <span class="bs-who-emoji">🗺️</span>
                        <h3 class="bs-who-title">Tourists & Visitors</h3>
                        <p class="bs-who-desc">
                            Explore curated food trails and cultural spots unique to your location.
                            Navigate the area's food scene like a local from day one.
                        </p>
                    </div>
                    <div class="bs-who-card">
                        <span class="bs-who-emoji">🏪</span>
                        <h3 class="bs-who-title">Food Vendors</h3>
                        <p class="bs-who-desc">
                            Claim your free listing, manage your menu, set your hours, and
                            get found by customers — without needing a marketing budget.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===================================================
             CTA STRIP
             =================================================== --}}
        <section class="bs-cta-strip">
            <div class="bs-container bs-cta-strip__inner">
                <div>
                    <h2 class="bs-cta-strip__title">Ready to explore?</h2>
                    <p class="bs-cta-strip__sub">Join BiteSpot and never wonder where to eat again.</p>
                </div>
                <div class="bs-cta-strip__actions">
                    <a href="/explore" class="bs-cta-strip__btn bs-cta-strip__btn--ghost">Browse spots</a>
                    @guest
                        <a href="{{ route('register') }}" class="bs-cta-strip__btn bs-cta-strip__btn--primary">
                            Create free account
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        {{-- ===================================================
             FOOTER
             =================================================== --}}
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
                    <a href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Sign up</a>
                    @endif
                </div>
            </div>
        </footer>

        <script>
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
    </body>
</html>