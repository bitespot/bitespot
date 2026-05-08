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
            {{-- Slideshow background --}}
            <div class="bs-hero__slideshow" aria-hidden="true">
                {{-- Dark overlay for text legibility --}}
                <div class="bs-hero__overlay" aria-hidden="true"></div>
                @foreach($slideFiles as $i => $slide)
                    <div
                        class="bs-hero__slide{{ $i === 0 ? ' bs-hero__slide--active' : '' }}"
                        style="background-image: url('{{ $slide }}');"
                    ></div>
                @endforeach
            </div>

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

                <div class="bs-dashboard-search" id="hero-search-bar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                         fill="none" stroke="#bbb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="flex-shrink:0;">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        id="hero-search-input"
                        type="text"
                        placeholder="Find food near you... press Enter to search"
                        autocomplete="off"
                        style="border:none;outline:none;background:transparent;font-size:1rem;width:100%;min-width:0;color:#222;"
                    >
                </div>

                <div id="search-dropdown" class="bs-search-dropdown"></div>
            </div>
        </section>

        {{-- ===================================================
             MAIN CONTENT
             =================================================== --}}
        <div class="bs-main bs-container" style="max-width:1200px; margin-left:auto; margin-right:auto; padding-left:1.5rem; padding-right:1.5rem;">

            {{-- ── Categories ── --}}
            <section class="bs-categories-section">
                <h2 class="bs-section-title">What are you craving?</h2>
                <div class="bs-categories flex flex-wrap gap-3 mt-4">
                    <a href="/explore?category=restaurants" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/categories/restaurants.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <span class="bs-category-label">Restaurants</span>
                    </a>
                    <a href="/explore?category=street-food" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/categories/street_foods.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <span class="bs-category-label">Street Food</span>
                    </a>
                    <a href="/explore?category=cafes" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/categories/cafes.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <span class="bs-category-label">Cafés</span>
                    </a>
                    <a href="/explore?category=desserts" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/categories/desserts.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <span class="bs-category-label">Desserts</span>
                    </a>
                    <a href="/explore?category=drinks" class="bs-category-btn flex-1 min-w-[120px] max-w-[180px] flex flex-col items-center">
                        <span class="bs-who-emoji" style="display: flex; align-items: center; justify-content: center; height: 2.8em;">
                            <img src="/images/categories/drinks.png" alt="Everyday Diners" style="width: 2.2em; height: 2.2em; object-fit: contain; display: block;" loading="lazy">
                        </span>
                        <span class="bs-category-label">Drinks</span>
                    </a>
                </div>
            </section>

            {{-- ── Promotions Carousel (Agoda-style) ── --}}
            {{--}
            <section class="bs-promo-carousel-section">
                <div class="bs-section-header">
                    <h2 class="bs-section-title" style="margin-bottom:0;">Deals &amp; Promotions</h2>
                    <a href="/deals" class="bs-see-all">
                        View all
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </div>

                <div class="bs-promo-carousel" id="bs-promo-carousel">
                   
                    <button class="bs-promo-arrow bs-promo-arrow--left" id="bs-promo-prev" aria-label="Previous promotions">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>

               
                    <div class="bs-promo-track" id="bs-promo-track">
                   
                        @foreach (range(1, 5) as $i)
                            <div class="bs-promo-skeleton">
                                <div class="bs-skeleton" style="width:100%; height:100%; border-radius:14px;"></div>
                            </div>
                        @endforeach
                    </div>

               
                    <button class="bs-promo-arrow bs-promo-arrow--right" id="bs-promo-next" aria-label="Next promotions">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
            </section>
            --}}

            {{-- ── Trending Spots ── --}}
            <section id="trending-spots" class="bs-spot-carousel-section">
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

                <div class="bs-promo-carousel" id="bs-trending-carousel">
                    <button class="bs-promo-arrow bs-promo-arrow--left" id="bs-trending-prev" aria-label="Previous trending spots">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>

                    <div class="bs-promo-track" id="trending-container">
                        @foreach (range(1, 6) as $i)
                            <div class="bs-spot-skeleton">
                                <div class="bs-skeleton" style="width:100%;height:160px;border-radius:12px 12px 0 0;"></div>
                                <div style="padding:0.75rem;">
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:60%;"></div>
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:85%;"></div>
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:50%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="bs-promo-arrow bs-promo-arrow--right" id="bs-trending-next" aria-label="Next trending spots">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
            </section>

            {{-- ── Hidden Gems ── --}}
            <section id="hidden-gems" class="bs-spot-carousel-section">
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

                <div class="bs-promo-carousel" id="bs-gems-carousel">
                    <button class="bs-promo-arrow bs-promo-arrow--left" id="bs-gems-prev" aria-label="Previous hidden gems">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </button>

                    <div class="bs-promo-track" id="gems-container">
                        @foreach (range(1, 4) as $i)
                            <div class="bs-spot-skeleton">
                                <div class="bs-skeleton" style="width:100%;height:160px;border-radius:12px 12px 0 0;"></div>
                                <div style="padding:0.75rem;">
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:55%;"></div>
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:80%;"></div>
                                    <div class="bs-skeleton bs-skeleton-card__line" style="width:45%;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="bs-promo-arrow bs-promo-arrow--right" id="bs-gems-next" aria-label="Next hidden gems">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
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
        // ── Search redirect (Enter key only — no button) ──
        document.getElementById('hero-search-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const q = this.value.trim();
                window.location.href = '/explore' + (q ? '?q=' + encodeURIComponent(q) : '');
            }
        });
        </script>

        {{-- home.js fetches and renders trending + hidden gems cards into their containers --}}
        <script src="{{ asset('js/home.js') }}"></script>

        {{-- Floating Add BiteSpot Button (only for logged-in users) --}}
        @auth
            @include('components.add-bitespot')
        @endauth
        <style>
        /* ── Hero Slideshow ─────────────────────────────────────── */
        .bs-hero__slideshow {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        .bs-hero__slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
            will-change: opacity;
        }
        .bs-hero__slide--active { opacity: 1; }
        .bs-hero__overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background: rgba(0,0,0,0.35);
            pointer-events: none;
        }
        .bs-hero__content { position: relative; z-index: 2; }

        /* ── Main content centering ──────────────────────────────── */
        .bs-main.bs-container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── Smaller spot cards ──────────────────────────────────── */
        .bs-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.1rem;
        }
        .bs-skeleton-card {
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
        }
        .bs-skeleton-card__img  { height: 140px; }
        .bs-skeleton-card__body { padding: 0.75rem; }
        .bs-skeleton-card__line { height: 11px; border-radius: 6px; margin-bottom: 8px; }

        /* ── Promotions carousel ─────────────────────────────────── */
        .bs-promo-carousel-section { margin-top: 2.5rem; margin-bottom: 0.5rem; }

        .bs-promo-carousel {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0;
            margin-top: 1rem;
        }

        .bs-promo-track {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;          /* Firefox */
            padding: 8px 4px 12px;
            flex: 1;
            scroll-snap-type: x mandatory;
        }
        .bs-promo-track::-webkit-scrollbar { display: none; }

        /* Real promo card */
        .bs-promo-card {
            flex: 0 0 280px;
            height: 155px;
            border-radius: 14px;
            overflow: hidden;
            scroll-snap-align: start;
            box-shadow: 0 2px 10px rgba(0,0,0,0.10);
            transition: transform 0.18s, box-shadow 0.18s;
            display: block;
            text-decoration: none;
        }
        .bs-promo-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .bs-promo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Skeleton promo card */
        .bs-promo-skeleton {
            flex: 0 0 280px;
            height: 155px;
            border-radius: 14px;
            overflow: hidden;
            scroll-snap-align: start;
            background: #e8e8e8;
        }

        /* Arrow buttons */
        .bs-promo-arrow {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            transition: background 0.15s, box-shadow 0.15s, opacity 0.2s;
            position: absolute;
            top: 50%;
            transform: translateY(-60%);   /* account for bottom padding */
            z-index: 2;
        }
        .bs-promo-arrow:hover { background: #f5f5f5; box-shadow: 0 4px 14px rgba(0,0,0,0.18); }
        .bs-promo-arrow--left  { left: -18px; }
        .bs-promo-arrow--right { right: -18px; }
        .bs-promo-arrow[disabled] { opacity: 0; pointer-events: none; }

        @media (max-width: 640px) {
            .bs-promo-arrow { display: none; }   /* touch-scroll on mobile, no arrows needed */
            .bs-promo-card, .bs-promo-skeleton { flex: 0 0 240px; height: 133px; }
        }

        /* ── Spot carousels (Trending + Hidden Gems) ─────────────── */
        .bs-spot-carousel-section { margin-top: 2.5rem; }

        /* The spot card inside a carousel track */
        .bs-card {
            flex: 0 0 220px;
            scroll-snap-align: start;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 8px rgba(0,0,0,0.08);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            transition: transform 0.18s, box-shadow 0.18s;
        }
        .bs-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.13);
        }
        .bs-card__img-wrap { width: 100%; height: 160px; overflow: hidden; flex-shrink: 0; }
        .bs-card__img      { width: 100%; height: 100%; object-fit: cover; display: block; }
        .bs-card__img--placeholder {
            width: 100%; height: 100%;
            background: linear-gradient(135deg,#ffe0cc,#ffd6b0);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
        }
        .bs-card__body   { padding: 0.75rem; display: flex; flex-direction: column; gap: 2px; flex: 1; }
        .bs-card__name   { font-size: 0.88rem; font-weight: 600; color: #1a1a1a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bs-card__meta   { font-size: 0.75rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bs-card__city   { font-size: 0.73rem; color: #aaa; }
        .bs-card__rating { display: flex; align-items: center; gap: 3px; font-size: 0.78rem; color: #555; margin-top: 4px; }

        /* Skeleton spot card */
        .bs-spot-skeleton {
            flex: 0 0 220px;
            scroll-snap-align: start;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 1px 6px rgba(0,0,0,0.07);
        }
        .bs-skeleton-card__line { height: 11px; border-radius: 6px; margin-bottom: 8px; }

        @media (max-width: 640px) {
            .bs-card, .bs-spot-skeleton { flex: 0 0 180px; }
            .bs-card__img-wrap { height: 130px; }
        }

        /* ── Dashboard hero search pill (no button) ─────────────── */
        .bs-dashboard-search {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: #fff;
            border-radius: 999px;
            padding: 0 1.25rem;
            height: 52px;
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            box-sizing: border-box;
        }
                </style>

        <script>
        // ── Hero Slideshow ────────────────────────────────────────
        (function () {
            const slides = document.querySelectorAll('.bs-hero__slide');
            if (slides.length < 2) return;
            let current = 0;
            setInterval(function () {
                slides[current].classList.remove('bs-hero__slide--active');
                current = (current + 1) % slides.length;
                slides[current].classList.add('bs-hero__slide--active');
            }, 5000);
        })();

        // ── Promotions carousel arrows ────────────────────────────
        (function () {
            const track   = document.getElementById('bs-promo-track');
            const btnPrev = document.getElementById('bs-promo-prev');
            const btnNext = document.getElementById('bs-promo-next');
            if (!track || !btnPrev || !btnNext) return;

            const SCROLL_BY = 300; // px per arrow click

            function updateArrows() {
                btnPrev.disabled = track.scrollLeft <= 4;
                btnNext.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
            }

            btnPrev.addEventListener('click', () => {
                track.scrollBy({ left: -SCROLL_BY, behavior: 'smooth' });
            });
            btnNext.addEventListener('click', () => {
                track.scrollBy({ left:  SCROLL_BY, behavior: 'smooth' });
            });

            track.addEventListener('scroll', updateArrows, { passive: true });
            // Run once after content is injected (home.js calls window.renderPromoCards)
            updateArrows();

            // Re-check whenever home.js finishes injecting cards
            window.addEventListener('bsPromosReady', updateArrows);
        })();

        // ── Trending carousel arrows ──────────────────────────────
        (function () {
            const track   = document.getElementById('trending-container');
            const btnPrev = document.getElementById('bs-trending-prev');
            const btnNext = document.getElementById('bs-trending-next');
            if (!track || !btnPrev || !btnNext) return;
            const SCROLL_BY = 240;
            function updateArrows() {
                btnPrev.disabled = track.scrollLeft <= 4;
                btnNext.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
            }
            btnPrev.addEventListener('click', () => track.scrollBy({ left: -SCROLL_BY, behavior: 'smooth' }));
            btnNext.addEventListener('click', () => track.scrollBy({ left:  SCROLL_BY, behavior: 'smooth' }));
            track.addEventListener('scroll', updateArrows, { passive: true });
            updateArrows();
            window.addEventListener('bsTrendingReady', updateArrows);
        })();

        // ── Hidden Gems carousel arrows ───────────────────────────
        (function () {
            const track   = document.getElementById('gems-container');
            const btnPrev = document.getElementById('bs-gems-prev');
            const btnNext = document.getElementById('bs-gems-next');
            if (!track || !btnPrev || !btnNext) return;
            const SCROLL_BY = 240;
            function updateArrows() {
                btnPrev.disabled = track.scrollLeft <= 4;
                btnNext.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
            }
            btnPrev.addEventListener('click', () => track.scrollBy({ left: -SCROLL_BY, behavior: 'smooth' }));
            btnNext.addEventListener('click', () => track.scrollBy({ left:  SCROLL_BY, behavior: 'smooth' }));
            track.addEventListener('scroll', updateArrows, { passive: true });
            updateArrows();
            window.addEventListener('bsGemsReady', updateArrows);
        })();
        </script>

    </body>
</html>