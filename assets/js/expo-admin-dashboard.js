(() => {
	const cfg = window.expoBookingAdmin || {
		root: (window.location.origin || '') + '/wp-json/expo/v1',
		nonce: (window.wpApiSettings && window.wpApiSettings.nonce) || '',
        adminUrl: (window.location.origin || '') + '/wp-admin/admin.php'
	};

	const root = document.querySelector('.expo-admin-dashboard');
	if (!root) return;

    // --- Helper Functions ---
    const el = (html) => {
        const t = document.createElement('template');
        t.innerHTML = html.trim();
        return t.content.firstChild;
    };

    const apiCall = async (endpoint, method = 'GET', body = null) => {
        const opts = {
            method,
            headers: {
                'X-WP-Nonce': cfg.nonce,
                'Content-Type': 'application/json'
            }
        };
        if (body) opts.body = JSON.stringify(body);
        
        const res = await fetch(cfg.root + endpoint, opts);
        if (!res.ok) {
            const text = await res.text();
            throw new Error(`API Error: ${text}`);
        }
        return res.json();
    };

    // --- Components ---

    // 1. Bookings Tab
    const renderBookings = async (container) => {
        container.innerHTML = '<div class="expo-loading">Loading bookings...</div>';
        try {
            const bookings = await apiCall('/admin/bookings');
            
            const card = el(`
                <div class="expo-card expo-admin-card">
                    <div class="expo-card-header">
                        <h3>Manage Bookings <span class="expo-count">(${bookings.length})</span></h3>
                        <button class="expo-btn expo-btn--sm expo-btn-refresh">Refresh</button>
                    </div>
                    <div class="expo-table-wrapper"></div>
                </div>
            `);

            const table = document.createElement('table');
            table.className = 'expo-table expo-admin-table';
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Stalls</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;
            
            const tbody = table.querySelector('tbody');
            bookings.forEach(row => {
                const statusClass = `expo-status-${(row.status || 'pending').toLowerCase()}`;
                const tr = el(`
                    <tr>
                        <td>#${row.id || ''}</td>
                        <td>
                            <strong>${row.company || '—'}</strong><br>
                            <small>${row.email || ''}</small>
                        </td>
                        <td>${(row.stalls || []).join(', ') || '—'}</td>
                        <td><span class="expo-badge ${statusClass}">${row.status || 'Pending'}</span></td>
                        <td>
                            ${row.contact || ''}<br>
                            <small>${row.phone || ''}</small>
                        </td>
                        <td class="expo-actions">
                            <button class="expo-btn-icon expo-btn-edit" title="Edit">✏️</button>
                            ${row.status !== 'approved' ? '<button class="expo-btn-icon expo-btn-approve" title="Approve">✅</button>' : ''}
                            ${row.status !== 'rejected' ? '<button class="expo-btn-icon expo-btn-reject" title="Reject">❌</button>' : ''}
                        </td>
                    </tr>
                `);

                // Bind Events
                tr.querySelector('.expo-btn-edit').onclick = () => {
                    showEditModal(row, async (data) => {
                        await updateBooking(data);
                        renderBookings(container); // Reload tab
                    });
                };

                const approveBtn = tr.querySelector('.expo-btn-approve');
                if (approveBtn) {
                    approveBtn.onclick = async () => {
                        if(confirm(`Approve booking for ${row.company}?`)) {
                            await updateStatus(row.id, 'approved');
                            renderBookings(container);
                        }
                    };
                }

                const rejectBtn = tr.querySelector('.expo-btn-reject');
                if (rejectBtn) {
                    rejectBtn.onclick = async () => {
                        if(confirm(`Reject booking for ${row.company}?`)) {
                            await updateStatus(row.id, 'rejected');
                            renderBookings(container);
                        }
                    };
                }

                tbody.appendChild(tr);
            });

            card.querySelector('.expo-table-wrapper').appendChild(table);
            card.querySelector('.expo-btn-refresh').onclick = () => renderBookings(container);

            container.innerHTML = '';
            container.appendChild(card);

        } catch (err) {
            container.innerHTML = `<div class="expo-error">Unable to load bookings. ${err.message} <button class="expo-btn expo-btn--sm">Retry</button></div>`;
            container.querySelector('button').onclick = () => renderBookings(container);
        }
    };

    // 2. Floor Plan Tab
    const renderFloorPlan = (container) => {
        // Construct URL for the submenu page
        // We need to point to edit.php?post_type=cnc_booking&page=cnc-floorplan
        const adminRoot = cfg.adminUrl.replace('admin.php', '');
        const iframeUrl = `${adminRoot}edit.php?post_type=cnc_booking&page=cnc-floorplan&view=designer&embed=true`;
        
        container.innerHTML = `
            <div class="expo-card expo-admin-card" style="height: 800px; padding: 0; overflow: hidden;">
                <iframe src="${iframeUrl}" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        `;
    };

    // 3. Profile Tab
    const renderProfile = async (container) => {
        container.innerHTML = '<div class="expo-loading">Loading profile...</div>';
        try {
            const user = await apiCall('/admin/me');
            
            const card = el(`
                <div class="expo-card expo-admin-card" style="max-width: 600px; margin: 0 auto;">
                    <div class="expo-card-header">
                        <h3>My Profile</h3>
                    </div>
                    <div class="expo-card-body">
                        <form id="expo-profile-form">
                            <div class="expo-form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" value="${user.first_name || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" value="${user.last_name || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="${user.email || ''}">
                            </div>
                            <div class="expo-form-actions">
                                <button type="submit" class="expo-btn expo-btn--primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            card.querySelector('form').onsubmit = async (e) => {
                e.preventDefault();
                const btn = e.target.querySelector('button');
                const originalText = btn.innerText;
                btn.innerText = 'Saving...';
                btn.disabled = true;

                try {
                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());
                    const res = await apiCall('/admin/profile', 'POST', data);
                    if (res.success) {
                        alert('Profile updated successfully!');
                    } else {
                        alert('Error: ' + (res.message || 'Unknown error'));
                    }
                } catch (err) {
                    alert('Failed to update profile: ' + err.message);
                } finally {
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            };

            container.innerHTML = '';
            container.appendChild(card);

        } catch (err) {
            container.innerHTML = `<div class="expo-error">Unable to load profile. ${err.message}</div>`;
        }
    };

    // --- Shared Logic ---

    const updateStatus = async (id, status) => {
        const endpoint = status === 'approved' ? '/admin/approved' : '/admin/rejected';
        await apiCall(endpoint + `?id=${id}`, 'POST');
    };

    const updateBooking = async (data) => {
        await apiCall('/admin/update', 'POST', data);
    };

    const showEditModal = (booking, onSave) => {
        const modal = el(`
            <div class="expo-modal-overlay">
                <div class="expo-modal">
                    <div class="expo-modal-header">
                        <h3>Edit Booking #${booking.id}</h3>
                        <button class="expo-modal-close">&times;</button>
                    </div>
                    <div class="expo-modal-body">
                        <form id="expo-edit-form">
                            <div class="expo-form-group">
                                <label>Company Name</label>
                                <input type="text" name="company" value="${booking.company || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Contact Person</label>
                                <input type="text" name="contact" value="${booking.contact || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" value="${booking.phone || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="${booking.email || ''}">
                            </div>
                            <div class="expo-form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="pending" ${booking.status === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="approved" ${booking.status === 'approved' ? 'selected' : ''}>Approved</option>
                                    <option value="rejected" ${booking.status === 'rejected' ? 'selected' : ''}>Rejected</option>
                                </select>
                            </div>
                            <div class="expo-modal-actions">
                                <button type="button" class="expo-btn expo-btn--secondary expo-modal-close-btn">Cancel</button>
                                <button type="submit" class="expo-btn expo-btn--primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `);

        document.body.appendChild(modal);
        const close = () => modal.remove();
        modal.querySelectorAll('.expo-modal-close, .expo-modal-close-btn').forEach(b => b.onclick = close);

        modal.querySelector('form').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            await onSave({ ...data, id: booking.id });
            close();
        };
    };

    // --- Main Init ---

	const init = () => {
        // Basic Layout
        root.innerHTML = `
            <div class="expo-dashboard-header">
                <h2>Admin Dashboard</h2>
                <div class="expo-tabs">
                    <button class="expo-tab active" data-tab="bookings">Bookings</button>
                    <button class="expo-tab" data-tab="floorplan">Stall Management</button>
                    <button class="expo-tab" data-tab="profile">My Profile</button>
                </div>
                <a href="${window.location.origin}/wp-login.php?action=logout" class="expo-logout-link">Logout</a>
            </div>
            <div class="expo-tab-content"></div>
        `;

        const contentArea = root.querySelector('.expo-tab-content');
        const tabs = root.querySelectorAll('.expo-tab');

        const switchTab = (tabId) => {
            tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === tabId));
            
            if (tabId === 'bookings') renderBookings(contentArea);
            if (tabId === 'floorplan') renderFloorPlan(contentArea);
            if (tabId === 'profile') renderProfile(contentArea);
        };

        tabs.forEach(tab => {
            tab.onclick = () => switchTab(tab.dataset.tab);
        });

        // Initial Load
        switchTab('bookings');
	};

	document.addEventListener('DOMContentLoaded', init);
})();
