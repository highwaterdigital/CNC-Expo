<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Ensure Logger is available
require_once get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php';

/**
 * Unified booking processor
 * AJAX: wp_ajax_(nopriv_)cnc_process_booking_request
 */
add_action('wp_ajax_cnc_process_booking_request', 'cnc_process_booking_request');
add_action('wp_ajax_nopriv_cnc_process_booking_request', 'cnc_process_booking_request');
function cnc_process_booking_request() {
    // Log start of request
    if (class_exists('cnc_Registration_Logger')) {
        cnc_Registration_Logger::log('booking_attempt', 'Booking request received', [
            'post_data' => $_POST,
            'user_id'   => get_current_user_id()
        ]);
    }

    check_ajax_referer('cnc_booking_action', 'cnc_booking_nonce');

    $stall_ids_raw = isset($_POST['stall_id']) ? $_POST['stall_id'] : ($_POST['selected_stall_id'] ?? '');
    $company_name  = sanitize_text_field($_POST['company_name'] ?? '');
    $contact_person= sanitize_text_field($_POST['contact_person'] ?? '');
    $phone_raw     = sanitize_text_field($_POST['phone'] ?? '');
    $email         = sanitize_email($_POST['email'] ?? '');
    $clean_phone   = preg_replace('/\D+/', '', $phone_raw);

    if (empty($stall_ids_raw) || empty($company_name) || empty($phone_raw)) {
        wp_send_json_error(['message' => 'Please fill all required fields.']);
    }

    // Block duplicate registrations by phone/email and prompt login instead.
    $duplicate_meta = ['relation' => 'OR'];
    if (!empty($clean_phone)) {
        $duplicate_meta[] = [
            'key'     => 'cnc_phone',
            'value'   => $clean_phone,
            'compare' => 'LIKE',
        ];
    }
    if (!empty($email)) {
        $duplicate_meta[] = [
            'key'     => 'cnc_email',
            'value'   => $email,
            'compare' => 'LIKE',
        ];
    }
    if (count($duplicate_meta) > 1) {
        $existing_booking = new WP_Query([
            'post_type'      => 'cnc_booking',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'meta_query'     => $duplicate_meta,
            'fields'         => 'ids',
        ]);

        if (!empty($existing_booking->posts)) {
            wp_send_json_error([
                'message'      => 'You are already registered with these details. Please log in to your account.',
                'redirect_url' => home_url('/my-account/dashboard/'),
            ]);
        }
    }

    $stall_ids = array_values(array_filter(array_unique(array_map('trim', explode(',', $stall_ids_raw)))));
    if (empty($stall_ids)) {
        wp_send_json_error(['message' => 'Please select at least one stall.']);
    }

    $user_result = cnc_booking_get_or_create_user($phone_raw, $email, $contact_person, $company_name);
    if (is_wp_error($user_result)) {
        wp_send_json_error(['message' => $user_result->get_error_message()]);
    }
    $user_id = $user_result['user_id'];
    $user    = get_userdata($user_id);
    if ($user) {
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', $user->user_login, $user);
    }

    $created_bookings = [];
    $booked_stalls    = [];

    foreach ($stall_ids as $stall_id) {
        if (empty($stall_id)) {
            continue;
        }
        if (cnc_booking_find_existing_stall($stall_id)) {
            continue;
        }

        $post_id = wp_insert_post([
            'post_title'   => $company_name . ' - Stall ' . $stall_id,
            'post_type'    => 'cnc_booking',
            'post_status'  => 'publish',
            'post_author'  => $user_id,
        ]);

        if ($post_id) {
            update_post_meta($post_id, 'cnc_stall_id', $stall_id);
            update_post_meta($post_id, 'cnc_company_name', $company_name);
            update_post_meta($post_id, 'cnc_contact_person', $contact_person);
            update_post_meta($post_id, 'cnc_phone', $phone_raw);
            update_post_meta($post_id, 'cnc_email', $email);
            update_post_meta($post_id, 'cnc_booking_status', 'pending');
            update_post_meta($post_id, 'cnc_user_id', $user_id);
            update_post_meta($post_id, 'cnc_addons', [$stall_id => []]);

            $booked_stalls[]    = $stall_id;
            $created_bookings[] = $post_id;
        }
    }

    if (empty($created_bookings)) {
        wp_send_json_error(['message' => 'Failed to create booking or stall already taken.']);
    }

    if (function_exists('cnc_update_booked_stalls_cache')) {
        cnc_update_booked_stalls_cache();
    }

    cnc_booking_notify_whatsapp($phone_raw, $booked_stalls, $company_name, $created_bookings);
    cnc_booking_sync_to_sheets($created_bookings, $booked_stalls, $company_name, $contact_person, $phone_raw, $email, $user_id);

    // Log success
    if (class_exists('cnc_Registration_Logger')) {
        cnc_Registration_Logger::log('booking_success', 'Booking processed successfully', [
            'booking_ids' => $created_bookings,
            'stalls'      => $booked_stalls,
            'user_id'     => $user_id
        ]);
    }

    wp_send_json_success([
        'message'       => 'Booking submitted successfully!',
        'booked_stalls' => $booked_stalls,
        'booking_ids'   => $created_bookings,
        'redirect_url'  => home_url('/my-account/dashboard/'),
    ]);
}

