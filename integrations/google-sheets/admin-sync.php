<?php
/**
 * Admin Sync Handler
 * 
 * Syncs data to Google Sheets when a post is saved or deleted in WP Admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class cnc_Admin_Sync {

    public static function init() {
        add_action( 'save_post', array( __CLASS__, 'handle_save_post' ), 20, 3 );
        add_action( 'before_delete_post', array( __CLASS__, 'handle_delete_post' ) );
    }

    /**
     * Handle Post Deletion
     */
    public static function handle_delete_post( $post_id ) {
        if ( get_post_type( $post_id ) !== 'cnc_booking' ) {
            return;
        }

        // Prepare data for deletion
        $data = [
            'action' => 'delete_booking',
            'data' => [
                'wp_post_id' => $post_id
            ]
        ];

        // Send to Sheet
        if ( class_exists( 'cnc_Sheet_Sync' ) ) {
            cnc_Sheet_Sync::send_data_to_sheet( $data );
            error_log("CNC Sync: Sent delete request for Booking ID $post_id");
        }
    }

    /**
     * Handle Post Save (Create/Update)
     */
    public static function handle_save_post( $post_id, $post, $update ) {
        // 1. Basic Checks
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( wp_is_post_revision( $post_id ) ) return;
        
        // Only handle cnc_booking
        if ( 'cnc_booking' !== $post->post_type ) {
            return;
        }

        // Prevent sync loop if this save was triggered by the importer
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST && strpos( $_SERVER['REQUEST_URI'], '/cnc/v1/sync' ) !== false ) {
            return;
        }

        // 2. Prepare Data
        $stall_id = get_post_meta( $post_id, 'cnc_stall_id', true );
        $company = get_post_meta( $post_id, 'cnc_company_name', true ); // Assuming meta key
        if (empty($company)) $company = $post->post_title;
        
        $contact_person = get_post_meta( $post_id, 'cnc_contact_person', true );
        $email = get_post_meta( $post_id, 'cnc_email', true );
        $phone = get_post_meta( $post_id, 'cnc_phone', true );
        $status = get_post_meta( $post_id, 'cnc_booking_status', true );
        
        // Map status to Sheet format
        $status_map = [
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'pending'  => 'Pending'
        ];
        $sheet_status = isset($status_map[$status]) ? $status_map[$status] : ucfirst($status);

        $data = [
            'action' => 'create_booking', // Currently we only have create, which appends. 
            // TODO: Implement update_booking in GS to avoid duplicates on edit.
            'data' => [
                'timestamp' => current_time( 'mysql' ),
                'wp_post_id' => $post_id,
                'stall_id' => $stall_id,
                'company' => $company,
                'name' => $contact_person,
                'email' => $email,
                'phone' => $phone,
                'status' => $sheet_status
            ]
        ];

        // 3. Send to Sheet
        // Only sync if we have minimal data
        if ( !empty($stall_id) ) {
            if ( class_exists( 'cnc_Sheet_Sync' ) ) {
                cnc_Sheet_Sync::send_data_to_sheet( $data );
                error_log("CNC Sync: Sent save request for Booking ID $post_id");
            }
        }
    }
}

// Initialize
cnc_Admin_Sync::init();

