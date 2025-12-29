<?php
/**
 * CNC Booking Post Type
 * Registers the 'cnc_booking' custom post type and manages its admin columns.
 * Updated: Fixed syntax error in meta box registration.
 */

if (!defined('ABSPATH')) exit;

function cnc_register_booking_cpt() {
    $labels = array(
        'name'                  => 'Stall Bookings',
        'singular_name'         => 'Booking',
        'menu_name'             => 'Stall Bookings',
        'name_admin_bar'        => 'Booking',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Booking',
        'new_item'              => 'New Booking',
        'edit_item'             => 'Edit Booking',
        'view_item'             => 'View Booking',
        'all_items'             => 'All Bookings',
        'search_items'          => 'Search Bookings',
        'not_found'             => 'No bookings found.',
        'not_found_in_trash'    => 'No bookings found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false, // Not publicly queryable via URL
        'publicly_queryable' => false,
        'show_ui'            => true,  // Show in Admin
        'show_in_menu'       => true,
        'show_in_admin_bar'  => false, // Hide from "New" dropdown in Admin Bar
        'query_var'          => true,
        'rewrite'            => array('slug' => 'booking'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-store',
        'supports'           => array('title', 'custom-fields'), // Title = Company Name
    );

    register_post_type('cnc_booking', $args);
}
add_action('init', 'cnc_register_booking_cpt');

/**
 * Remove "Add New" submenu for Bookings
 * Admin should use the Floor Plan Manager instead.
 */
function cnc_remove_booking_add_new() {
    remove_submenu_page('edit.php?post_type=cnc_booking', 'post-new.php?post_type=cnc_booking');
}
add_action('admin_menu', 'cnc_remove_booking_add_new', 999);

/**
 * Add Custom Columns to Admin List
 */
function cnc_booking_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'Company Name',
        'stall_id' => 'Stall ID',
        'contact_person' => 'Contact Person',
        'phone' => 'Phone',
        'status' => 'Status',
        'date' => 'Date'
    );
    return $new_columns;
}
add_filter('manage_cnc_booking_posts_columns', 'cnc_booking_columns');

/**
 * Populate Custom Columns
 */
function cnc_booking_custom_column($column, $post_id) {
    switch ($column) {
        case 'stall_id':
            echo get_post_meta($post_id, 'cnc_stall_id', true);
            break;
        case 'contact_person':
            echo get_post_meta($post_id, 'cnc_contact_person', true);
            break;
        case 'phone':
            echo get_post_meta($post_id, 'cnc_phone', true);
            break;
        case 'status':
            $status = get_post_meta($post_id, 'cnc_booking_status', true);
            $color = ($status === 'approved') ? 'green' : (($status === 'rejected') ? 'red' : 'orange');
            echo "<span style='color:{$color}; font-weight:bold; text-transform:uppercase;'>{$status}</span>";
            break;
    }
}
add_action('manage_cnc_booking_posts_custom_column', 'cnc_booking_custom_column', 10, 2);

/**
 * Add Meta Box for Booking Details & Actions
 */
