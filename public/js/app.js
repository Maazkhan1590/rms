/**
 * RMS Admin JavaScript
 * Simple UI interactions for admin layout
 */

// Load axios from CDN (already loaded in layout)
// window.axios is available globally

document.addEventListener('DOMContentLoaded', () => {
	const sidebar = document.getElementById('sidebar');
	const btnSidebar = document.getElementById('btnSidebar');
	const userMenuBtn = document.getElementById('userMenuBtn');
	const userMenu = document.getElementById('userMenu');
	const btnDarkMode = document.getElementById('btnDarkMode');
	const darkIcon = document.getElementById('darkIcon');

	// Sidebar toggle (mobile)
	if (btnSidebar && sidebar) {
		btnSidebar.addEventListener('click', () => {
			sidebar.classList.toggle('hidden');
		});
	}

	// Collapsible sidebar groups
	document.querySelectorAll('[data-collapse]')?.forEach(btn => {
		btn.addEventListener('click', () => {
			const key = btn.getAttribute('data-collapse');
			const el = document.getElementById(`collapse-${key}`);
			if (el) el.classList.toggle('hidden');
		});
	});

	// User dropdown
	if (userMenuBtn && userMenu) {
		userMenuBtn.addEventListener('click', () => {
			userMenu.classList.toggle('hidden');
		});
		document.addEventListener('click', (e) => {
			if (!userMenu.contains(e.target) && !userMenuBtn.contains(e.target)) {
				userMenu.classList.add('hidden');
			}
		});
	}

	// Dark mode persistence
	const THEME_KEY = 'rms-theme';
	const setTheme = (mode) => {
		if (mode === 'dark') {
			document.body.classList.add('theme-dark');
			if (darkIcon) {
				darkIcon.classList.remove('bi-moon-stars');
				darkIcon.classList.add('bi-sun');
			}
		} else {
			document.body.classList.remove('theme-dark');
			if (darkIcon) {
				darkIcon.classList.remove('bi-sun');
				darkIcon.classList.add('bi-moon-stars');
			}
		}
	};

	const saved = localStorage.getItem(THEME_KEY);
	if (saved) setTheme(saved);

	if (btnDarkMode) {
		btnDarkMode.addEventListener('click', () => {
			const next = document.body.classList.contains('theme-dark') ? 'light' : 'dark';
			setTheme(next);
			localStorage.setItem(THEME_KEY, next);
		});
	}

	// Charts demo (if Chart.js loaded)
	const tryCharts = () => {
		if (!window.Chart) return;
		const pub = document.getElementById('chartPublications');
		if (pub) {
			new window.Chart(pub.getContext('2d'), {
				type: 'doughnut',
				data: {
					labels: ['Journal', 'Conference', 'Book'],
					datasets: [{ data: [52, 36, 12], backgroundColor: ['#4d8bff', '#0056b3', '#9ca3af'] }]
				},
				options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
			});
		}

		const sub = document.getElementById('chartSubmissions');
		if (sub) {
			new window.Chart(sub.getContext('2d'), {
				type: 'line',
				data: {
					labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
					datasets: [{ label: 'Submissions', data: [5,7,8,9,12,14,11,13,9,10,12,15], borderColor: '#0056b3', backgroundColor: 'rgba(0,86,179,0.1)', tension: 0.3 }]
				},
				options: { responsive: true, scales: { y: { beginAtZero: true } } }
			});
		}
	};

	// Defer slightly until Chart.js is available
	setTimeout(tryCharts, 50);
});