// Backward compatibility
add_action('wp_ajax_cnc_book_stall', 'cnc_process_booking_request');
add_action('wp_ajax_nopriv_cnc_book_stall', 'cnc_process_booking_request');

/**
 * Find or create a subscriber user for the booking.
 */
function cnc_booking_get_or_create_user($phone, $email, $contact_person, $company_name) {
    $clean_phone = preg_replace('/\D+/', '', $phone);

    // Block attempts to register with privileged accounts.
    if (!empty($email)) {
        $priv_user = get_user_by('email', $email);
        if ($priv_user && $priv_user->has_cap('manage_options')) {
            return new WP_Error('forbidden_email', 'This email is reserved. Please use a different email to register.');
        }
    }
    if (!empty($clean_phone)) {
        $priv_by_phone = get_users([
            'meta_key'   => 'cnc_phone',
            'meta_value' => $clean_phone,
            'number'     => 1,
            'fields'     => 'ID',
        ]);
        if (!empty($priv_by_phone)) {
            $priv_user = get_user_by('id', (int) $priv_by_phone[0]);
            if ($priv_user && $priv_user->has_cap('manage_options')) {
                return new WP_Error('forbidden_phone', 'This phone number is reserved. Please use a different phone number to register.');
            }
        }

        $priv_by_login = get_user_by('login', $clean_phone);
        if ($priv_by_login && $priv_by_login->has_cap('manage_options')) {
            return new WP_Error('forbidden_phone', 'This phone number is reserved. Please use a different phone number to register.');
        }
    }

    // 1. Try by phone meta
    $existing = get_users([
        'meta_key'   => 'cnc_phone',
        'meta_value' => $clean_phone,
        'number'     => 1,
        'fields'     => 'ID',
    ]);
    if (!empty($existing)) {
        $uid = (int) $existing[0];
        $user = get_userdata($uid);
        if ($user && !$user->has_cap('manage_options') && !in_array('customer', (array) $user->roles, true)) {
            $user->set_role('customer');
        }
        return ['user_id' => $uid, 'created' => false];
    }

    // 2. Try by email
    if (!empty($email)) {
        $by_email = get_user_by('email', $email);
        if ($by_email) {
            $uid = (int) $by_email->ID;
            $user = get_userdata($uid);
            if ($user && !$user->has_cap('manage_options') && !in_array('customer', (array) $user->roles, true)) {
                $user->set_role('customer');
            }
            return ['user_id' => $uid, 'created' => false];
        }
    }

    // 3. Try by username (=phone)
    if (!empty($clean_phone)) {
        $by_login = get_user_by('login', $clean_phone);
        if ($by_login) {
            $uid = (int) $by_login->ID;
            $user = get_userdata($uid);
            if ($user && !$user->has_cap('manage_options') && !in_array('customer', (array) $user->roles, true)) {
                $user->set_role('customer');
            }
            return ['user_id' => $uid, 'created' => false];
        }
    }

    // Create new user
    $username = !empty($clean_phone) ? $clean_phone : 'cnc_' . wp_generate_password(6, false);
    $password = wp_generate_password(12, true);
    $user_email = !empty($email) ? $email : $username . '@cncexpo.com';

    $user_id = wp_insert_user([
        'user_login'   => $username,
        'user_pass'    => $password,
        'user_email'   => $user_email,
        'role'         => 'customer',
        'display_name' => $company_name ?: $contact_person ?: $username,
    ]);

    if (is_wp_error($user_id)) {
        return $user_id;
    }

    update_user_meta($user_id, 'cnc_phone', $clean_phone);
    update_user_meta($user_id, 'cnc_company_name', $company_name);
    update_user_meta($user_id, 'first_name', $contact_person);

    return ['user_id' => (int) $user_id, 'created' => true];
}

