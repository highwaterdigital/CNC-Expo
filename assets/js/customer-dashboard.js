(() => {
	const cfg = window.expoBooking || {
		root: (window.location.origin || '') + '/wp-json/expo/v1',
		nonce: '',
		isLoggedIn: false,
	};

	const root = document.querySelector('.expo-customer-dashboard');
	const tabButtons = document.querySelectorAll('.expo-tab-btn');
	const panels = document.querySelectorAll('.expo-tab-panel');
	if (!root) return;

	const sessionToken = window.localStorage.getItem('expoBookingSession') || '';

	const state = {
		data: null,
		selectedStall: null,
	};

	const buildHeaders = () => {
		const headers = {
			'Content-Type': 'application/json',
		};
		if (cfg.nonce) headers['X-WP-Nonce'] = cfg.nonce;
		if (sessionToken) headers['X-Expo-Session'] = sessionToken;
		return headers;
	};

	const api = async (endpoint, options = {}) => {
		const opts = {
			method: options.method || 'GET',
			headers: {
				...buildHeaders(),
				...(options.headers || {}),
			},
			credentials: 'same-origin',
		};
		if (options.body) {
			opts.body = JSON.stringify(options.body);
		}

		const res = await fetch(cfg.root + endpoint, opts);
		const text = await res.text();
		let json;
		try {
			json = JSON.parse(text);
		} catch (e) {
			throw { message: 'Invalid server response', responseText: text, status: res.status };
		}
		if (!res.ok) {
			throw { ...json, status: res.status };
		}
		return json;
	};

	const el = (html) => {
		const t = document.createElement('template');
		t.innerHTML = html.trim();
		return t.content.firstChild;
	};

	const setActiveTab = (tab) => {
		tabButtons.forEach((btn) => {
			const isActive = btn.dataset.tab === tab;
			btn.classList.toggle('active', isActive);
			btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
		});
		panels.forEach((panel) => {
			const isActive = panel.dataset.tab === tab;
			panel.classList.toggle('active', isActive);
			panel.classList.toggle('inactive', !isActive);
			if (isActive) {
				panel.classList.add('animate-in');
				setTimeout(() => panel.classList.remove('animate-in'), 220);
			}
		});
		root.scrollIntoView({ behavior: 'smooth', block: 'start' });
	};

	tabButtons.forEach((btn) => {
		btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
	});

	const renderLoginPrompt = () => {
		panels.forEach((p) => (p.innerHTML = ''));
		const gate = root.querySelector('.expo-login-gate');
		if (gate) {
			panels[0].appendChild(gate);
			gate.style.display = 'block';
		}
	};

	const renderBookingPanel = (data) => {
		const panel = root.querySelector('[data-tab="booking"]');
		if (!panel) return;
		const status = (data.status || '').toLowerCase();
		const stalls = data.stall_ids || [];

		panel.innerHTML = '';
		panel.appendChild(
			el(`
				<div class="expo-card">
					<div class="expo-card-header">
						<h3>Your Booking</h3>
						<span class="expo-badge expo-badge--${status}">${data.status || 'Pending'}</span>
					</div>
					<div class="expo-grid">
						<div><strong>Stall(s)</strong><span>${stalls.join(', ') || '--'}</span></div>
						<div><strong>Company</strong><span>${data.company || '--'}</span></div>
						<div><strong>Contact</strong><span>${data.contact || '--'}</span></div>
						<div><strong>Phone</strong><span>${data.phone || '--'}</span></div>
						<div><strong>Email</strong><span>${data.email || '--'}</span></div>
						${data.quotation_url ? `<div><strong>Quotation PDF</strong><a class="expo-link" href="${data.quotation_url}" target="_blank" rel="noopener">View PDF</a></div>` : ''}
					</div>
				</div>
			`)
		);
	};

	const renderCompanyPanel = (data) => {
		const panel = root.querySelector('[data-tab="company"]');
		if (!panel) return;
		panel.innerHTML = '';
		const profile = data.profile || {};
		let logoFileData = null;

		const form = el(`
			<form class="expo-card expo-form" id="expo-profile-form">
				<div class="expo-card-header"><h3>Company Profile</h3></div>
				<div class="expo-grid">
					<label>Company Name<input name="company" value="${profile.company || ''}" required /></label>
					<label>GST Number<input name="gst" value="${profile.gst || ''}" /></label>
					<label>Contact Person<input name="contact" value="${data.contact || ''}" /></label>
					<label>Phone<input name="phone" value="${data.phone || ''}" /></label>
					<label>Email<input type="email" name="email" value="${data.email || ''}" /></label>
					<label class="wide">Upload Logo
						<input type="file" name="logo_file" accept="image/*" />
						${profile.logo_url ? `<small>Current: <a class="expo-link" href="${profile.logo_url}" target="_blank" rel="noopener">${profile.logo_url}</a></small>` : ''}
					</label>
					<label class="wide">Address<textarea name="address">${profile.address || ''}</textarea></label>
				</div>
				<button type="submit" class="expo-btn expo-btn--primary">Save Company Info</button>
			</form>
		`);

		const logoInput = form.querySelector('input[name="logo_file"]');
		if (logoInput) {
			logoInput.addEventListener('change', () => {
				const file = logoInput.files && logoInput.files[0];
				if (!file) {
					logoFileData = null;
					return;
				}
				if (file.size > 3 * 1024 * 1024) {
					alert('Logo file is too large. Please upload a file under 3MB.');
					logoInput.value = '';
					logoFileData = null;
					return;
				}
				const reader = new FileReader();
				reader.onload = () => {
					logoFileData = { data: reader.result, name: file.name };
				};
				reader.readAsDataURL(file);
			});
		}

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			const payload = Object.fromEntries(formData.entries());
			if (logoFileData) {
				payload.logo_data = logoFileData.data;
				payload.logo_name = logoFileData.name;
			}
			panel.classList.add('expo-loading');
			try {
				await api('/customer/update', { method: 'POST', body: payload });
				await load();
				showToast('Company info saved', 'success');
			} catch (err) {
				showToast(err?.message || 'Unable to save company details.', 'error');
			} finally {
				panel.classList.remove('expo-loading');
			}
		});

		panel.appendChild(form);
	};

	const getAddonsForStall = (addonsMap, stall) => {
		if (!addonsMap || typeof addonsMap !== 'object') return {};
		return addonsMap[stall] || addonsMap['default'] || {};
	};

	const renderAddonsPanel = (data) => {
		const panel = root.querySelector('[data-tab="addons"]');
		if (!panel) return;
		panel.innerHTML = '';

		const stalls = data.stall_ids || [];
		if (!state.selectedStall) state.selectedStall = stalls[0] || 'default';

		const stallList = el('<div class="expo-stall-list"></div>');
		if (!stalls.length) {
			panel.appendChild(el('<div class="expo-card"><p>No stalls assigned yet. Please contact support.</p></div>'));
			return;
		}

		stalls.forEach((sid) => {
			const pill = el(`<div class="expo-stall-pill${sid === state.selectedStall ? ' active' : ''}" data-stall="${sid}">${sid}</div>`);
			pill.addEventListener('click', () => {
				state.selectedStall = sid;
				renderAddonsPanel(data);
			});
			stallList.appendChild(pill);
		});

		panel.appendChild(el('<div class="expo-card"><div class="expo-card-header"><h3>Your Stalls</h3></div></div>'));
		panel.querySelector('.expo-card').appendChild(stallList);

		const addonsForStall = getAddonsForStall(data.addons, state.selectedStall);

		const form = el(`
			<form class="expo-card expo-form" id="expo-addons-form">
				<div class="expo-card-header"><h3>Add-ons for Stall ${state.selectedStall || ''}</h3></div>
				<p class="expo-muted" style="margin-top:0;">Add services now - our team will confirm availability even while your booking is pending.</p>
				<div class="expo-grid">
					<label>Extra Chairs<input type="number" min="0" name="extra_chairs" value="${addonsForStall.extra_chairs || 0}" /></label>
					<label>Extra Tables<input type="number" min="0" name="extra_tables" value="${addonsForStall.extra_tables || 0}" /></label>
					<label>Extra Manpower<input type="number" min="0" name="extra_manpower" value="${addonsForStall.extra_manpower || 0}" /></label>
					<label>Electricity Load<input name="electricity_load" value="${addonsForStall.electricity_load || ''}" /></label>
					<label>TV Units<input type="number" min="0" name="tv_units" value="${addonsForStall.tv_units || 0}" /></label>
					<label>Spikes/Input Sockets<input type="number" min="0" name="spikes" value="${addonsForStall.spikes || 0}" /></label>
				</div>
				<button type="submit" class="expo-btn expo-btn--secondary">Save Add-ons</button>
			</form>
		`);

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			const payload = Object.fromEntries(formData.entries());
			const addonsPayload = {
				extra_chairs: parseInt(payload.extra_chairs || 0, 10),
				extra_tables: parseInt(payload.extra_tables || 0, 10),
				extra_manpower: parseInt(payload.extra_manpower || 0, 10),
				electricity_load: payload.electricity_load || '',
				tv_units: parseInt(payload.tv_units || 0, 10),
				spikes: parseInt(payload.spikes || 0, 10),
			};

			panel.classList.add('expo-loading');
			try {
				await api('/customer/update', {
					method: 'POST',
					body: { addons: addonsPayload, stall_id: state.selectedStall || 'default' },
				});
				await load();
				showToast('Add-ons saved', 'success');
			} catch (err) {
				showToast(err?.message || 'Unable to save add-ons.', 'error');
			} finally {
				panel.classList.remove('expo-loading');
			}
		});

		panel.appendChild(form);
	};

	const load = async () => {
		panels.forEach((p) => (p.innerHTML = '<p>Loading...</p>'));
		try {
			const data = await api('/customer/me', { method: 'GET' });
			state.data = data;
			state.selectedStall = (data.stall_ids || [])[0] || 'default';
			renderBookingPanel(data);
			renderCompanyPanel(data);
			renderAddonsPanel(data);
			root.setAttribute('data-loaded', '1');
		} catch (err) {
			if (err?.status === 401 || err?.code === 'invalid_token') {
				renderLoginPrompt();
				return;
			}
			panels.forEach((p) => (p.innerHTML = `<div class="expo-error">Unable to load your booking. ${err?.message || ''}</div>`));
			showToast(err?.message || 'Unable to load dashboard.', 'error');
		}
	};

	// Toast helper
	const ensureToast = () => {
		let t = document.querySelector('.expo-toast');
		if (!t) {
			t = el('<div class="expo-toast" role="status"></div>');
			document.body.appendChild(t);
		}
		return t;
	};

	const showToast = (msg, type = 'info') => {
		const t = ensureToast();
		t.textContent = msg;
		t.className = `expo-toast expo-toast--${type}`;
		t.style.display = 'block';
		setTimeout(() => {
			t.style.display = 'none';
		}, 2400);
	};

	document.addEventListener('DOMContentLoaded', () => {
		setActiveTab('booking');
		load();
	});
})();
