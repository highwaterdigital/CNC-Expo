<?php
/**
 * WhatsApp Cloud API helper.
 * Reads credentials from WP options or environment variables and sends
 * text messages using Meta's Cloud API.
 */
class cnc_WhatsApp_API {

    /**
     * Send a text message via WhatsApp Cloud API.
     */
    public static function send_message( $phone_number, $message, $context = [] ) {
        $config = self::get_config();

        if ( empty( $config['access_token'] ) || empty( $config['phone_number_id'] ) ) {
            error_log( 'WhatsApp: Missing access token or phone number ID. Configure in Settings > Google Sheets/WhatsApp.' );
            return false;
        }

        $clean_number = self::format_phone_number( $phone_number );
        if ( empty( $clean_number ) ) {
            error_log( 'WhatsApp: Invalid phone number provided: ' . print_r( $phone_number, true ) );
            return false;
        }

        $api_base = !empty($config['api_url']) ? rtrim($config['api_url'], '/') : 'https://graph.facebook.com/v20.0';
        $url  = sprintf( '%s/%s/messages', $api_base, $config['phone_number_id'] );
        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('whatsapp', 'outgoing', ['to' => $clean_number, 'type' => 'text'], ['url' => $url]);
        }
        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $clean_number,
            'type'              => 'text',
            'text'              => [
                'body'        => $message,
                'preview_url' => false,
            ],
        ];

        $response = wp_remote_post(
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['access_token'],
                    'Content-Type'  => 'application/json',
                ],
                'body'      => wp_json_encode( $body ),
                'timeout'   => 20,
                'sslverify' => true,
            ]
        );

        if ( is_wp_error( $response ) ) {
            error_log( 'WhatsApp: HTTP error - ' . $response->get_error_message() );
            if (function_exists('cnc_log_whatsapp_event')) {
                cnc_log_whatsapp_event('text', $clean_number, $message, 'http_error', $context['post_id'] ?? null, $response->get_error_message());
            }
            return false;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $resp = wp_remote_retrieve_body( $response );

        if (function_exists('cnc_log_whatsapp_event')) {
            cnc_log_whatsapp_event('text', $clean_number, $message, ($code === 200 ? 'success' : 'failed'), $context['post_id'] ?? null, $resp);
        }

        if ( $code !== 200 ) {
            error_log( 'WhatsApp: Non-200 response ' . $code . ' - ' . $resp );
            if (function_exists('cnc_debug_log')) {
                cnc_debug_log('whatsapp', 'incoming', ['status' => $code, 'body' => $resp], ['to' => $clean_number]);
            }
            return false;
        }

        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('whatsapp', 'incoming', ['status' => $code, 'body' => $resp], ['to' => $clean_number]);
        }
        error_log( 'WhatsApp: Message sent to ' . $clean_number );
        return true;
    }

    /**
     * Send a templated message via WhatsApp Cloud API (falls back to text).
     */
    public static function send_template( $phone_number, $template_name, $language = 'en', $components = [], $context = [] ) {
        $config = self::get_config();

        if ( empty( $config['access_token'] ) || empty( $config['phone_number_id'] ) ) {
            return self::send_message( $phone_number, 'Booking received. Thank you for registering for CNC Expo.', $context );
        }

        $clean_number = self::format_phone_number( $phone_number );
        if ( empty( $clean_number ) ) {
            return false;
        }

        $body = [
            'messaging_product' => 'whatsapp',
            'to'                => $clean_number,
            'type'              => 'template',
            'template'          => [
                'name'     => $template_name,
                'language' => [ 'code' => $language ],
            ],
        ];

        if ( ! empty( $components ) ) {
            $body['template']['components'] = $components;
        }

        $api_base = !empty($config['api_url']) ? rtrim($config['api_url'], '/') : 'https://graph.facebook.com/v20.0';
        $url      = sprintf( '%s/%s/messages', $api_base, $config['phone_number_id'] );
        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('whatsapp', 'outgoing', ['to' => $clean_number, 'type' => 'template', 'template' => $template_name], ['url' => $url]);
        }
        $response = wp_remote_post(
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $config['access_token'],
                    'Content-Type'  => 'application/json',
                ],
                'body'      => wp_json_encode( $body ),
                'timeout'   => 20,
                'sslverify' => true,
            ]
        );

        if ( is_wp_error( $response ) ) {
            error_log( 'WhatsApp template error: ' . $response->get_error_message() );
            if (function_exists('cnc_log_whatsapp_event')) {
                cnc_log_whatsapp_event('template', $clean_number, $template_name, 'http_error', $context['post_id'] ?? null, $response->get_error_message());
            }
            return false;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $resp = wp_remote_retrieve_body( $response );

        if (function_exists('cnc_log_whatsapp_event')) {
            cnc_log_whatsapp_event('template', $clean_number, $template_name, ($code === 200 ? 'success' : 'failed'), $context['post_id'] ?? null, $resp);
        }

        if ( $code !== 200 ) {
            error_log( 'WhatsApp template non-200 response: ' . $resp );
            if (function_exists('cnc_debug_log')) {
                cnc_debug_log('whatsapp', 'incoming', ['status' => $code, 'body' => $resp], ['to' => $clean_number]);
            }
            return false;
        }

        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('whatsapp', 'incoming', ['status' => $code, 'body' => $resp], ['to' => $clean_number]);
        }
        error_log( 'WhatsApp template sent to ' . $clean_number );
        return true;
    }

    /**
     * Get admin phone configured for alerts.
     */
    public static function get_admin_phone() {
        $default = '+919505050007'; // matches site contact number
        $stored  = get_option( 'cnc_admin_phone', $default );
        return self::format_phone_number( $stored );
    }

    /**
     * Fetch config from options/env.
     */
    private static function get_config() {
        $access_token    = get_option( 'cnc_whatsapp_access_token', '' );
        $phone_number_id = get_option( 'cnc_whatsapp_phone_id', '' );
        $api_url         = '';

        // Allow environment variable overrides
        if ( empty( $access_token ) ) {
            $access_token = getenv( 'CNC_WHATSAPP_ACCESS_TOKEN' ) ?: getenv( 'CNC_META_ACCESS_TOKEN' );
        }
        if ( empty( $phone_number_id ) ) {
            $phone_number_id = getenv( 'CNC_WHATSAPP_PHONE_ID' );
        }
        if ( empty( $api_url ) ) {
            $api_url = getenv( 'CNC_WHATSAPP_API_URL' );
        }

        // Fallback to TK_* constants if defined in wp-config
        if ( empty( $access_token ) && defined( 'TK_WHATSAPP_API_TOKEN' ) ) {
            $access_token = TK_WHATSAPP_API_TOKEN;
        }
        if ( empty( $phone_number_id ) && defined( 'TK_WHATSAPP_PHONE_NUMBER_ID' ) ) {
            $phone_number_id = TK_WHATSAPP_PHONE_NUMBER_ID;
        }
        if ( empty( $api_url ) && defined( 'TK_WHATSAPP_API_URL' ) ) {
            $api_url = TK_WHATSAPP_API_URL;
        }

        return [
            'access_token'    => trim( $access_token ),
            'phone_number_id' => trim( $phone_number_id ),
            'api_url'         => trim( $api_url ),
        ];
    }

    /**
     * Normalize phone numbers to E.164 without plus.
     */
    private static function format_phone_number( $number, $default_country = '91' ) {
        $digits = preg_replace( '/\D+/', '', (string) $number );
        if ( empty( $digits ) ) {
            return '';
        }

        // Prepend default country code if a 10-digit local number is provided
        if ( strlen( $digits ) === 10 && strpos( $digits, $default_country ) !== 0 ) {
            $digits = $default_country . $digits;
        }

        return $digits;
    }
}
