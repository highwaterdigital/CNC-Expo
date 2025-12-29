(() => {
	const modal = document.getElementById('expo-login-modal');
	const backdrop = document.getElementById('expo-login-backdrop');
	if (!modal || !backdrop) return;

	const form = modal.querySelector('#expo-login-form');
	const otpField = modal.querySelector('.expo-otp-field');
	const statusEl = modal.querySelector('.expo-login-status');
	const submitBtn = form.querySelector('button[type="submit"]');

	const openModal = () => {
		modal.hidden = false;
		backdrop.hidden = false;
		document.body.classList.add('expo-login-open');
	};

	const closeModal = () => {
		modal.hidden = true;
		backdrop.hidden = true;
		document.body.classList.remove('expo-login-open');
	};

    // Robust Event Delegation for Login Triggers
    // We bind to document body to catch any click, even on dynamically added elements
    document.body.addEventListener('click', (e) => {
        // 1. Check for explicit class
        let trigger = e.target.closest('.expo-login-open');
        
        // 2. Check for data attribute
        if (!trigger) trigger = e.target.closest('[data-expo-login-trigger]');
        
        // 3. Check for title="Login" (common in WP menus)
        if (!trigger && e.target.closest('a')) {
            const link = e.target.closest('a');
            if (link.title === 'Login' || link.textContent.trim() === 'Login') {
                trigger = link;
            }
        }

        if (trigger) {
            console.log('🔐 Expo Login: Trigger clicked', {
                element: trigger,
                class: trigger.className,
                text: trigger.textContent
            });
            e.preventDefault();
            e.stopPropagation(); // Prevent other handlers
            openModal();
        }
    });

	modal.querySelector('.expo-login-close').addEventListener('click', closeModal);
	backdrop.addEventListener('click', closeModal);

	const setStatus = (msg, isError = false) => {
		statusEl.textContent = msg;
		statusEl.style.color = isError ? '#d9534f' : '#1f2f26';
	};

	const callApi = async (data) => {
		const res = await fetch(`${expoLogin.root}/customer/login`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': expoLogin.nonce,
			},
			credentials: 'same-origin',
			body: JSON.stringify(data),
		});
		const json = await res.json();
		if (!res.ok || json.success === false) {
			throw json || { message: 'Login failed' };
		}
		return json;
	};

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		const formData = new FormData(form);
		const payload = Object.fromEntries(formData.entries());

		if (!payload.phone || !payload.email) {
			setStatus('Phone and email are required.', true);
			return;
		}

		submitBtn.disabled = true;
		submitBtn.textContent = otpField.hidden ? 'Sending OTP...' : 'Verifying...';

		try {
			const response = await callApi(payload);
			if (payload.otp) {
				// verified
				window.localStorage.setItem('expoBookingSession', response.token);
				setStatus(expoLogin.messages.loginOk || 'Login successful.');
				window.location.href = expoLogin.redirect || '/my-account';
			} else {
				otpField.hidden = false;
				submitBtn.textContent = 'Verify OTP';
				setStatus(expoLogin.messages.otpSent || 'OTP sent.');
			}
		} catch (err) {
			setStatus(err?.message || expoLogin.messages.otpFailed || 'Login failed.', true);
		} finally {
			submitBtn.disabled = false;
		}
	});
})();
