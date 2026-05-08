{{-- Navbar Component --}}
<nav class="bs-navbar bs-navbar--solid">
    <div class="bs-navbar__logo-name">
        <a href="{{ url('/dashboard') }}">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name', 'BiteSpot') }} logo" class="bs-navbar__logo">
        </a>
        <span class="bs-navbar__name">BiteSpot</span>
    </div>

    <div class="bs-navbar__links bs-navbar__links--center">
        <a href="/dashboard" class="bs-navbar__link" data-nav="home" aria-label="Home">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="bs-navbar__underline"></span>
        </a>
        <a href="{{ route('explore') }}" class="bs-navbar__link" data-nav="explore" aria-label="Explore">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/>
                <line x1="8" y1="2" x2="8" y2="18"/>
                <line x1="16" y1="6" x2="16" y2="22"/>
            </svg>
            <span class="bs-navbar__underline"></span>
        </a>
        <a href="/saved" class="bs-navbar__link" data-nav="saved" aria-label="Saved">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
            </svg>
            <span class="bs-navbar__underline"></span>
        </a>

        {{-- VENDOR DASHBOARD LINK (ONLY VISIBLE TO VENDORS) --}}
        @if(auth()->check() && auth()->user()->isVendor())
        <a href="/vendor-dashboard" class="bs-navbar__link" data-nav="vendor-dashboard" aria-label="Vendor Dashboard">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3h18v18H3zM12 3v18M3 12h18"/>
            </svg>
            <span class="bs-navbar__underline"></span>
        </a>
        @endif
    </div>

    @auth
    <div class="bs-user-menu" id="user-menu-wrap">
        <button class="bs-user-menu__trigger" id="user-menu-btn" aria-expanded="false">
            <span class="bs-user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <span class="bs-user-menu__name">{{ auth()->user()->name }}</span>
            <svg class="bs-user-menu__chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
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
            {{-- 
            <a href="/my-reviews" class="bs-user-menu__item">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                My Reviews
            </a>
            --}}
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
    <div class="bs-user-menu">
        <a href="/login"
           class="bs-signin-btn">
            Sign In
        </a>
    </div>
    @endauth
</nav>

