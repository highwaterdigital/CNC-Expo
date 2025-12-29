(() => {
	// Build a local config if the plugin didn't inject one.
	if (typeof window.expoBooking === 'undefined') {
		window.expoBooking = {
			root: (window.location.origin || '') + '/wp-json/expo/v1',
			nonce: (window.wpApiSettings && window.wpApiSettings.nonce) || '',
			theme: {
				gold: '#D4AF37',
				green: '#0B5E3C',
				status: {
					available: '#5CB85C',
					pending: '#F0AD4E',
					booked: '#D9534F',
				},
			},
		};
	}

	const debug = (...args) => console.log('[Expo Dashboard]', ...args);

	const renderDebugPanel = (info) => {
		const panel = document.createElement('pre');
		panel.className = 'expo-debug-panel';
		panel.textContent = JSON.stringify(info, null, 2);
		return panel;
	};

	if (typeof window.wp === 'undefined') {
		// console.error('Expo Booking: wp api not available');
		// return;
	}

	const root = document.querySelector('.expo-customer-dashboard');
	if (!root) return;

	const sessionToken = window.localStorage.getItem('expoBookingSession') || '';
	debug('Config', window.expoBooking);
	debug('Session token', sessionToken || 'none');

	const state = {
		data: null,
	};

	const el = (html) => {
		const div = document.createElement('div');
		div.innerHTML = html.trim();
		return div.firstChild;
	};

	const renderInfo = (data) => {
		const info = el(`
      <div class="expo-card">
        <h3>Your Booking</h3>
        <div class="expo-grid">
          <div><strong>Stall</strong><span>${(data.stall_ids || []).join(', ') || '—'}</span></div>
          <div><strong>Status</strong><span class="badge badge-${(data.status || '').toLowerCase()}">${data.status}</span></div>
          <div><strong>Company</strong><span>${data.company || '—'}</span></div>
          <div><strong>Contact</strong><span>${data.contact || '—'}</span></div>
          <div><strong>Phone</strong><span>${data.phone || '—'}</span></div>
          <div><strong>Email</strong><span>${data.email || '—'}</span></div>
        </div>
      </div>
    `);
		return info;
	};

	const renderProfileForm = (data) => {
		const profile = data.profile || {};
		const form = el(`
      <form class="expo-card" id="expo-profile-form">
        <h3>Company Details</h3>
        <div class="expo-grid">
          <label>Full Company Name<input name="company" value="${profile.company || ''}" /></label>
          <label>GST<input name="gst" value="${profile.gst || ''}" /></label>
          <label>PAN<input name="pan" value="${profile.pan || ''}" /></label>
          <label>Logo URL<input name="logo_url" value="${profile.logo_url || ''}" /></label>
          <label class="wide">Address<textarea name="address">${profile.address || ''}</textarea></label>
        </div>
        <button type="submit" class="expo-btn expo-btn--primary">Save Profile</button>
      </form>
    `);

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			const payload = Object.fromEntries(formData.entries());
			payload.booking_id = data.booking_id;

			try {
                const res = await fetch(window.expoBooking.root + '/customer/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.expoBooking.nonce,
                        'X-Expo-Session': sessionToken,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();
                if (!res.ok) throw json;
				alert('Profile updated.');
			} catch (err) {
				console.error('Expo Booking: profile update failed', err);
				alert('Unable to update profile.');
			}
		});

		return form;
	};

	const renderAddonsForm = (data) => {
		const addons = data.addons || {};
		const isApproved = (data.status || '').toLowerCase() === 'approved';

		const form = el(`
      <form class="expo-card" id="expo-addons-form">
        <h3>Addons</h3>
        ${!isApproved ? '<p class="expo-muted">Addons are available after approval.</p>' : ''}
        <div class="expo-grid">
          <label>Extra Chairs<input type="number" min="0" name="extra_chairs" value="${addons.extra_chairs || 0}" /></label>
          <label>Extra Tables<input type="number" min="0" name="extra_tables" value="${addons.extra_tables || 0}" /></label>
          <label>Extra Manpower<input type="number" min="0" name="extra_manpower" value="${addons.extra_manpower || 0}" /></label>
          <label>Electricity Load<input name="electricity_load" value="${addons.electricity_load || ''}" /></label>
          <label>TV Units<input type="number" min="0" name="tv_units" value="${addons.tv_units || 0}" /></label>
          <label>Spikes/Input Sockets<input type="number" min="0" name="spikes" value="${addons.spikes || 0}" /></label>
        </div>
        <button type="submit" class="expo-btn expo-btn--primary"${!isApproved ? ' disabled' : ''}>Save Addons</button>
      </form>
    `);

		if (!isApproved) {
			return form;
		}

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(form);
			const payload = Object.fromEntries(formData.entries());
			payload.booking_id = data.booking_id;
			payload.stall_id = (data.stall_ids || [])[0] || '';
			payload.addons = payload;

			try {
                const res = await fetch(window.expoBooking.root + '/customer/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.expoBooking.nonce,
                        'X-Expo-Session': sessionToken,
                    },
                    body: JSON.stringify({
						booking_id: payload.booking_id,
						stall_id: payload.stall_id,
						addons: {
							extra_chairs: parseInt(payload.extra_chairs || 0, 10),
							extra_tables: parseInt(payload.extra_tables || 0, 10),
							extra_manpower: parseInt(payload.extra_manpower || 0, 10),
							electricity_load: payload.electricity_load || '',
							tv_units: parseInt(payload.tv_units || 0, 10),
							spikes: parseInt(payload.spikes || 0, 10),
						},
					}),
                });
                const json = await res.json();
                if (!res.ok) throw json;
				alert('Addons saved.');
			} catch (err) {
				console.error('Expo Booking: addons save failed', err);
				alert('Unable to save addons.');
			}
		});

		return form;
	};

	const render = () => {
		root.innerHTML = '';
		if (!state.data) return;
		root.appendChild(renderInfo(state.data));
		root.appendChild(renderProfileForm(state.data));
		root.appendChild(renderAddonsForm(state.data));
	};

	const load = async () => {
		root.innerHTML = '<p>Loading your booking...</p>';
		try {
            const res = await fetch(window.expoBooking.root + '/customer/me', {
                method: 'GET',
                headers: {
                    'X-WP-Nonce': window.expoBooking.nonce,
                    'X-Expo-Session': sessionToken,
                },
            });
            
            const text = await res.text();
            let json;
            try {
                json = JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON:', text);
                throw new Error('Server returned invalid JSON: ' + text.substring(0, 100) + '...');
            }

            if (!res.ok) throw json;
			debug('Load success', json);
			state.data = json;
			render();
		} catch (err) {
			console.error('Expo Booking dashboard load failed', err);
			const status = err?.data?.status || err?.status || err?.code || '';
			const message = err?.message || (err?.responseJSON?.message) || (err?.responseText) || 'Unable to load your booking. Please re-login with OTP.';
			
			// Check for various 401 indicators and show a friendly login prompt
			if (status === 401 || status === 'rest_forbidden' || status === 'no_token' || status === 'invalid_token') {
				root.innerHTML = `
					<div class="expo-login-prompt" style="text-align:center; padding: 4rem 2rem; background: #f9f9f9; border-radius: 12px; border: 1px solid #eee;">
						<div style="font-size: 48px; margin-bottom: 1rem;">👋</div>
						<h3 style="margin-bottom: 0.5rem; color: #333;">Welcome to CNC Expo</h3>
						<p style="margin-bottom: 2rem; color: #666; max-width: 400px; margin-left: auto; margin-right: auto;">
                            Exhibitors can manage their stall bookings, update company profiles, and request addons here.
                        </p>
						<button class="expo-btn expo-btn--primary expo-login-open" style="padding: 12px 32px; font-size: 16px;">
                            Login with OTP
                        </button>
                        <p style="margin-top: 1.5rem; font-size: 0.9rem; color: #888;">
                            New exhibitor? <a href="/book-space" style="color: var(--cnc-magenta);">Book a Stall</a>
                        </p>
					</div>
				`;
				return;
			}

			root.innerHTML = `<p class="expo-error">Error ${status}: ${message}</p>`;
			
			debug('Failure detail', {
				status: err?.status,
				code: err?.code,
				responseJSON: err?.responseJSON,
				responseText: err?.responseText,
				config: window.expoBooking,
				sessionToken,
			});
			root.appendChild(
				renderDebugPanel({
					status: err?.status,
					code: err?.code,
					message,
					responseJSON: err?.responseJSON,
					responseText: err?.responseText,
					config: window.expoBooking,
					sessionToken,
				})
			);
		}
	};

	document.addEventListener('DOMContentLoaded', load);
})();
