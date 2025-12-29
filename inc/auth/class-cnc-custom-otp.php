<?php
/**
 * Custom OTP verifier for expo login.
 * Verifies OTP and logs the user in by phone/email.
 */
if (!defined('ABSPATH')) { exit; }

add_action('wp_ajax_nopriv_cnc_verify_custom_otp', 'cnc_verify_custom_otp');
add_action('wp_ajax_cnc_verify_custom_otp', 'cnc_verify_custom_otp');

function cnc_verify_custom_otp() {
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'cnc_login_nonce')) {
        wp_send_json_error(['message' => 'Invalid request']);
    }

    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $otp   = sanitize_text_field($_POST['otp'] ?? '');

    if (empty($phone) || empty($otp)) {
        wp_send_json_error(['message' => 'Phone and OTP are required']);
    }

    $transient_key = 'cnc_otp_' . md5($email . $phone);
    $saved_otp = get_transient($transient_key);
    if (!$saved_otp || $otp != $saved_otp) {
        wp_send_json_error(['message' => 'Invalid OTP']);
    }

    // Find user by phone meta or email
    $clean_phone = preg_replace('/\D+/', '', $phone);
    $user_id = 0;
    $by_phone = get_users([
        'meta_key'   => 'cnc_phone',
        'meta_value' => $clean_phone,
        'number'     => 1,
        'fields'     => 'ID',
    ]);
    if (!empty($by_phone)) {
        $user_id = (int) $by_phone[0];
    } elseif (!empty($email)) {
        $user = get_user_by('email', $email);
        if ($user) $user_id = (int) $user->ID;
    }

    if (!$user_id) {
        wp_send_json_error(['message' => 'No account found for this phone/email']);
    }

    $user = get_userdata($user_id);
    if ($user && !in_array('customer', (array) $user->roles, true) && !$user->has_cap('manage_options')) {
        $user->set_role('customer');
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    do_action('wp_login', $user->user_login, $user);

    delete_transient($transient_key);

    wp_send_json_success([
        'message' => 'Logged in',
        'redirect_url' => home_url('/my-account/dashboard/'),
    ]);
}
