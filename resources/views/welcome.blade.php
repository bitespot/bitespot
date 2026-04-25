<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BiteSpot') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-50 min-h-screen" style="background-color: #f9fafb;">

        {{-- ===== HERO SECTION ===== --}}
        <section class="bs-hero">
            {{-- Navbar inside hero --}}
            <nav class="bs-navbar">
                <div class="bs-navbar__logo-name">
                    <a href="/">
                        <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'BiteSpot') }} logo" class="bs-navbar__logo">
                    </a>
                    <!-- BiteSpot name after the logo -->
                    <span class="bs-navbar__name">BiteSpot</span>
                </div>
                

                <div class="bs-navbar__links">
                    <a href="/explore" class="bs-navbar__link">Explore</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bs-navbar__link">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="bs-navbar__link">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary" style="font-size:.9rem; padding:.5rem 1.1rem;">Sign up</a>
                        @endif
                    @endauth
                </div>
            </nav>

            {{-- Background image --}}
            <img
                src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1920&q=80"
                alt=""
                class="bs-hero__bg"
                aria-hidden="true"
            >

            <div class="bs-hero__content">
                <h1 class="bs-hero__title">
                    Discover your next <span>favorite bite</span>
                </h1>
                <p class="bs-hero__subtitle">
                    Find the best restaurants, cafés, and hidden street food gems in your city.
                </p>

                {{-- Search bar --}}
                <div class="bs-search" id="hero-search-bar">
                    <div class="bs-search__inner">
                        {{-- Search icon --}}
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

                {{-- Autocomplete dropdown --}}
                <div id="search-dropdown"
                     style="display:none; position:absolute; left:50%; transform:translateX(-50%); width:min(40rem, 90vw);
                            margin-top:.25rem; background:#fff; border-radius:.75rem; box-shadow:0 20px 25px -5px rgba(0,0,0,.2);
                            text-align:left; z-index:30; max-height:16rem; overflow-y:auto;">
                </div>
            </div>
        </section>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="bs-main">

            {{-- ── Categories ── --}}
            <section style="margin-bottom: 3rem;">
                <h2 class="bs-section-title">What are you craving?</h2>
                <div class="bs-categories">
                    <a href="/explore?category=restaurants" class="bs-category-btn">
                        <div class="bs-category-icon">
                            {{-- Utensils --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3zm0 0v7"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Restaurants</span>
                    </a>

                    <a href="/explore?category=street-food" class="bs-category-btn">
                        <div class="bs-category-icon">
                            {{-- Pizza --}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2 2 22h20L12 2z"/><path d="M12 14a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                            </svg>
                        </div>
                        <span class="bs-category-label">Street Food</span>
                    </a>

                    <a href="/explore?category=cafes" class="bs-category-btn">
                        <div class="bs-category-icon">
                            {{-- Coffee --}}
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
                            {{-- Ice cream --}}
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
                            {{-- Beer --}}
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

                {{-- Cards render here via JS; skeletons shown while loading --}}
                <div id="trending-container" class="bs-cards-grid">
                    @foreach (range(1, 6) as $i)
                    <div style="background:#fff; border-radius:1rem; overflow:hidden; border:1px solid #f3f4f6;">
                        <div class="bs-skeleton" style="height:12rem;"></div>
                        <div style="padding:1.25rem;">
                            <div class="bs-skeleton" style="height:1.1rem; width:60%; margin-bottom:.6rem;"></div>
                            <div class="bs-skeleton" style="height:.875rem; width:90%; margin-bottom:.4rem;"></div>
                            <div class="bs-skeleton" style="height:.875rem; width:70%;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

        </div>{{-- /.bs-main --}}

        <script>
        // ── Search: redirect to /explore with query param ──
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

        {{-- Existing home.js handles fetching & rendering the trending cards --}}
        <script src="{{ asset('js/home.js') }}"></script>
    </body>
</html>