/**
 * Check if stall already has a pending/approved booking.
 */
function cnc_booking_find_existing_stall($stall_id) {
    $query = new WP_Query([
        'post_type'      => 'cnc_booking',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
        'meta_query'     => [
            [
                'key'     => 'cnc_stall_id',
                'value'   => $stall_id,
                'compare' => '='
            ],
            [
                'key'     => 'cnc_booking_status',
                'value'   => ['pending', 'approved'],
                'compare' => 'IN'
            ]
        ]
    ]);

    return !empty($query->posts) ? (int) $query->posts[0] : 0;
}

/**
 * Send WhatsApp confirmation for booking received.
 */
function cnc_booking_notify_whatsapp($phone, $stalls, $company_name, $post_ids = []) {
    if (!class_exists('cnc_WhatsApp_API')) {
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('whatsapp_error', 'WhatsApp API class not found');
        }
        return;
    }

    $stall_list = implode(', ', $stalls);
    $primary_post_id = !empty($post_ids) ? $post_ids[0] : null;
    
    // Template: cnc_stall_pending
    // Params: {{1}}=Name, {{2}}=Stall Number
    $template_name = 'cnc_stall_pending';
    $components = [
        [
            'type' => 'body',
            'parameters' => [
                ['type' => 'text', 'text' => $company_name],
                ['type' => 'text', 'text' => $stall_list]
            ]
        ]
    ];

    try {
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('whatsapp_sending', 'Sending WhatsApp template', [
                'phone' => $phone,
                'template' => $template_name,
                'components' => $components
            ]);
        }

        if (method_exists('cnc_WhatsApp_API', 'send_template')) {
            $result = cnc_WhatsApp_API::send_template($phone, $template_name, 'en', $components, ['post_id' => $primary_post_id]);
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('whatsapp_sent', 'WhatsApp template sent', ['result' => $result]);
            }
        }
        
        // Notify Admin as well (optional, but good practice)
        $admin_phone = cnc_WhatsApp_API::get_admin_phone();
        $admin_msg = "New Booking Request:\nCompany: $company_name\nStalls: $stall_list\nPhone: $phone";
        cnc_WhatsApp_API::send_message($admin_phone, $admin_msg);

    } catch (Exception $e) {
        error_log('CNC WhatsApp booking notify failed: ' . $e->getMessage());
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('whatsapp_fail', 'WhatsApp send failed', ['error' => $e->getMessage()]);
        }
    }
}

/**
 * Send booking data to Google Sheets.
 */
function cnc_booking_sync_to_sheets($booking_ids, $stalls, $company_name, $contact_person, $phone, $email, $user_id) {
    if (!class_exists('cnc_Sheet_Sync')) {
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('sheet_error', 'Sheet Sync class not found');
        }
        return;
    }

    foreach ($booking_ids as $index => $post_id) {
        $stall_id = $stalls[$index] ?? $stalls[0];
        $payload  = [
            'action' => 'booking_created',
            'data'   => [
                'wp_post_id' => $post_id,
                'stall_id'   => $stall_id,
                'company'    => $company_name,
                'contact'    => $contact_person,
                'phone'      => $phone,
                'email'      => $email,
                'status'     => 'pending',
                'user_id'    => $user_id,
            ],
        ];

        try {
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('sheet_syncing', 'Syncing booking to Sheets', ['payload' => $payload]);
            }

            $response = cnc_Sheet_Sync::send_data_to_sheet($payload);

            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('sheet_synced', 'Booking synced to Sheets', ['response' => $response]);
            }

        } catch (Exception $e) {
            error_log('CNC Sheets sync failed for booking ' . $post_id . ': ' . $e->getMessage());
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('sheet_fail', 'Sheet sync failed', ['error' => $e->getMessage()]);
            }
        }
    }
}
