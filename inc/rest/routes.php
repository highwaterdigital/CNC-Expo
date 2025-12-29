<?php
if (!defined('ABSPATH')) exit;

// Ensure Logger is available
require_once get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php';

add_action('rest_api_init', function () {
    register_rest_route('expo/v1', '/customer/login', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_login_handler',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('expo/v1', '/customer/me', [
        'methods' => 'GET',
        'callback' => 'cnc_expo_me_handler',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('expo/v1', '/customer/update', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_update_handler',
        'permission_callback' => '__return_true',
    ]);

    // Admin Routes
    register_rest_route('expo/v1', '/admin/bookings', [
        'methods' => 'GET',
        'callback' => 'cnc_expo_admin_bookings_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);

    register_rest_route('expo/v1', '/admin/approved', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_admin_approve_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);

    register_rest_route('expo/v1', '/admin/rejected', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_admin_reject_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);

    register_rest_route('expo/v1', '/admin/update', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_admin_update_booking_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);

    register_rest_route('expo/v1', '/admin/profile', [
        'methods' => 'POST',
        'callback' => 'cnc_expo_admin_profile_update_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);

    register_rest_route('expo/v1', '/admin/me', [
        'methods' => 'GET',
        'callback' => 'cnc_expo_admin_me_handler',
        'permission_callback' => function() { return current_user_can('manage_options'); },
    ]);
});

function cnc_expo_admin_me_handler() {
    $user = wp_get_current_user();
    return [
        'id' => $user->ID,
        'email' => $user->user_email,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'display_name' => $user->display_name
    ];
}

function cnc_expo_admin_profile_update_handler($request) {
    $user = wp_get_current_user();
    $params = $request->get_json_params();
    
    $args = ['ID' => $user->ID];
    
    if (isset($params['email']) && is_email($params['email'])) {
        $args['user_email'] = sanitize_email($params['email']);
    }
    if (isset($params['first_name'])) {
        $args['first_name'] = sanitize_text_field($params['first_name']);
    }
    if (isset($params['last_name'])) {
        $args['last_name'] = sanitize_text_field($params['last_name']);
    }
    
    $result = wp_update_user($args);
    
    if (is_wp_error($result)) {
        return $result;
    }
    
    return ['success' => true, 'message' => 'Profile updated successfully'];
}

function cnc_expo_admin_bookings_handler() {
    $args = [
        'post_type' => 'cnc_booking',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ];
    $query = new WP_Query($args);
    $bookings = [];
    foreach ($query->posts as $post) {
        $meta = get_post_meta($post->ID);
        $stalls = isset($meta['cnc_stall_id']) ? (is_array($meta['cnc_stall_id']) ? $meta['cnc_stall_id'] : [$meta['cnc_stall_id']]) : [];
        $bookings[] = [
            'id' => $post->ID,
            'company' => $meta['cnc_company_name'][0] ?? '',
            'contact' => $meta['cnc_contact_person'][0] ?? '',
            'phone' => $meta['cnc_phone'][0] ?? '',
            'email' => $meta['cnc_email'][0] ?? '',
            'stalls' => $stalls,
            'status' => $meta['cnc_booking_status'][0] ?? 'pending',
        ];
    }
    return $bookings;
}

function cnc_expo_admin_approve_handler($request) {
    $id = $request->get_param('id');
    if (!$id) return new WP_Error('missing_id', 'ID required', ['status' => 400]);
    
    update_post_meta($id, 'cnc_booking_status', 'approved');
    // Logic to update stall status in floorplan data (if stored in DB) would go here
    
    return ['success' => true];
}

function cnc_expo_admin_reject_handler($request) {
    $id = $request->get_param('id');
    if (!$id) return new WP_Error('missing_id', 'ID required', ['status' => 400]);
    
    update_post_meta($id, 'cnc_booking_status', 'rejected');
    
    return ['success' => true];
}

function cnc_expo_admin_update_booking_handler($request) {
    $params = $request->get_json_params();
    $id = $params['id'] ?? 0;
    
    if (!$id) return new WP_Error('missing_id', 'ID required', ['status' => 400]);

    if (isset($params['company'])) update_post_meta($id, 'cnc_company_name', sanitize_text_field($params['company']));
    if (isset($params['contact'])) update_post_meta($id, 'cnc_contact_person', sanitize_text_field($params['contact']));
    if (isset($params['phone'])) update_post_meta($id, 'cnc_phone', sanitize_text_field($params['phone']));
    if (isset($params['email'])) update_post_meta($id, 'cnc_email', sanitize_email($params['email']));
    if (isset($params['status'])) update_post_meta($id, 'cnc_booking_status', sanitize_text_field($params['status']));
    
    // Handle stall updates if needed (complex because it might involve floorplan data)
    // For now, we assume stall IDs are managed via the floorplan designer or direct DB edits if critical.
    
    return ['success' => true, 'message' => 'Booking updated'];
}

function cnc_expo_login_handler($request) {
    $params = $request->get_json_params();
    $email = sanitize_email($params['email'] ?? '');
    $phone = sanitize_text_field($params['phone'] ?? '');
    $otp = sanitize_text_field($params['otp'] ?? '');

    if (empty($email) || empty($phone)) {
        return new WP_Error('missing_params', 'Email and Phone are required', ['status' => 400]);
    }

    // Find Booking
    $args = [
        'post_type' => 'cnc_booking',
        'meta_query' => [
            'relation' => 'AND',
            ['key' => 'cnc_email', 'value' => $email],
            ['key' => 'cnc_phone', 'value' => $phone],
        ],
        'posts_per_page' => 1,
    ];
    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return new WP_Error('not_found', 'No booking found with these details.', ['status' => 404]);
    }
    $booking_id = $query->posts[0]->ID;
    $company    = get_post_meta($booking_id, 'cnc_company_name', true);
    $contact    = get_post_meta($booking_id, 'cnc_contact_person', true);

    // OTP Logic
    $transient_key = 'cnc_otp_' . md5($email . $phone);
    
    if (empty($otp)) {
        // Generate OTP
        $new_otp = rand(100000, 999999);
        set_transient($transient_key, $new_otp, 10 * MINUTE_IN_SECONDS);
        
        // Log OTP for debugging
        error_log("CNC OTP for $email: $new_otp");
        
        // Send via WhatsApp if available
        if (class_exists('cnc_WhatsApp_API')) {
             try {
                cnc_WhatsApp_API::send_message($phone, "Your CNC Expo Login OTP is: $new_otp");
             } catch (Exception $e) {
                 error_log("Failed to send OTP: " . $e->getMessage());
             }
        }

        return ['success' => true, 'message' => 'OTP sent'];
    } else {
        // Verify OTP
        $saved_otp = get_transient($transient_key);
        if ($otp != $saved_otp) {
             return new WP_Error('invalid_otp', 'Invalid OTP', ['status' => 400]);
        }

        // Ensure the booking has an associated WP user
        $user_id = (int) get_post_field('post_author', $booking_id);
        if (!$user_id) {
            $user_result = cnc_booking_get_or_create_user($phone, $email, $contact, $company);
            if (is_wp_error($user_result)) {
                return $user_result;
            }
            $user_id = (int) $user_result['user_id'];
            wp_update_post([
                'ID'          => $booking_id,
                'post_author' => $user_id,
            ]);
            update_post_meta($booking_id, 'cnc_user_id', $user_id);
        }

        // Log the user in
        $user = get_userdata($user_id);
        if ($user) {
            if ($user->has_cap('manage_options')) {
                return new WP_Error('forbidden_user', 'This account cannot be used to sign in here.', ['status' => 403]);
            }
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);
            do_action('wp_login', $user->user_login, $user);
        }

        // Generate Token
        $token = bin2hex(random_bytes(32));
        set_transient('cnc_session_' . $token, $booking_id, 24 * HOUR_IN_SECONDS);

        return [
            'success' => true,
            'token'   => $token,
            'redirect_url' => home_url('/my-account/dashboard/'),
        ];
    }
}

function cnc_expo_me_handler($request) {
    $token = $request->get_header('X-Expo-Session');
    $user_id = get_current_user_id();
    $booking_id = 0;

    if ($user_id) {
        $booking_id = cnc_expo_find_booking_for_user($user_id);
    }

    if (!$booking_id && !empty($token)) {
        $booking_id = get_transient('cnc_session_' . $token);
    }

    if (!$booking_id) {
        return new WP_Error('invalid_token', 'Invalid or expired session', ['status' => 401]);
    }
    
    $meta = get_post_meta($booking_id);

    // Handle stall IDs which might be single value or array
    $stall_ids = [];
    if (isset($meta['cnc_stall_id'])) {
        foreach ($meta['cnc_stall_id'] as $sid) {
            $stall_ids[] = $sid;
        }
    }

    $addons_raw = get_post_meta($booking_id, 'cnc_addons', true);
    $addons_map = is_array($addons_raw) ? $addons_raw : [];

    return [
        'booking_id' => $booking_id,
        'company'    => $meta['cnc_company_name'][0] ?? '',
        'contact'    => $meta['cnc_contact_person'][0] ?? '',
        'phone'      => $meta['cnc_phone'][0] ?? '',
        'email'      => $meta['cnc_email'][0] ?? '',
        'stall_ids'  => $stall_ids,
        'status'     => $meta['cnc_booking_status'][0] ?? 'pending',
        'addons'     => $addons_map,
        'profile' => [
            'company'  => $meta['cnc_company_name'][0] ?? '',
            'gst'      => $meta['cnc_gst'][0] ?? '',
            'pan'      => $meta['cnc_pan'][0] ?? '',
            'address'  => $meta['cnc_address'][0] ?? '',
            'logo_url' => $meta['cnc_logo_url'][0] ?? '',
        ],
        'quotation_url' => $meta['_cnc_quotation_url'][0] ?? '',
    ];
}

function cnc_expo_update_handler($request) {
    $nonce = $request->get_header('X-WP-Nonce');
    if ($nonce && !wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('invalid_nonce', 'Invalid security token', ['status' => 403]);
    }

    $token = $request->get_header('X-Expo-Session');
    $user_id = get_current_user_id();
    $booking_id = $user_id ? cnc_expo_find_booking_for_user($user_id) : 0;

    if (!$booking_id && !empty($token)) {
        $booking_id = get_transient('cnc_session_' . $token);
    }

    if (!$booking_id) {
        return new WP_Error('invalid_token', 'Invalid or expired session', ['status' => 401]);
    }
    
    $params = $request->get_json_params();
    $meta = get_post_meta($booking_id);
    $stall_ids = [];
    if (isset($meta['cnc_stall_id'])) {
        foreach ($meta['cnc_stall_id'] as $sid) {
            $stall_ids[] = $sid;
        }
    }

    // Handle logo upload (base64)
    $params = $request->get_json_params();
    if (!empty($params['logo_data'])) {
        $upload = cnc_expo_save_base64_logo($params['logo_data'], $params['logo_name'] ?? 'logo.png');
        if (is_wp_error($upload)) {
            return $upload;
        }
        $params['logo_url'] = $upload['url'];
    }

    // Profile fields
    $profile_updated = false;
    if (isset($params['company'])) {
        update_post_meta($booking_id, 'cnc_company_name', sanitize_text_field($params['company']));
        if ($user_id) {
            update_user_meta($user_id, 'cnc_company_name', sanitize_text_field($params['company']));
        }
        $profile_updated = true;
    }
    if (isset($params['gst'])) {
        update_post_meta($booking_id, 'cnc_gst', sanitize_text_field($params['gst']));
        if ($user_id) {
            update_user_meta($user_id, 'cnc_gst', sanitize_text_field($params['gst']));
        }
        $profile_updated = true;
    }
    if (isset($params['pan'])) {
        update_post_meta($booking_id, 'cnc_pan', sanitize_text_field($params['pan']));
        $profile_updated = true;
    }
    if (isset($params['address'])) {
        update_post_meta($booking_id, 'cnc_address', sanitize_textarea_field($params['address']));
        $profile_updated = true;
    }
    if (isset($params['logo_url'])) {
        $logo_url = esc_url_raw($params['logo_url']);
        update_post_meta($booking_id, 'cnc_logo_url', $logo_url);
        update_post_meta($booking_id, 'cnc_company_logo', $logo_url);
        if ($user_id) {
            update_user_meta($user_id, 'cnc_company_logo', $logo_url);
        }
        $profile_updated = true;
    }
    if (isset($params['contact'])) {
        update_post_meta($booking_id, 'cnc_contact_person', sanitize_text_field($params['contact']));
        $profile_updated = true;
    }
    if (isset($params['phone'])) {
        $clean_phone = sanitize_text_field($params['phone']);
        update_post_meta($booking_id, 'cnc_phone', $clean_phone);
        if ($user_id) {
            update_user_meta($user_id, 'cnc_phone', preg_replace('/\D+/', '', $clean_phone));
        }
        $profile_updated = true;
    }
    if (isset($params['email'])) {
        $email_clean = sanitize_email($params['email']);
        update_post_meta($booking_id, 'cnc_email', $email_clean);
        if ($user_id && is_email($email_clean)) {
            wp_update_user([
                'ID'         => $user_id,
                'user_email' => $email_clean,
            ]);
        }
        $profile_updated = true;
    }

    // Sync Profile Update to Sheets
    if ($profile_updated && class_exists('cnc_Sheet_Sync')) {
        try {
            $payload = [
                'action' => 'update_booking',
                'data'   => [
                    'wp_post_id' => $booking_id,
                    'company'    => get_post_meta($booking_id, 'cnc_company_name', true),
                    'contact'    => get_post_meta($booking_id, 'cnc_contact_person', true),
                    'phone'      => get_post_meta($booking_id, 'cnc_phone', true),
                    'email'      => get_post_meta($booking_id, 'cnc_email', true),
                ],
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('sheet_syncing', 'Syncing profile update', ['payload' => $payload]);
            }
            
            cnc_Sheet_Sync::send_data_to_sheet($payload);
            
        } catch (Exception $e) {
            error_log('CNC Sheets profile sync failed: ' . $e->getMessage());
        }
    }

    // Addons
    if (isset($params['addons']) && is_array($params['addons'])) {
        $addons_clean = array_map(function($val) {
            return is_numeric($val) ? intval($val) : sanitize_text_field($val);
        }, $params['addons']);

        $addons_map = get_post_meta($booking_id, 'cnc_addons', true);
        if (!is_array($addons_map)) {
            $addons_map = [];
        }

        $stall_key = $params['stall_id'] ?? ($stall_ids[0] ?? 'default');
        $addons_map[$stall_key] = $addons_clean;

        update_post_meta($booking_id, 'cnc_addons', $addons_map);

        if (class_exists('cnc_Sheet_Sync')) {
            try {
                cnc_Sheet_Sync::send_data_to_sheet([
                    'action' => 'addons_update',
                    'data'   => [
                        'wp_post_id' => $booking_id,
                        'addons'     => $addons_clean,
                        'status'     => get_post_meta($booking_id, 'cnc_booking_status', true),
                        'stall_id'   => $stall_key,
                        'company'    => get_post_meta($booking_id, 'cnc_company_name', true),
                    ],
                ]);
            } catch (Exception $e) {
                error_log('CNC Sheets addons sync failed: ' . $e->getMessage());
            }
        }
    }

    return ['success' => true, 'message' => 'Profile updated'];
}

function cnc_expo_save_base64_logo($data, $filename = 'logo.png') {
    if (empty($data)) {
        return new WP_Error('invalid_logo', 'Logo data missing', ['status' => 400]);
    }

    // Parse data URL
    if (!preg_match('/^data:image\\/(png|jpe?g|gif|webp);base64,/', $data, $mime)) {
        return new WP_Error('invalid_logo', 'Unsupported logo format', ['status' => 400]);
    }

    $img_data = substr($data, strpos($data, ',') + 1);
    $binary   = base64_decode($img_data);
    if ($binary === false) {
        return new WP_Error('invalid_logo', 'Could not decode logo image', ['status' => 400]);
    }

    if (strlen($binary) > 3 * 1024 * 1024) { // 3MB limit
        return new WP_Error('invalid_logo', 'Logo file too large. Max 3MB.', ['status' => 400]);
    }

    $safe_name = sanitize_file_name($filename);
    if (empty($safe_name)) {
        $safe_name = 'logo-' . time() . '.png';
    }

    if (!function_exists('wp_upload_bits')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $upload = wp_upload_bits($safe_name, null, $binary);
    if (!empty($upload['error'])) {
        return new WP_Error('upload_error', $upload['error'], ['status' => 500]);
    }

    return $upload;
}

/**
 * Find the latest booking for a given user.
 */
function cnc_expo_find_booking_for_user($user_id) {
    $query = new WP_Query([
        'post_type'      => 'cnc_booking',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'post_status'    => 'publish',
        'author'         => $user_id,
        'fields'         => 'ids',
    ]);

    if (!empty($query->posts)) {
        return (int) $query->posts[0];
    }

    // Fallback to meta linkage
    $meta_query = new WP_Query([
        'post_type'      => 'cnc_booking',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'   => 'cnc_user_id',
                'value' => $user_id,
            ],
        ],
    ]);

    return !empty($meta_query->posts) ? (int) $meta_query->posts[0] : 0;
}
