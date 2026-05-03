import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Navbar detach on scroll
document.addEventListener('DOMContentLoaded', function () {
	const navbar = document.querySelector('.bs-navbar');
	let lastScrollY = window.scrollY;
	let ticking = false;

	function onScroll() {
		if (!navbar) return;
		if (window.scrollY > 40) {
			navbar.classList.add('bs-navbar--detached');
		} else {
			navbar.classList.remove('bs-navbar--detached');
		}
	}

	window.addEventListener('scroll', function () {
		if (!ticking) {
			window.requestAnimationFrame(function () {
				onScroll();
				ticking = false;
			});
			ticking = true;
		}
	});
	onScroll(); // Initial check
	});

	// Navbar indicator logic for dashboard
	document.addEventListener('DOMContentLoaded', function () {
		const indicator = document.getElementById('navbar-indicator');
		const links = document.querySelectorAll('.bs-navbar__link[data-nav]');
		if (!indicator || !links.length) return;



		function setIndicator() {
			let active = null;
			const path = window.location.pathname;
			if (path === '/dashboard' || path === '/') {
				active = document.querySelector('.bs-navbar__link[data-nav="home"]');
			} else if (path.startsWith('/explore')) {
				active = document.querySelector('.bs-navbar__link[data-nav="explore"]');
			} else if (path.startsWith('/saved')) {
				active = document.querySelector('.bs-navbar__link[data-nav="saved"]');
			}
			if (active) {
				// Calculate left position by summing widths and gaps of previous siblings
				let left = 0;
				let node = active.parentElement.firstElementChild;
				const gap = parseFloat(getComputedStyle(active.parentElement).gap) || 0;
				while (node && node !== active) {
					if (node.classList.contains('bs-navbar__link')) {
						left += node.offsetWidth + gap;
					}
					node = node.nextElementSibling;
				}
				indicator.style.width = active.offsetWidth + 'px';
				indicator.style.left = left + 'px';
				indicator.style.opacity = 1;
			} else {
				indicator.style.opacity = 0;
			}
		}

		setIndicator();
		window.addEventListener('resize', setIndicator);
		// Optional: If you use client-side routing, listen for route changes and call setIndicator()
	});
