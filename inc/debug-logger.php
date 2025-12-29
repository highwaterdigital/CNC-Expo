<?php
/**
 * Lightweight debug logger for CNC events.
 * Logs outgoing/incoming payloads for WhatsApp, Google Sheets, and mail.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('cnc_debug_log')) {
    /**
     * @param string $channel e.g. whatsapp|sheets|mail
     * @param string $direction e.g. outgoing|incoming|error
     * @param mixed  $payload data to log
     * @param array  $context optional context values
     */
    function cnc_debug_log($channel, $direction, $payload, $context = []) {
        $enabled = get_option('cnc_enable_debug_logging', true);
        if (!$enabled) {
            return;
        }

        $channel = sanitize_key($channel);
        $direction = sanitize_key($direction);

        // Avoid logging secrets
        if (is_array($payload)) {
            unset($payload['token'], $payload['access_token'], $payload['Authorization']);
        }
        if (is_array($context)) {
            unset($context['token'], $context['access_token'], $context['Authorization']);
        }

        $entry = [
            'channel'   => $channel,
            'direction' => $direction,
            'timestamp' => current_time('mysql'),
            'payload'   => $payload,
            'context'   => $context,
        ];

        error_log('CNC DEBUG ' . strtoupper($channel) . ': ' . wp_json_encode($entry));
    }
}

if (!function_exists('cnc_log_whatsapp_event')) {
    /**
     * Specialized logger for WhatsApp events.
     * Writes to a separate file and optionally attaches to a post.
     */
    function cnc_log_whatsapp_event($type, $to, $message, $status, $post_id = null, $response = '') {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/cnc-logs';
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }
        
        $log_file = $log_dir . '/whatsapp.log';
        $timestamp = current_time('mysql');
        $entry = "[$timestamp] [$type] To: $to | Status: $status | PostID: " . ($post_id ?: 'N/A') . " | Msg: " . substr(json_encode($message), 0, 100) . " | Resp: " . substr($response, 0, 50) . "\n";
        
        // 1. Write to File
        error_log($entry, 3, $log_file);
        
        // 2. Attach to Post (Admin Notes)
        if ($post_id) {
            $current_log = get_post_meta($post_id, 'cnc_whatsapp_log', true);
            if (!is_array($current_log)) $current_log = [];
            
            $current_log[] = [
                'time' => $timestamp,
                'type' => $type,
                'to' => $to,
                'status' => $status,
                'message' => $message,
                'response' => $response
            ];
            
            // Keep last 20 logs per post to save space
            if (count($current_log) > 20) {
                $current_log = array_slice($current_log, -20);
            }
            
            update_post_meta($post_id, 'cnc_whatsapp_log', $current_log);
        }
    }
}

// Log outgoing mails
add_filter('wp_mail', function($args) {
    cnc_debug_log('mail', 'outgoing', [
        'to'      => $args['to'],
        'subject' => $args['subject'],
        'headers' => $args['headers'],
    ]);
    return $args;
});

add_action('wp_mail_failed', function($wp_error) {
    cnc_debug_log('mail', 'error', $wp_error->get_error_message(), $wp_error->get_error_data());
});

add_action('wp_mail_succeeded', function($mail_data) {
    cnc_debug_log('mail', 'sent', [
        'to'      => $mail_data['to'],
        'subject' => $mail_data['subject'],
    ]);
});
