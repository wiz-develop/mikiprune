(function () {
	'use strict';

	var page = document.querySelector('.kirei2026');
	if (!page) {
		return;
	}

	var revealItems = page.querySelectorAll('[data-kirei-reveal]');
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (reduceMotion || !('IntersectionObserver' in window)) {
		revealItems.forEach(function (item) {
			item.classList.add('is-visible');
		});
		return;
	}

	page.classList.add('kirei-ready');

	var observer = new IntersectionObserver(function (entries) {
		entries.forEach(function (entry) {
			if (!entry.isIntersecting) {
				return;
			}

			entry.target.classList.add('is-visible');
			observer.unobserve(entry.target);
		});
	}, {
		rootMargin: '0px 0px -10% 0px',
		threshold: 0.08
	});

	revealItems.forEach(function (item) {
		observer.observe(item);
	});
}());

