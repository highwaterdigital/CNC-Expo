<?php
/**
 * CNC Google Sheets Webhook Handler
 * Handles incoming requests from Google Sheets to update booking status and quotation links.
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNC_Sheet_Webhook {

    public static function init() {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('cnc/v1', '/update-status', [
            'methods'             => 'POST',
            'callback'            => [__CLASS__, 'handle_status_update'],
            'permission_callback' => '__return_true', // validated via shared secret
        ]);
    }

    public static function handle_status_update($request) {
        $params = $request->get_json_params();

        error_log('CNC Webhook: Received status update request');
        error_log('CNC Webhook Payload: ' . print_r($params, true));

        $secret = 'cnc_xpo_2026_secure_sync'; // TODO: move to option/env
        if (empty($params['secret']) || $params['secret'] !== $secret) {
            return new WP_Error('forbidden', 'Invalid secret key', ['status' => 403]);
        }

        if (empty($params['post_id']) || empty($params['status'])) {
            return new WP_Error('missing_params', 'Missing post_id or status', ['status' => 400]);
        }

        $post_id       = intval($params['post_id']);
        $status        = sanitize_text_field($params['status']); // Approved, Rejected, Pending
        $quotation_url = !empty($params['quotation_url']) ? esc_url_raw($params['quotation_url']) : '';

        if (!get_post($post_id)) {
            return new WP_Error('not_found', "Post ID $post_id not found", ['status' => 404]);
        }

        $status_map = [
            'Approved' => 'approved',
            'Rejected' => 'rejected',
            'Pending'  => 'pending',
        ];

        $internal_status = isset($status_map[$status]) ? $status_map[$status] : strtolower($status);
        $old_status      = get_post_meta($post_id, 'cnc_booking_status', true);

        update_post_meta($post_id, 'cnc_booking_status', $internal_status);
        if (!empty($quotation_url)) {
            update_post_meta($post_id, '_cnc_quotation_url', $quotation_url);
        }

        return rest_ensure_response([
            'success'       => true,
            'message'       => "Booking #$post_id synced to status $internal_status",
            'post_id'       => $post_id,
            'new_status'    => $internal_status,
            'old_status'    => $old_status,
            'quotation_url' => $quotation_url,
        ]);
    }
}

CNC_Sheet_Webhook::init();
