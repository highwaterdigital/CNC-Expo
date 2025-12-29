<?php
/**
 * Visitor Registration Hooks
 * Handles AJAX submission for visitor registration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Ensure Logger is available
require_once get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php';

add_action('wp_ajax_cnc_register_visitor', 'cnc_handle_visitor_registration');
add_action('wp_ajax_nopriv_cnc_register_visitor', 'cnc_handle_visitor_registration');

function cnc_handle_visitor_registration() {
    // Log start
    if (class_exists('cnc_Registration_Logger')) {
        cnc_Registration_Logger::log('visitor_attempt', 'Visitor registration attempt', ['post' => $_POST]);
    }

    check_ajax_referer('cnc_visitor_nonce', 'nonce');

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $company = sanitize_text_field($_POST['company']);
    $designation = sanitize_text_field($_POST['designation']);
    $city = sanitize_text_field($_POST['city']);
    $interest = sanitize_text_field($_POST['interest']);

    if (empty($name) || empty($email) || empty($phone)) {
        wp_send_json_error(['message' => 'Required fields missing']);
    }

    // Prepare data for sync
    $sync_data = [
        'action' => 'register_visitor',
        'data' => [
            'timestamp' => current_time('mysql'),
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'designation' => $designation,
            'city' => $city,
            'interest' => $interest
        ]
    ];

    // Sync to Google Sheets
    $sync_result = ['success' => false, 'message' => 'Sync not attempted'];
    if (class_exists('cnc_Sheet_Sync')) {
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('visitor_syncing', 'Syncing visitor to Sheets', ['data' => $sync_data]);
        }
        $sync_result = cnc_Sheet_Sync::send_data_to_sheet($sync_data);
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('visitor_synced', 'Visitor sync result', ['result' => $sync_result]);
        }
    }

    // Fire WhatsApp notifications in the background (non-blocking)
    if (class_exists('cnc_WhatsApp_API') && method_exists('cnc_WhatsApp_API', 'send_template')) {
        $admin_phone = cnc_WhatsApp_API::get_admin_phone();
        $user_phone  = $phone;
        $ref_id      = 'VIS-' . time(); // Simple Reference ID

        // 1. Notify Admin (Text)
        $admin_message = "New visitor registration\n"
            . "Name: {$name}\n"
            . "Email: {$email}\n"
            . "Phone: {$phone}\n"
            . "City: {$city}\n"
            . "Interest: {$interest}\n"
            . "At: " . current_time('d-M-Y h:i A');

        // 2. Notify User (Template)
        // Template: cnc_notify_registration_received
        // Params: {{1}}=Name, {{2}}=Reference ID
        $template_name = 'cnc_notify_registration_received';
        $components = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $name],
                    ['type' => 'text', 'text' => $ref_id]
                ]
            ]
        ];

        try {
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('visitor_whatsapp', 'Sending WhatsApp notifications', [
                    'admin_phone' => $admin_phone,
                    'user_phone' => $user_phone,
                    'template' => $template_name
                ]);
            }

            cnc_WhatsApp_API::send_message($admin_phone, $admin_message);
            cnc_WhatsApp_API::send_template($user_phone, $template_name, 'en', $components);
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('visitor_whatsapp_sent', 'WhatsApp notifications sent');
            }
        } catch (Exception $e) {
            error_log('Visitor WhatsApp notification failed: ' . $e->getMessage());
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log('visitor_whatsapp_fail', 'WhatsApp failed', ['error' => $e->getMessage()]);
            }
        }
    }

    if ($sync_result['success']) {
        if (class_exists('cnc_WhatsApp_API')) {
            try {
                do_action('cnc_visitor_registered', [
                    'name'    => $name,
                    'email'   => $email,
                    'phone'   => $phone,
                    'city'    => $city,
                    'interest'=> $interest,
                ]);
            } catch (Exception $e) {
                error_log('Visitor hook dispatch failed: ' . $e->getMessage());
            }
        }
        
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('visitor_success', 'Registration completed successfully');
        }
        
        wp_send_json_success(['message' => 'Registration successful!']);
    } else {
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log('visitor_fail', 'Registration failed at sync', ['result' => $sync_result]);
        }
        wp_send_json_error(['message' => 'Registration failed. Please try again. ' . ($sync_result['message'] ?? '')]);
    }
}
