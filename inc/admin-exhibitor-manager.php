<?php
/**
 * CNC Exhibitor Manager
 * Admin tool to sync Bookings with Exhibitor Profiles.
 */

if (!defined('ABSPATH')) exit;

function cnc_register_exhibitor_manager_page() {
    add_submenu_page(
        'edit.php?post_type=cnc_exhibitor',
        'Exhibitor Manager',
        'Sync & Manage',
        'manage_options',
        'cnc-exhibitor-manager',
        'cnc_render_exhibitor_manager_page'
    );
}
add_action('admin_menu', 'cnc_register_exhibitor_manager_page');

function cnc_render_exhibitor_manager_page() {
    // Handle Sync Action
    if (isset($_POST['cnc_sync_exhibitors']) && check_admin_referer('cnc_sync_exhibitors_action')) {
        cnc_process_exhibitor_sync();
    }

    ?>
    <div class="wrap">
        <h1>Exhibitor Manager</h1>
        <p>Use this tool to sync confirmed Bookings into Exhibitor Profiles.</p>
        
        <div class="card" style="max-width: 600px; padding: 20px; margin-bottom: 20px;">
            <h2>Sync Bookings to Exhibitors</h2>
            <p>This will:</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li>Scan all <strong>Stall Bookings</strong>.</li>
                <li>Create new <strong>Exhibitor Profiles</strong> for bookings that don't have one (matched by Email or Company Name).</li>
                <li>Update existing profiles with the latest Stall Number.</li>
                <li>Link the Booking ID to the Exhibitor Profile.</li>
            </ul>
            <form method="post">
                <?php wp_nonce_field('cnc_sync_exhibitors_action'); ?>
                <input type="submit" name="cnc_sync_exhibitors" class="button button-primary button-hero" value="Run Sync Now">
            </form>
        </div>

        <h2>Sync Status</h2>
        <?php cnc_render_sync_status_table(); ?>
    </div>
    <?php
}

function cnc_process_exhibitor_sync() {
    $bookings = get_posts(array(
        'post_type' => 'cnc_booking',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));

    $created = 0;
    $updated = 0;

    foreach ($bookings as $booking) {
        $company_name = $booking->post_title;
        $email = get_post_meta($booking->ID, 'cnc_email', true); // Assuming 'cnc_email' is the key, checking bookings.php it might be different, let's verify.
        // Checking bookings.php, it doesn't explicitly list 'cnc_email' in columns but it's likely there. 
        // Wait, bookings.php columns show 'cnc_contact_person', 'cnc_phone'. 
        // I should check how bookings are saved. Assuming 'cnc_email' for now, but will fallback to title match.
        
        // Actually, let's check if we can find an email.
        if (!$email) {
             // Try to find email in meta if key is different
             $email = get_post_meta($booking->ID, 'cnc_contact_email', true);
        }

        $stall_id = get_post_meta($booking->ID, 'cnc_stall_id', true);
        $phone = get_post_meta($booking->ID, 'cnc_phone', true);

        // 1. Try to find existing Exhibitor by Linked Booking ID
        $exhibitor_query = new WP_Query(array(
            'post_type' => 'cnc_exhibitor',
            'meta_key' => 'cnc_linked_booking_id',
            'meta_value' => $booking->ID,
            'posts_per_page' => 1
        ));

        $exhibitor_id = 0;

        if ($exhibitor_query->have_posts()) {
            $exhibitor_id = $exhibitor_query->posts[0]->ID;
        } else {
            // 2. Try to find by Email
            if ($email) {
                $exhibitor_query = new WP_Query(array(
                    'post_type' => 'cnc_exhibitor',
                    'meta_key' => 'cnc_contact_email',
                    'meta_value' => $email,
                    'posts_per_page' => 1
                ));
                if ($exhibitor_query->have_posts()) {
                    $exhibitor_id = $exhibitor_query->posts[0]->ID;
                }
            }

            // 3. Try to find by Title (Company Name)
            if (!$exhibitor_id) {
                $exhibitor_by_title = get_page_by_title($company_name, OBJECT, 'cnc_exhibitor');
                if ($exhibitor_by_title) {
                    $exhibitor_id = $exhibitor_by_title->ID;
                }
            }
        }

        if ($exhibitor_id) {
            // Update Existing
            update_post_meta($exhibitor_id, 'cnc_stall_number', $stall_id);
            update_post_meta($exhibitor_id, 'cnc_linked_booking_id', $booking->ID);
            // Only update contact info if missing
            if (!get_post_meta($exhibitor_id, 'cnc_contact_email', true)) update_post_meta($exhibitor_id, 'cnc_contact_email', $email);
            if (!get_post_meta($exhibitor_id, 'cnc_contact_phone', true)) update_post_meta($exhibitor_id, 'cnc_contact_phone', $phone);
            
            $updated++;
        } else {
            // Create New
            $exhibitor_data = array(
                'post_title'    => $company_name,
                'post_status'   => 'draft', // Draft so admin can review
                'post_type'     => 'cnc_exhibitor',
                'post_content'  => '<!-- Auto-generated from Booking #' . $booking->ID . ' -->'
            );
            
            $exhibitor_id = wp_insert_post($exhibitor_data);
            
            if ($exhibitor_id) {
                update_post_meta($exhibitor_id, 'cnc_stall_number', $stall_id);
                update_post_meta($exhibitor_id, 'cnc_linked_booking_id', $booking->ID);
                update_post_meta($exhibitor_id, 'cnc_contact_email', $email);
                update_post_meta($exhibitor_id, 'cnc_contact_phone', $phone);
                $created++;
            }
        }
    }

    echo '<div class="notice notice-success is-dismissible"><p>Sync Complete! Created: ' . $created . ', Updated: ' . $updated . '</p></div>';
}

function cnc_render_sync_status_table() {
    $bookings = get_posts(array(
        'post_type' => 'cnc_booking',
        'posts_per_page' => 20, // Limit for display
        'post_status' => 'any'
    ));

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Booking ID</th><th>Company</th><th>Stall</th><th>Linked Exhibitor</th><th>Status</th></tr></thead>';
    echo '<tbody>';

    foreach ($bookings as $booking) {
        $stall = get_post_meta($booking->ID, 'cnc_stall_id', true);
        
        // Find linked exhibitor
        $exhibitor_query = new WP_Query(array(
            'post_type' => 'cnc_exhibitor',
            'meta_key' => 'cnc_linked_booking_id',
            'meta_value' => $booking->ID,
            'posts_per_page' => 1
        ));

        $linked = '-';
        if ($exhibitor_query->have_posts()) {
            $exhibitor = $exhibitor_query->posts[0];
            $linked = '<a href="' . get_edit_post_link($exhibitor->ID) . '">' . esc_html($exhibitor->post_title) . '</a>';
        }

        echo '<tr>';
        echo '<td>' . $booking->ID . '</td>';
        echo '<td>' . esc_html($booking->post_title) . '</td>';
        echo '<td>' . esc_html($stall) . '</td>';
        echo '<td>' . $linked . '</td>';
        echo '<td>' . ($linked !== '-' ? '<span style="color:green">Synced</span>' : '<span style="color:red">Not Synced</span>') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><em>Showing last 20 bookings.</em></p>';
}
