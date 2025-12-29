<?php
/**
 * Handles sending data to Google Sheets via a Webhook.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class cnc_Sheet_Sync {
    
    public static function send_data_to_sheet( $data ) {
        // Load credentials
        require_once get_stylesheet_directory() . '/integrations/google-sheets/credentials.php';
        
        // -----------------------------------------------------------------
        // cnc Google Sheets Integration with Secure Credentials
        // -----------------------------------------------------------------
        
        $credentials = cnc_Google_Credentials::get_api_credentials();
        $webhook_url = $credentials['webhook_url'];
        
        // Also try the direct option if credentials don't have it
        if (empty($webhook_url)) {
            $webhook_url = get_option('cnc_sheets_webhook_url');
        }
        
        // Try alternative option name used in admin
        if (empty($webhook_url)) {
            $webhook_url = get_option('cnc_google_sheets_webhook_url');
        }
        
        if ( empty( $webhook_url ) ) {
            // Log error for debugging
            error_log('cnc Google Sheets: Webhook URL not configured');
            return [
                'success' => false,
                'message' => 'Webhook URL not configured',
                'debug_info' => 'No webhook URL found in credentials or options'
            ];
        }
        
        // Prepare enhanced data for cnc Google Sheets
        if (isset($data['action']) && isset($data['data'])) {
            // CNC v4.0: Pre-formatted action request
            $body = $data;
        } else {
            // Legacy / Flat format support
            $body = [
                'timestamp' => $data['timestamp'] ?? date('Y-m-d H:i:s'),
                'name' => sanitize_text_field($data['name'] ?? $data['full_name'] ?? ''),
                'email' => sanitize_email($data['email'] ?? ''),
                'phone' => sanitize_text_field($data['phone'] ?? $data['mobile'] ?? ''),
                'company' => sanitize_text_field($data['company'] ?? ''),
                'stall_id' => sanitize_text_field($data['stall_id'] ?? ''),
                'status' => sanitize_text_field($data['status'] ?? 'Pending'),
                'wp_post_id' => sanitize_text_field($data['wp_post_id'] ?? ''),
                'sheet_name' => $data['sheet_name'] ?? 'Bookings'
            ];
        }

        // Send data to Google Sheets
        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('sheets', 'outgoing', $body, ['url' => $webhook_url]);
        }
        $response = wp_remote_post( $webhook_url, [
            'body' => json_encode( $body ),
            'headers' => [ 
                'Content-Type' => 'application/json',
                'User-Agent' => 'cnc-Website/1.0'
            ],
            'timeout' => 30,
            'sslverify' => true,
        ]);

        // Handle response
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            error_log('cnc Google Sheets Error: ' . $error_message);
            if (function_exists('cnc_debug_log')) {
                cnc_debug_log('sheets', 'incoming', ['status' => 'error', 'message' => $error_message], ['url' => $webhook_url]);
            }
            return [
                'success' => false,
                'message' => 'HTTP request failed: ' . $error_message,
                'debug_info' => 'WordPress HTTP error'
            ];
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        
        // Check for HTML response (Permissions Error)
        if ( strpos( trim($response_body), '<!DOCTYPE html>' ) === 0 || strpos( trim($response_body), '<html' ) === 0 ) {
             error_log("cnc Sync Error: Received HTML instead of JSON. Body Preview: " . substr($response_body, 0, 600));
             return [
                'success' => false,
                'message' => 'Google Permissions Error: The Web App is returning HTML. Redeploy with "Who has access: Anyone".',
                'debug_info' => 'Received HTML login page instead of JSON response.'
            ];
        }

        if ( $response_code !== 200 ) {
            error_log('cnc Google Sheets HTTP Error: ' . $response_code . ' - ' . $response_body);
            if (function_exists('cnc_debug_log')) {
                cnc_debug_log('sheets', 'incoming', ['status' => $response_code, 'body' => $response_body], ['url' => $webhook_url]);
            }
            return [
                'success' => false,
                'message' => 'Webhook returned non-200 status',
                'debug_info' => [
                    'response_code' => $response_code,
                    'response_body' => $response_body
                ]
            ];
        }
        
        // Log success
        error_log('cnc Google Sheets: Successfully sent data for ' . $body['name']);
        if (function_exists('cnc_debug_log')) {
            cnc_debug_log('sheets', 'incoming', ['status' => $response_code, 'body' => json_decode($response_body, true)], ['url' => $webhook_url]);
        }
        
        return [
            'success' => true,
            'message' => 'Data sent to Google Sheet successfully',
            'debug_info' => [
                'response_code' => $response_code,
                'response_body' => json_decode($response_body, true)
            ]
        ];
    }

    /**
     * Send volunteer data to separate Google Sheets tab
     */
    public function append_volunteer_data($data) {
        require_once get_stylesheet_directory() . '/integrations/google-sheets/credentials.php';
        
        $credentials = cnc_Google_Credentials::get_api_credentials();
        $webhook_url = $credentials['volunteer_webhook_url'] ?? $credentials['webhook_url'];
        
        if (empty($webhook_url)) {
            $webhook_url = get_option('cnc_volunteer_webhook_url', get_option('cnc_sheets_webhook_url'));
        }
        
        if (empty($webhook_url)) {
            error_log('cnc Volunteer: Webhook URL not configured');
            return false;
        }
        
        $body = [
            'sheet_type' => 'volunteers',
            'timestamp' => $data['timestamp'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'age' => $data['age'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'occupation' => $data['occupation'],
            'institution' => $data['institution'],
            'volunteer_role' => $data['volunteer_role'],
            'availability' => $data['availability'],
            'skills' => $data['skills'],
            'why_volunteer' => $data['why_volunteer'],
            'emergency_name' => $data['emergency_name'],
            'emergency_phone' => $data['emergency_phone'],
            'emergency_relation' => $data['emergency_relation'],
            'photo_consent' => $data['photo_consent'],
            'ip_address' => self::get_user_ip()
        ];
        
        $response = wp_remote_post($webhook_url, [
            'body' => json_encode($body),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            error_log('cnc Volunteer Sync Error: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        return $response_code === 200;
    }
    
    /**
     * Get user's IP address
     */
    private static function get_user_ip() {
        $ip_fields = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_fields as $field) {
            if (!empty($_SERVER[$field])) {
                $ip = $_SERVER[$field];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    }

    /**
     * Test the Google Sheets connection
     */
    public static function test_connection() {
        // Load logger for connection test logging
        if (file_exists(get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php')) {
            require_once get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php';
        }
        
        // Try both possible option names
        $webhook_url = get_option('cnc_sheets_webhook_url');
        if (empty($webhook_url)) {
            $webhook_url = get_option('cnc_google_sheets_webhook_url');
        }
        
        if (empty($webhook_url)) {
            $result = [
                'success' => false,
                'message' => 'No webhook URL configured. Please set up your Google Apps Script webhook URL.',
                'debug_info' => 'Webhook URL is empty in both WordPress option locations'
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log_connection_test('failed', $result);
            }
            return $result;
        }
        
        // Validate webhook URL format
        if (!filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            $result = [
                'success' => false,
                'message' => 'Invalid webhook URL format. Please check your Google Apps Script URL.',
                'debug_info' => 'URL validation failed: ' . $webhook_url
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log_connection_test('failed', $result);
            }
            return $result;
        }
        
        // Check if URL looks like a Google Apps Script URL
        if (strpos($webhook_url, 'script.google.com') === false) {
            $result = [
                'success' => false,
                'message' => 'Webhook URL does not appear to be a Google Apps Script URL.',
                'debug_info' => 'URL does not contain script.google.com domain'
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log_connection_test('failed', $result);
            }
            return $result;
        }
        
        // Check if it's a library URL instead of web app URL
        if (strpos($webhook_url, '/macros/library/') !== false) {
            $result = [
                'success' => false,
                'message' => 'Wrong URL type: This is a Library URL. You need a Web App deployment URL that ends with /exec',
                'debug_info' => 'Library URLs cannot receive POST requests. Create a Web app deployment instead.'
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log_connection_test('failed', $result);
            }
            return $result;
        }
        
        // Check if it's a /dev URL instead of /exec
        if (strpos($webhook_url, '/dev') !== false) {
            $result = [
                'success' => false,
                'message' => 'Development URL detected: URLs ending with /dev may have permission issues. Try the production URL ending with /exec',
                'debug_info' => 'Replace /dev with /exec in your webhook URL for better reliability.'
            ];
            
            if (class_exists('cnc_Registration_Logger')) {
                cnc_Registration_Logger::log_connection_test('failed', $result);
            }
            return $result;
        }
        
        // Test data matching the expected format
        $test_data = [
            'timestamp' => current_time('mysql'),
            'name' => 'cnc Test Registration',
            'email' => 'test@cncindia.org',
            'phone' => '9999999999',
            'age' => 25,
            'city' => 'Hyderabad',
            'registration_type' => 'Registrations',
            'interest' => 'Website',
            'source' => 'Connection Test',
            'ip_address' => self::get_user_ip(),
            'user_agent' => 'Mozilla/5.0 (Connection Test)',
            'status' => 'Test'
        ];
        
        if (class_exists('cnc_Registration_Logger')) {
            cnc_Registration_Logger::log_connection_test('attempting', [
                'webhook_url' => $webhook_url,
                'test_data_fields' => array_keys($test_data),
                'option1' => get_option('cnc_sheets_webhook_url'),
                'option2' => get_option('cnc_google_sheets_webhook_url')
            ]);
        }
        
        $result = self::send_data_to_sheet($test_data);
        
        // Log the connection test result
        if (class_exists('cnc_Registration_Logger')) {
            if ($result['success']) {
                cnc_Registration_Logger::log_connection_test('success', [
                    'response_body' => $result['response_body'] ?? 'No response body'
                ]);
            } else {
                cnc_Registration_Logger::log_connection_test('failed', [
                    'error_message' => $result['message'],
                    'response_code' => $result['response_code'] ?? 'No response code',
                    'debug_info' => $result['debug_info'] ?? 'No debug info'
                ]);
            }
        }
        
        return $result;
    }
}
// To get your Webhook URL:
// 1. Go to script.google.com and create a new project.
// 2. Paste this code:
/*
function doPost(e) {
  try {
    var sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName('Registrations');
    if (!sheet) {
      sheet = SpreadsheetApp.getActiveSpreadsheet().insertSheet('Registrations');
      sheet.appendRow(['Date', 'Name', 'Email', 'Phone']);
    }
    
    var data = JSON.parse(e.postData.contents);
    
    sheet.appendRow([
      data.date,
      data.name, 
      data.email, 
      data.phone
    ]);
    
    return ContentService.createTextOutput(JSON.stringify({result: 'success'})).setMimeType(ContentService.MimeType.JSON);
  } catch (ex) {
    return ContentService.createTextOutput(JSON.stringify({result: 'error', error: ex.message})).setMimeType(ContentService.MimeType.JSON);
  }
}
*/
// 3. Click Deploy > New Deployment > Select Type (Web app).
// 4. Set "Execute as" to "Me".
// 5. Set "Who has access" to "Anyone".
// 6. Click Deploy and copy the "Web app URL".
