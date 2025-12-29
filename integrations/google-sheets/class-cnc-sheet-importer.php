<?php
/**
 * CNC Sheet Importer
 * Handles pulling data from Google Sheets into WordPress.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class cnc_Sheet_Importer {

    /**
     * Fetch data from Google Sheet via Web App URL
     */
    public static function fetch_sheet_data( $action ) {
        $webhook_url = get_option('cnc_google_sheets_webhook_url');
        
        // Fallback
        if ( empty( $webhook_url ) ) {
            $webhook_url = get_option('cnc_sheets_webhook_url');
        }
        
        if ( empty( $webhook_url ) ) {
            error_log("CNC Sync Error: Webhook URL is missing in WP Settings.");
            return new WP_Error( 'no_url', 'Webhook URL not configured in WP Settings.' );
        }

        // Append query param for GET request
        $url = add_query_arg( 'action', $action, $webhook_url );
        
        error_log("CNC Sync: Fetching data from $url");

        // Important: Google Apps Script redirects, so we need to follow redirects
        $response = wp_remote_get( $url, array(
            'timeout' => 30,
            'redirection' => 5
        ));

        if ( is_wp_error( $response ) ) {
            error_log("CNC Sync Error: wp_remote_get failed. " . $response->get_error_message());
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        
        // Check for HTML response (Permissions Error)
        if ( strpos( trim($body), '<!DOCTYPE html>' ) === 0 || strpos( trim($body), '<html' ) === 0 ) {
            if (strpos($body, 'accounts.google.com/v3/signin') !== false) {
                return new WP_Error( 'permissions_error', 'CRITICAL: Google Script is asking for login. You MUST redeploy the script with "Who has access: Anyone".' );
            }
            return new WP_Error( 'permissions_error', 'Google Permissions Error: The Web App is returning HTML. Redeploy with "Who has access: Anyone".' );
        }

        $json = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            error_log('CNC Import Error: Invalid JSON. Body: ' . substr($body, 0, 500));
            return new WP_Error( 'invalid_json', 'Invalid JSON received from Google Sheet.' );
        }

        if ( isset( $json['result'] ) && $json['result'] === 'error' ) {
            return new WP_Error( 'sheet_error', $json['message'] );
        }

        if ( isset( $json['data'] ) ) {
            return $json['data'];
        }

        return new WP_Error( 'invalid_format', 'Invalid data format received from Sheet' );
    }

    /**
     * Import Booking Statuses
     */
    public static function import_bookings_status() {
        $data = self::fetch_sheet_data( 'get_bookings' );
        
        if ( is_wp_error( $data ) ) {
            return "Error fetching bookings: " . $data->get_error_message();
        }

        if ( !isset($data['bookings']) || !is_array($data['bookings']) ) {
            return "Error: No bookings data found in response.";
        }

        $bookings = $data['bookings'];
        error_log('CNC Sync: Received ' . count($bookings) . ' bookings.');

        $updated = 0;
        $total = 0;

        foreach ( $bookings as $row ) {
            $post_id = isset($row['Booking ID (WP)']) ? intval($row['Booking ID (WP)']) : 0;
            $status = isset($row['Status']) ? $row['Status'] : '';
            
            if ( $post_id > 0 && !empty($status) ) {
                $total++;
                
                // Map Status
                $status_map = [
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'Pending'  => 'pending'
                ];
                $internal_status = isset($status_map[$status]) ? $status_map[$status] : strtolower($status);
                
                // Update Post
                $current_status = get_post_meta($post_id, 'cnc_booking_status', true);
                if ($current_status !== $internal_status) {
                    update_post_meta($post_id, 'cnc_booking_status', $internal_status);
                    $updated++;
                }
            }
        }

        return "Sync Complete: Processed $total bookings. Updated $updated statuses.";
    }
}
