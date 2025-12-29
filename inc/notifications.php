<?php
/**
 * Central notification hooks for WhatsApp events.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * When a new stall registration is submitted (pending).
 */
add_action('cnc_notify_registration_received', function($payload) {
    if (!class_exists('cnc_WhatsApp_API')) {
        return;
    }
    $phone   = $payload['phone'] ?? '';
    $company = $payload['company'] ?? '';
    $stalls  = implode(', ', (array)($payload['stalls'] ?? []));

    $user_msg = "Hi {$company}, we received your booking request for stall(s): {$stalls}. Status: Pending approval. We will confirm shortly.";
    $admin_msg = "New stall request: {$company}\nStalls: {$stalls}\nPhone: {$phone}";

    try {
        if ($phone) {
            cnc_WhatsApp_API::send_message($phone, $user_msg);
        }
        $admin_phone = cnc_WhatsApp_API::get_admin_phone();
        if ($admin_phone) {
            cnc_WhatsApp_API::send_message($admin_phone, $admin_msg);
        }
    } catch (Exception $e) {
        error_log('notify_registration_received failed: ' . $e->getMessage());
    }
});

/**
 * When admin approves/updates a stall booking.
 */
add_action('cnc_notify_stall_updated', function($payload) {
    if (!class_exists('cnc_WhatsApp_API')) {
        return;
    }
    $phone   = $payload['phone'] ?? '';
    $company = $payload['company'] ?? '';
    $stalls  = implode(', ', (array)($payload['stalls'] ?? []));
    $status  = ucfirst($payload['status'] ?? 'Updated');

    $user_msg = "Hi {$company}, your booking for stall(s): {$stalls} is now {$status}. See your dashboard for details.";
    $admin_msg = "Stall status updated to {$status}\nCompany: {$company}\nStalls: {$stalls}\nPhone: {$phone}";

    try {
        if ($phone) {
            cnc_WhatsApp_API::send_message($phone, $user_msg);
        }
        $admin_phone = cnc_WhatsApp_API::get_admin_phone();
        if ($admin_phone) {
            cnc_WhatsApp_API::send_message($admin_phone, $admin_msg);
        }
    } catch (Exception $e) {
        error_log('notify_stall_updated failed: ' . $e->getMessage());
    }
});

/**
 * Visitor registration confirmation.
 */
add_action('cnc_visitor_registered', function($payload) {
    if (!class_exists('cnc_WhatsApp_API')) {
        return;
    }
    $name  = $payload['name'] ?? 'Visitor';
    $phone = $payload['phone'] ?? '';
    $city  = $payload['city'] ?? '';
    $interest = $payload['interest'] ?? '';

    $user_msg = "Hi {$name}, thanks for registering as a visitor for CNC Expo. City: {$city}. Interest: {$interest}. See you at the venue!";
    $admin_msg = "New visitor: {$name}\nPhone: {$phone}\nCity: {$city}\nInterest: {$interest}";

    try {
        if ($phone) {
            cnc_WhatsApp_API::send_message($phone, $user_msg);
        }
        $admin_phone = cnc_WhatsApp_API::get_admin_phone();
        if ($admin_phone) {
            cnc_WhatsApp_API::send_message($admin_phone, $admin_msg);
        }
    } catch (Exception $e) {
        error_log('cnc_visitor_registered WhatsApp failed: ' . $e->getMessage());
    }
});