function cnc_add_booking_meta_boxes() {
    add_meta_box(
        'cnc_booking_details',
        'Booking Details',
        'cnc_render_booking_meta_box',
        'cnc_booking',
        'normal',
        'high'
    );
    
    add_meta_box(
        'cnc_whatsapp_logs',
        'WhatsApp Notification Logs',
        'cnc_render_whatsapp_log_meta_box',
        'cnc_booking',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'cnc_add_booking_meta_boxes');

function cnc_render_whatsapp_log_meta_box($post) {
    $logs = get_post_meta($post->ID, 'cnc_whatsapp_log', true);
    
    if (empty($logs) || !is_array($logs)) {
        echo '<p><em>No WhatsApp notifications recorded for this booking.</em></p>';
        return;
    }
    
    echo '<div style="max-height: 300px; overflow-y: auto; background: #f9f9f9; border: 1px solid #ddd; padding: 10px;">';
    echo '<table class="widefat striped">';
    echo '<thead><tr><th>Time</th><th>Type</th><th>To</th><th>Status</th><th>Message</th></tr></thead>';
    echo '<tbody>';
    
    // Show newest first
    $logs = array_reverse($logs);
    
    foreach ($logs as $log) {
        $time = isset($log['time']) ? $log['time'] : '-';
        $type = isset($log['type']) ? $log['type'] : '-';
        $to = isset($log['to']) ? $log['to'] : '-';
        $status = isset($log['status']) ? $log['status'] : '-';
        $msg = isset($log['message']) ? $log['message'] : '';
        $resp = isset($log['response']) ? $log['response'] : '';
        
        $status_color = ($status === 'success') ? 'green' : 'red';
        
        echo '<tr>';
        echo "<td>{$time}</td>";
        echo "<td>{$type}</td>";
        echo "<td>{$to}</td>";
        echo "<td style='color:{$status_color}; font-weight:bold;'>{$status}</td>";
        echo "<td><small>" . esc_html(print_r($msg, true)) . "</small>";
        if ($status !== 'success') {
            echo "<br><span style='color:red; font-size:10px;'>Error: " . esc_html(substr($resp, 0, 100)) . "</span>";
        }
        echo "</td>";
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
}

function cnc_render_booking_meta_box($post) {
    $stall_id = get_post_meta($post->ID, 'cnc_stall_id', true);
    $contact = get_post_meta($post->ID, 'cnc_contact_person', true);
    $phone = get_post_meta($post->ID, 'cnc_phone', true);
    $email = get_post_meta($post->ID, 'cnc_email', true);
    $status = get_post_meta($post->ID, 'cnc_booking_status', true);
    
    wp_nonce_field('cnc_save_booking_meta', 'cnc_booking_nonce');
    ?>
    <style>
        .cnc-row { margin-bottom: 15px; display: flex; align-items: center; }
        .cnc-label { width: 150px; font-weight: bold; }
        .cnc-input { width: 300px; padding: 5px; }
        .cnc-actions { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
        .btn-approve { background: #00a32a; color: #fff; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn-reject { background: #d63638; color: #fff; border: none; padding: 8px 15px; cursor: pointer; border-radius: 4px; text-decoration: none; display: inline-block; margin-left: 10px; }
    </style>
    
    <div class="cnc-row">
        <label class="cnc-label">Stall ID:</label>
        <input type="text" name="cnc_stall_id" value="<?php echo esc_attr($stall_id); ?>" class="cnc-input">
    </div>
    <div class="cnc-row">
        <label class="cnc-label">Contact Person:</label>
        <input type="text" name="cnc_contact_person" value="<?php echo esc_attr($contact); ?>" class="cnc-input">
    </div>
    <div class="cnc-row">
        <label class="cnc-label">Phone:</label>
        <input type="text" name="cnc_phone" value="<?php echo esc_attr($phone); ?>" class="cnc-input">
    </div>
    <div class="cnc-row">
        <label class="cnc-label">Email:</label>
        <input type="email" name="cnc_email" value="<?php echo esc_attr($email); ?>" class="cnc-input">
    </div>
    
    <div class="cnc-row">
        <label class="cnc-label">Current Status:</label>
        <select name="cnc_booking_status" class="cnc-input">
            <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
            <option value="approved" <?php selected($status, 'approved'); ?>>Approved</option>
            <option value="rejected" <?php selected($status, 'rejected'); ?>>Rejected</option>
        </select>
    </div>

    <div class="cnc-actions">
        <p><strong>Quick Actions:</strong> (Will save and trigger notifications)</p>
        <button type="submit" name="cnc_action" value="approve" class="btn-approve">✅ Approve & Notify</button>
        <button type="submit" name="cnc_action" value="reject" class="btn-reject">❌ Reject & Notify</button>
    </div>
    <?php
}

/**
 * Save Meta Data & Handle Actions
 */
function cnc_save_booking_meta($post_id) {
    if (!isset($_POST['cnc_booking_nonce']) || !wp_verify_nonce($_POST['cnc_booking_nonce'], 'cnc_save_booking_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save Fields
    $fields = ['cnc_stall_id', 'cnc_contact_person', 'cnc_phone', 'cnc_email', 'cnc_booking_status'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Handle Quick Actions
    if (isset($_POST['cnc_action'])) {
        $action = $_POST['cnc_action'];
        $new_status = ($action === 'approve') ? 'approved' : 'rejected';
        update_post_meta($post_id, 'cnc_booking_status', $new_status);
        
        // Trigger Notifications (Placeholder for now)
        cnc_trigger_booking_notification($post_id, $new_status);
    }

    // Update Global Cache of Booked Stalls
    cnc_update_booked_stalls_cache();
}
add_action('save_post', 'cnc_save_booking_meta');

/**
 * Rebuild Booked Stalls Cache
 * Scans all 'approved' AND 'pending' bookings and updates the global option for the frontend map.
 * This prevents double-booking while a request is under review.
 */
function cnc_update_booked_stalls_cache() {
    $args = [
        'post_type'      => 'cnc_booking',
        'meta_query'     => [
            [
                'key'     => 'cnc_booking_status',
                'value'   => ['approved', 'pending'],
                'compare' => 'IN'
            ]
        ],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'any'
    ];
    
    $query = new WP_Query($args);
    $booked_stalls = [];
    
    if ($query->have_posts()) {
        foreach ($query->posts as $pid) {
            $stall = get_post_meta($pid, 'cnc_stall_id', true);
            if ($stall) {
                $booked_stalls[] = $stall;
            }
        }
    }
    
    update_option('cnc_booked_stalls', array_unique($booked_stalls));
}

/**
 * Notification Trigger
 */
function cnc_trigger_booking_notification($post_id, $status) {
    $phone = get_post_meta($post_id, 'cnc_phone', true);
    $stall = get_post_meta($post_id, 'cnc_stall_id', true);
    $company = get_the_title($post_id);
    
    if ($status === 'approved') {
        $msg = "Hello! Your booking for Stall $stall at CNC Expo 2026 has been APPROVED. We will contact you shortly for the next steps.";
    } elseif ($status === 'rejected') {
        $msg = "Hello. Your booking request for Stall $stall at CNC Expo 2026 has been declined. Please contact us for more info.";
    } else {
        return;
    }
    
    // Call WhatsApp API here
    if (class_exists('CNC_WhatsApp_API')) {
        // CNC_WhatsApp_API::send_message($phone, $msg);
        error_log("WhatsApp Notification to $phone: $msg");
    }
}
