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
        <nav class="bs-navbar bs-navbar--solid">
            <div class="bs-navbar__logo-name">
                <a href="/">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'BiteSpot') }} logo" class="bs-navbar__logo">
                </a>
                <span class="bs-navbar__name">BiteSpot</span>
            </div>

            <div class="bs-navbar__links bs-navbar__links--left" style="flex:1; justify-content:flex-start;">
                <form id="navbar-search-form" action="/explore" method="GET" style="display:flex; align-items:center; gap:0.5rem;">
                    <div style="position:relative; display:flex; align-items:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); color:#888;">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input 
                            type="text" 
                            name="q" 
                            id="navbar-search-input" 
                            placeholder="Search food..." 
                            style="padding:0.5rem 0.75rem 0.5rem 2.2rem; border-radius:1.5rem; border:1.5px solid #ddd; background:#fff; min-width:220px; font-size:1rem; outline:none; transition:border 0.2s;"
                            autocomplete="off"
                        >
                    </div>
                </form>
                <!-- User menu follows -->
                @auth
            </div>
            <div class="bs-user-menu" id="user-menu-wrap">
                <button class="bs-user-menu__trigger" id="user-menu-btn" aria-expanded="false">
                    <span class="bs-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="bs-user-menu__name">{{ auth()->user()->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="bs-user-menu__dropdown" id="user-menu-dropdown">
                    <a href="/profile" class="bs-user-menu__item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        My Profile
                    </a>
                    <a href="/my-reviews" class="bs-user-menu__item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        My Reviews
                    </a>
                    <div class="bs-user-menu__divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bs-user-menu__item bs-user-menu__item--danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
            @else
            </div>
            <div class="bs-navbar__links bs-navbar__links--right">
                <a href="{{ route('login') }}" class="btn-primary" style="font-size:.9rem; padding:.5rem 1.1rem; background:#fff; color:#222; border:1.5px solid #222; box-shadow:none;">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary" style="font-size:.9rem; padding:.5rem 1.1rem;">
                        Sign up free
                    </a>
                @endif
            </div>
            @endauth
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

                <div class="bs-search w-full max-w-xl mx-auto flex flex-col sm:flex-row gap-2 sm:gap-0" id="hero-search-bar">
                    <div class="bs-search__inner flex-1 min-w-0 px-2 py-1 sm:px-0 sm:py-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            id="hero-search-input"
                            type="text"
                            placeholder="Find food near you..."
                            class="bs-search__input w-full min-w-0 text-base sm:text-lg py-2 sm:py-3"
                            autocomplete="off"
                        >
                    </div>
                    <a href="/explore" class="bs-search__btn w-full sm:w-auto mt-2 sm:mt-0 text-base sm:text-lg py-2 sm:py-3 px-4 sm:px-6 rounded-full sm:rounded-full" id="hero-search-btn">Search</a>
                </div>

                <div id="search-dropdown" class="bs-search-dropdown"></div>

                <!-- Mobile-only quick links example -->
                <div class="flex sm:hidden mt-6 gap-2 justify-center">
                    <a href="/explore?category=restaurants" class="px-3 py-2 rounded-full bg-orange-100 text-orange-700 text-sm font-medium">Restaurants</a>
                    <a href="/explore?category=street-food" class="px-3 py-2 rounded-full bg-orange-100 text-orange-700 text-sm font-medium">Street Food</a>
                    <a href="/explore?category=cafes" class="px-3 py-2 rounded-full bg-orange-100 text-orange-700 text-sm font-medium">Cafés</a>
                </div>
                <!-- End mobile-only quick links -->
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

                <div class="bs-value-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                    <div class="bs-value-card flex flex-col items-center text-center p-6 bg-white rounded-xl shadow">
                        <div class="bs-value-icon mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title font-semibold text-lg">Discover Hidden Gems</h3>
                        <p class="bs-value-desc mt-2 text-gray-600">
                            Browse a searchable, map-based directory of restaurants, street food stalls,
                            cafés, and night market vendors — all local, all verified.
                        </p>
                    </div>

                    <div class="bs-value-card flex flex-col items-center text-center p-6 bg-white rounded-xl shadow">
                        <div class="bs-value-icon mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title font-semibold text-lg">Community Reviews</h3>
                        <p class="bs-value-desc mt-2 text-gray-600">
                            Real ratings and reviews from locals. Find what's trending,
                            what's a hidden gem, and what's worth the queue.
                        </p>
                    </div>

                    <div class="bs-value-card flex flex-col items-center text-center p-6 bg-white rounded-xl shadow">
                        <div class="bs-value-icon mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                        <h3 class="bs-value-title font-semibold text-lg">Visibility for Vendors</h3>
                        <p class="bs-value-desc mt-2 text-gray-600">
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
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/dashboard/who_uses_bitespot/diners.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <h3 class="bs-who-title">Everyday Diners</h3>
                        <p class="bs-who-desc">
                            Find a nearby place to eat in seconds. Filter by category,
                            check ratings, see operating hours, and read reviews from real locals.
                        </p>
                    </div>
                    <div class="bs-who-card">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/dashboard/who_uses_bitespot/tourists.png" alt="Tourists & Visitors" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <h3 class="bs-who-title">Tourists & Visitors</h3>
                        <p class="bs-who-desc">
                            Explore curated food trails and cultural spots unique to your location.
                            Navigate the area's food scene like a local from day one.
                        </p>
                    </div>
                    <div class="bs-who-card">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/dashboard/who_uses_bitespot/vendors.png" alt="Food Vendors" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
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

        <style>
        /* Navbar search bar left-aligned, simple rectangle */
        .bs-navbar__links--left {
            display: flex;
            margin-left: 1rem;
            align-items: center;
            justify-content: flex-start;
            gap: 0.5rem;
            flex: 1;
        }
        </style>

        <script>
        // No underline indicator logic needed
        // Navbar search bar submit on Enter
        document.getElementById('navbar-search-form').addEventListener('submit', function(e) {
            const input = document.getElementById('navbar-search-input');
            if (input.value.trim() === '') {
                e.preventDefault(); // Prevent empty search
            }
        });
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