<style>
    /* ── Shared base ── */
    .bs-navbar__links--center {
        display: flex;
        align-items: stretch;
        justify-content: center;
        gap: 0;
        flex: 1;
    }

    .bs-navbar__link {
        background: none;
        border: none;
        outline: none;
        padding: 0 2rem;
        padding-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        position: relative;
        color: #374151;
        transition: color 0.2s ease;
        text-decoration: none;
    }

    .bs-navbar__link:hover {
        color: var(--color-primary);
    }

    .bs-navbar__link.is-active {
        color: var(--color-primary);
    }

    .bs-navbar__link svg {
        display: block;
        flex-shrink: 0;
    }

    .bs-navbar__underline {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, #ff8800 60%, #ffb347 100%);
        border-radius: 2px 2px 0 0;
        transform: scaleX(0);
        transform-origin: left center;
        pointer-events: none;
        will-change: transform;
    }

    .bs-navbar__underline.enter-from-left {
        transform-origin: left center;
        animation: underline-grow 0.32s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .bs-navbar__underline.enter-from-right {
        transform-origin: right center;
        animation: underline-grow 0.32s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .bs-navbar__underline.exit-to-right {
        transform-origin: right center;
        animation: underline-shrink 0.24s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .bs-navbar__underline.exit-to-left {
        transform-origin: left center;
        animation: underline-shrink 0.24s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .bs-navbar__underline.is-shown {
        transform: scaleX(1);
    }

    @keyframes underline-grow {
        from { transform: scaleX(0); }
        to   { transform: scaleX(1); }
    }

    @keyframes underline-shrink {
        from { transform: scaleX(1); }
        to   { transform: scaleX(0); }
    }

    /* ── Sign in button ── */
    .bs-signin-btn {
        padding: 0.5rem 1rem;
        background: #f97316;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 9999px;
        text-decoration: none;
        transition: background 0.2s ease;
        white-space: nowrap;
    }

    .bs-signin-btn:hover {
        background: #ea6c10;
    }

    /* ════════════════════════════════════════
       MOBILE  (≤ 767px) — top bar
    ════════════════════════════════════════ */
    @media (max-width: 767px) {

        /* The nav itself sits at the top, full width */
        .bs-navbar {
            position: fixed !important;
            top: 0 !important;
            bottom: auto !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 60px !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            padding: 0 !important;
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            border-top: none !important;
            border-radius: 0 !important;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
        }

        /* Add top padding to page body so content isn't hidden under the bar */
        body {
            padding-top: 60px;
            padding-bottom: 0;
        }

        /* Logo area — left side, show only the image, hide the text name */
        .bs-navbar__logo-name {
            display: flex !important;
            align-items: center;
            padding: 0 0.75rem;
            flex-shrink: 0;
        }

        .bs-navbar__name {
            display: none !important;
        }

        .bs-navbar__logo {
            width: 32px !important;
            height: 32px !important;
        }

        /* Center nav links — flex: 1 so they fill all remaining space between logo and avatar */
        .bs-navbar__links--center {
            flex: 1;
            display: flex;
            align-items: stretch;
            justify-content: center;
            gap: 0;
            height: 100%;
        }

        /* Each nav link: taller tap targets, underline shifts to top on mobile */
        .bs-navbar__link {
            padding: 0 1.1rem;
            padding-top: 0.4rem;
            height: 100%;
            flex-direction: column;
            justify-content: center;
        }

        /* On mobile the underline stays at the BOTTOM (standard for top nav) */
        .bs-navbar__underline {
            bottom: 0;
            top: auto;
            border-radius: 2px 2px 0 0;
        }

        /* ── User menu: avatar circle only, no name, no chevron ── */
        .bs-user-menu {
            padding: 0 0.75rem;
            flex-shrink: 0;
            position: relative;
        }

        .bs-user-menu__name,
        .bs-user-menu__chevron {
            display: none !important;
        }

        .bs-user-menu__trigger {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .bs-user-avatar {
            width: 34px;
            height: 34px;
            font-size: 0.875rem;
        }

        /* Dropdown opens DOWNWARD on mobile (navbar is at top) */
        .bs-user-menu__dropdown {
            top: calc(100% + 8px) !important;
            bottom: auto !important;
            right: 0.5rem !important;
            left: auto !important;
            min-width: 180px;
        }

        /* Sign-in button on mobile — compact */
        .bs-signin-btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.75rem;
        }
    }
</style>

<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        const links = Array.from(document.querySelectorAll('.bs-navbar__link[data-nav]'));
        const underlines = links.map(l => l.querySelector('.bs-navbar__underline'));

        /* ── determine active link from current URL ── */
        const path = window.location.pathname;
        let activeIdx = 0;
        links.forEach(function (link, i) {
            const linkPath = new URL(link.getAttribute('href'), window.location.origin).pathname;
            if (
                (linkPath === '/dashboard' && path === '/dashboard') ||
                (linkPath.startsWith('/explore') && path.startsWith('/explore')) ||
                (linkPath.startsWith('/saved')   && path.startsWith('/saved')) ||
                (linkPath.startsWith('/vendor-dashboard') && path.startsWith('/vendor-dashboard'))
            ) {
                activeIdx = i;
            }
        });

        /* ── helpers ── */
        function clearUnderlineClasses(bar) {
            bar.classList.remove(
                'enter-from-left', 'enter-from-right',
                'exit-to-left',    'exit-to-right',
                'is-shown'
            );
        }

        function initActive(idx) {
            if (links.length > 0 && links[idx]) {
                links.forEach(function (link, i) {
                    link.classList.toggle('is-active', i === idx);
                });
                const bar = underlines[idx];
                clearUnderlineClasses(bar);
                bar.classList.add('is-shown');
            }
        }

        function animateTransition(oldIdx, newIdx) {
            if (oldIdx === newIdx || !underlines[oldIdx] || !underlines[newIdx]) return;

            const direction = newIdx > oldIdx ? 'right' : 'left';
            const oldBar = underlines[oldIdx];
            const newBar = underlines[newIdx];

            clearUnderlineClasses(oldBar);
            oldBar.classList.add(direction === 'right' ? 'exit-to-right' : 'exit-to-left');

            clearUnderlineClasses(newBar);
            newBar.classList.add(direction === 'right' ? 'enter-from-left' : 'enter-from-right');

            links[oldIdx].classList.remove('is-active');
            links[newIdx].classList.add('is-active');

            newBar.addEventListener('animationend', function handler() {
                newBar.removeEventListener('animationend', handler);
                clearUnderlineClasses(newBar);
                newBar.classList.add('is-shown');
            });
        }

        initActive(activeIdx);

        links.forEach(function (link, i) {
            link.addEventListener('click', function () {
                if (i === activeIdx) return;
                animateTransition(activeIdx, i);
                activeIdx = i;
            });
        });
    });

    /* ── User menu toggle ── */
    const menuBtn = document.getElementById('user-menu-btn');
    const menuDropdown = document.getElementById('user-menu-dropdown');
    if (menuBtn && menuDropdown) {
        menuBtn.addEventListener('click', function () {
            const isOpen = menuDropdown.classList.toggle('is-open');
            menuBtn.setAttribute('aria-expanded', isOpen);
        });
        document.addEventListener('click', function (e) {
            if (!document.getElementById('user-menu-wrap').contains(e.target)) {
                menuDropdown.classList.remove('is-open');
                menuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
}());
</script>