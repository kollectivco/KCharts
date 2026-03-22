document.addEventListener('DOMContentLoaded', function () {
	const THEME_KEY = 'kontentainmentChartsTheme';
	const rootNodes = document.querySelectorAll('.amc-admin-shell, .amc-custom-dashboard');
	const toggles = document.querySelectorAll('[data-amc-theme-toggle]');
	const buttons = document.querySelectorAll('.amc-admin-button-row .button');
	const links = document.querySelectorAll('a[href*="amc_confirm="]');

	function getStoredTheme() {
		const stored = window.localStorage.getItem(THEME_KEY);
		return stored === 'light' ? 'light' : 'dark';
	}

	function applyTheme(theme) {
		rootNodes.forEach(function (node) {
			node.setAttribute('data-amc-theme', theme);
		});

		document.documentElement.setAttribute('data-amc-theme', theme);

		toggles.forEach(function (toggle) {
			const darkLabel = toggle.getAttribute('data-amc-theme-label-dark') || 'Dark Mode';
			const lightLabel = toggle.getAttribute('data-amc-theme-label-light') || 'Light Mode';
			const isDark = theme === 'dark';

			toggle.textContent = isDark ? darkLabel : lightLabel;
			toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
		});
	}

	function switchTheme() {
		const nextTheme = getStoredTheme() === 'dark' ? 'light' : 'dark';
		window.localStorage.setItem(THEME_KEY, nextTheme);
		applyTheme(nextTheme);
	}

	applyTheme(getStoredTheme());

	toggles.forEach(function (toggle) {
		toggle.addEventListener('click', switchTheme);
	});

	buttons.forEach(function (button) {
		button.addEventListener('click', function () {
			button.blur();
		});
	});

	links.forEach(function (link) {
		link.addEventListener('click', function (event) {
			try {
				const url = new URL(link.href);
				const message = url.searchParams.get('amc_confirm');

				if (message && !window.confirm(message)) {
					event.preventDefault();
				}
			} catch (error) {
				/* noop */
			}
		});
	});

	// Action link feedback: show loading state on action links to give immediate feedback.
	const ACTION_SELECTORS = [
		'a.amc-action-btn',
		'a.amc-alert-action',
		'.amc-admin-table a[href*="amc_row_action"]',
		'.amc-admin-alerts a[href*="amc_row_action"]',
	].join(', ');

	document.querySelectorAll(ACTION_SELECTORS).forEach(function (link) {
		link.addEventListener('click', function (event) {
			// For confirm-guarded links, wait until confirm passes.
			const isConfirm = link.href && link.href.indexOf('amc_confirm=') !== -1;
			if (isConfirm) { return; }

			const originalText = link.textContent;
			link.setAttribute('data-amc-loading', '1');
			link.textContent = 'Working\u2026';

			// Safety fallback: restore after 8s in case navigation stalls.
			setTimeout(function () {
				link.removeAttribute('data-amc-loading');
				link.setAttribute('data-amc-done', '1');
				link.textContent = originalText;
			}, 8000);
		});
	});
});
