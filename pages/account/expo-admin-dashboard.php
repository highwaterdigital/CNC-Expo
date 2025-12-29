<?php
/**
 * Expo Admin Dashboard (theme fallback).
 *
 * @package cnc_child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check permissions
if (!current_user_can('manage_options')) {
    echo '<div class="expo-error">Access Denied</div>';
    return;
}
?>
<style>
    .expo-admin-dashboard { padding: 20px; background: #f9f9f9; }
    .expo-admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .expo-admin-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .expo-admin-table th, .expo-admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
    .expo-admin-table th { background: #f1f1f1; font-weight: 600; }
    .expo-btn-approve { background: #28a745; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
    .expo-btn-reject { background: #dc3545; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px; }
    .expo-status-pending { color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 4px; }
    .expo-status-approved { color: #155724; background: #d4edda; padding: 2px 6px; border-radius: 4px; }
</style>

<section class="expo-dashboard-section">
	<div class="expo-admin-header">
        <h2>Admin Dashboard</h2>
        <button onclick="location.reload()" class="expo-btn expo-btn--primary">Refresh</button>
    </div>
	
	<div class="expo-admin-dashboard">
        <table class="expo-admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Stalls</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="expo-admin-bookings-body">
                <tr><td colspan="6">Loading bookings...</td></tr>
            </tbody>
        </table>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('expo-admin-bookings-body');
    const nonce = '<?php echo wp_create_nonce("wp_rest"); ?>';
    const root = '<?php echo esc_url_raw(rest_url("expo/v1")); ?>';

    function loadBookings() {
        fetch(root + '/admin/bookings', {
            headers: { 'X-WP-Nonce': nonce }
        })
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
            if(data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No bookings found.</td></tr>';
                return;
            }
            data.forEach(booking => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${booking.id}</td>
                    <td>${booking.company}</td>
                    <td>${booking.contact}<br><small>${booking.phone}</small></td>
                    <td>${booking.stalls.join(', ')}</td>
                    <td><span class="expo-status-${booking.status}">${booking.status}</span></td>
                    <td>
                        ${booking.status === 'pending' ? `
                            <button class="expo-btn-approve" onclick="updateStatus(${booking.id}, 'approved')">Approve</button>
                            <button class="expo-btn-reject" onclick="updateStatus(${booking.id}, 'rejected')">Reject</button>
                        ` : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="6" style="color:red">Error loading bookings.</td></tr>';
            console.error(err);
        });
    }

    window.updateStatus = function(id, status) {
        if(!confirm('Are you sure you want to ' + status + ' this booking?')) return;
        
        fetch(root + '/admin/' + status, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce
            },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert('Booking ' + status);
                loadBookings();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Error updating status'));
    };

    loadBookings();
});
</script>
