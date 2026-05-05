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
    </div>

    @auth
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
    <div class="bs-user-menu">
        <a href="/login"
           class="px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition">
            Sign In
        </a>
    </div>
    @endauth
</nav>

<style>
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
        /* NO overflow:hidden here. It creates a compositing layer boundary that
           clips the absolutely-positioned underline during scroll repaints on
           sticky/fixed navbars. scaleX(0) already makes the bar invisible — we
           don't need overflow to hide it, and keeping it breaks scroll. */
    }

    .bs-navbar__link:hover {
        color: var(--color-primary);
    }

    .bs-navbar__link.is-active {
        color: var(--color-primary);
    }

    .bs-navbar__link svg {
        display: block;
        /* Prevent svg from shrinking the available bottom space */
        flex-shrink: 0;
    }

    /*
     * Each link owns its own underline bar.
     * It sits flush at the bottom of the link, full-width of the link.
     * transform-origin controls which end the grow/shrink animates from.
     * Default state: scaleX(0) — invisible.
     */
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
        /* Own compositing layer — keeps the bar's repaints isolated from the
           navbar's sticky/fixed layer so scroll never causes a stale clip. */
        will-change: transform;
    }

    /*
     * .enter-from-left  — bar grows left→right (clicked item is to the right of prev)
     * .enter-from-right — bar grows right→left (clicked item is to the left of prev)
     * .exit-to-left     — bar shrinks toward the left  (departing item, new click is to the left)
     * .exit-to-right    — bar shrinks toward the right (departing item, new click is to the right)
     */
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

    /* Active — bar is fully shown, no animation running */
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
            // Parse the href so route() generated absolute URLs work correctly
            const linkPath = new URL(link.getAttribute('href'), window.location.origin).pathname;
            if (
                (linkPath === '/dashboard' && path === '/dashboard') ||
                (linkPath.startsWith('/explore') && path.startsWith('/explore')) ||
                (linkPath.startsWith('/saved')   && path.startsWith('/saved'))
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

        /* Set the active link's icon color and show its underline immediately (no animation on load) */
        function initActive(idx) {
            links.forEach(function (link, i) {
                link.classList.toggle('is-active', i === idx);
            });
            const bar = underlines[idx];
            clearUnderlineClasses(bar);
            bar.classList.add('is-shown');
        }

        /* Animate the transition from oldIdx → newIdx */
        function animateTransition(oldIdx, newIdx) {
            if (oldIdx === newIdx) return;

            const direction = newIdx > oldIdx ? 'right' : 'left';
            // "direction" = which side the new item is on relative to the old one.
            // The exiting bar should shrink toward the new item (chase it).
            // The entering bar should grow from the side closest to the old item.

            const oldBar = underlines[oldIdx];
            const newBar = underlines[newIdx];

            /* ── exit old bar ── */
            clearUnderlineClasses(oldBar);
            // Shrink toward new item's direction
            oldBar.classList.add(direction === 'right' ? 'exit-to-right' : 'exit-to-left');

            /* ── enter new bar ── */
            clearUnderlineClasses(newBar);
            // Grow from the side closest to the old item
            newBar.classList.add(direction === 'right' ? 'enter-from-left' : 'enter-from-right');

            /* ── icon colors ── */
            links[oldIdx].classList.remove('is-active');
            links[newIdx].classList.add('is-active');

            /* After the grow animation ends, lock the bar as is-shown so
               it stays visible without an ongoing animation */
            newBar.addEventListener('animationend', function handler() {
                newBar.removeEventListener('animationend', handler);
                clearUnderlineClasses(newBar);
                newBar.classList.add('is-shown');
            });
        }

        /* ── init ── */
        initActive(activeIdx);

        /* ── click handler ── */
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