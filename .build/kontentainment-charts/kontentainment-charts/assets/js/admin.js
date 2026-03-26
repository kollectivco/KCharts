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

	const bell = document.getElementById('amcNotificationsBell');
	const dropdown = document.getElementById('amcNotificationsDropdown');

	if (bell && dropdown) {
		bell.addEventListener('click', function (event) {
			event.stopPropagation();
			const isHidden = dropdown.hasAttribute('hidden');
			if (isHidden) {
				dropdown.removeAttribute('hidden');
				bell.classList.add('is-active');
			} else {
				dropdown.setAttribute('hidden', '');
				bell.classList.remove('is-active');
			}
		});

		document.addEventListener('click', function (event) {
			if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
				dropdown.setAttribute('hidden', '');
				bell.classList.remove('is-active');
			}
		});
	}

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

	// Improved Selection functionality
	const selectAllCheckbox = document.querySelector('[data-amc-select-all]');
	const selectAllBtn = document.querySelector('[data-amc-select-all-btn]');
	const clearAllBtn = document.querySelector('[data-amc-clear-all-btn]');
	const countDisplay = document.querySelector('[data-amc-selection-count]');
	const rowCheckboxes = document.querySelectorAll('[data-amc-row-checkbox]');

	function updateSelectionState() {
		const checkedCount = Array.from(rowCheckboxes).filter(cb => cb.checked).length;
		const totalCount = rowCheckboxes.length;

		if (selectAllCheckbox) {
			selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
			selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
		}

		if (countDisplay) {
			countDisplay.textContent = checkedCount + ' selected';
			countDisplay.classList.toggle('has-selection', checkedCount > 0);
		}
	}

	if (selectAllCheckbox) {
		selectAllCheckbox.addEventListener('change', function () {
			rowCheckboxes.forEach(function (cb) {
				cb.checked = selectAllCheckbox.checked;
			});
			updateSelectionState();
		});
	}

	if (selectAllBtn) {
		selectAllBtn.addEventListener('click', function () {
			rowCheckboxes.forEach(function (cb) {
				cb.checked = true;
			});
			updateSelectionState();
		});
	}

	if (clearAllBtn) {
		clearAllBtn.addEventListener('click', function () {
			rowCheckboxes.forEach(function (cb) {
				cb.checked = false;
			});
			updateSelectionState();
		});
	}

	rowCheckboxes.forEach(function (cb) {
		cb.addEventListener('change', updateSelectionState);
	});

	// Initialize state
	updateSelectionState();
});
