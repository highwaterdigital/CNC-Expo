<?php
/**
 * CNC Exhibitor Post Type
 * Registers the 'cnc_exhibitor' custom post type and manages its admin columns.
 */

if (!defined('ABSPATH')) exit;

function cnc_register_exhibitor_cpt() {
    $labels = array(
        'name'                  => 'Exhibitors',
        'singular_name'         => 'Exhibitor',
        'menu_name'             => 'Exhibitors',
        'name_admin_bar'        => 'Exhibitor',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Exhibitor',
        'new_item'              => 'New Exhibitor',
        'edit_item'             => 'Edit Exhibitor',
        'view_item'             => 'View Exhibitor',
        'all_items'             => 'All Exhibitors',
        'search_items'          => 'Search Exhibitors',
        'not_found'             => 'No exhibitors found.',
        'not_found_in_trash'    => 'No exhibitors found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'exhibitor'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-groups',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
    );

    register_post_type('cnc_exhibitor', $args);
}
add_action('init', 'cnc_register_exhibitor_cpt');

/**
 * Add Custom Columns to Admin List
 */
function cnc_exhibitor_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'thumbnail' => 'Logo',
        'title' => 'Company Name',
        'stall_number' => 'Stall',
        'contact_email' => 'Email',
        'booking_status' => 'Booking Status',
        'date' => 'Date'
    );
    return $new_columns;
}
add_filter('manage_cnc_exhibitor_posts_columns', 'cnc_exhibitor_columns');

/**
 * Populate Custom Columns
 */
function cnc_exhibitor_custom_column($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo '<span style="color:#ccc;">No Logo</span>';
            }
            break;
        case 'stall_number':
            echo get_post_meta($post_id, 'cnc_stall_number', true);
            break;
        case 'contact_email':
            echo get_post_meta($post_id, 'cnc_contact_email', true);
            break;
        case 'booking_status':
            $booking_id = get_post_meta($post_id, 'cnc_linked_booking_id', true);
            if ($booking_id) {
                $status = get_post_meta($booking_id, 'cnc_booking_status', true);
                $color = ($status === 'approved') ? 'green' : (($status === 'rejected') ? 'red' : 'orange');
                echo "<a href='post.php?post={$booking_id}&action=edit' style='color:{$color}; font-weight:bold;'>Linked ({$status})</a>";
            } else {
                echo '<span style="color:gray;">Not Linked</span>';
            }
            break;
    }
}
add_action('manage_cnc_exhibitor_posts_custom_column', 'cnc_exhibitor_custom_column', 10, 2);

/**
 * Add Meta Boxes
 */
function cnc_add_exhibitor_meta_boxes() {
    add_meta_box(
        'cnc_exhibitor_details',
        'Exhibitor Details',
        'cnc_render_exhibitor_meta_box',
        'cnc_exhibitor',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cnc_add_exhibitor_meta_boxes');

function cnc_render_exhibitor_meta_box($post) {
    $email = get_post_meta($post->ID, 'cnc_contact_email', true);
    $phone = get_post_meta($post->ID, 'cnc_contact_phone', true);
    $website = get_post_meta($post->ID, 'cnc_website', true);
    $stall = get_post_meta($post->ID, 'cnc_stall_number', true);
    $booking_id = get_post_meta($post->ID, 'cnc_linked_booking_id', true);
    
    wp_nonce_field('cnc_save_exhibitor_meta', 'cnc_exhibitor_nonce');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="cnc_contact_email">Contact Email</label></th>
            <td><input type="email" name="cnc_contact_email" id="cnc_contact_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="cnc_contact_phone">Phone</label></th>
            <td><input type="text" name="cnc_contact_phone" id="cnc_contact_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="cnc_website">Website</label></th>
            <td><input type="url" name="cnc_website" id="cnc_website" value="<?php echo esc_attr($website); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="cnc_stall_number">Stall Number</label></th>
            <td>
                <input type="text" name="cnc_stall_number" id="cnc_stall_number" value="<?php echo esc_attr($stall); ?>" class="regular-text">
                <p class="description">Synced from Booking if linked.</p>
            </td>
        </tr>
        <tr>
            <th><label>Linked Booking</label></th>
            <td>
                <?php if ($booking_id): ?>
                    <a href="<?php echo get_edit_post_link($booking_id); ?>" class="button">View Linked Booking (#<?php echo $booking_id; ?>)</a>
                <?php else: ?>
                    <span class="description">No booking linked yet. Use the Exhibitor Manager to sync.</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <?php
}

function cnc_save_exhibitor_meta($post_id) {
    if (!isset($_POST['cnc_exhibitor_nonce']) || !wp_verify_nonce($_POST['cnc_exhibitor_nonce'], 'cnc_save_exhibitor_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = ['cnc_contact_email', 'cnc_contact_phone', 'cnc_website', 'cnc_stall_number'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'cnc_save_exhibitor_meta');
