<?php
/**
 * Loads all integration files.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

error_log('cnc DEBUG: Loading integrations-loader.php');

// Load Google Sheets Credentials
require_once cnc_CHILD_DIR . '/integrations/google-sheets/credentials.php';

// Load Google Sheet Sync Class
require_once cnc_CHILD_DIR . '/integrations/google-sheets/class-cnc-sheet-sync.php';

// Load Google Sheet Importer Class
require_once cnc_CHILD_DIR . '/integrations/google-sheets/class-cnc-sheet-importer.php';
if ( class_exists( 'cnc_Sheet_Importer' ) ) {
    error_log('cnc DEBUG: cnc_Sheet_Importer class loaded successfully.');
} else {
    error_log('cnc DEBUG: cnc_Sheet_Importer class FAILED to load.');
}

// Load Registration Logger
require_once cnc_CHILD_DIR . '/integrations/google-sheets/registration-logger.php';

// Load Registration Handler
require_once cnc_CHILD_DIR . '/integrations/google-sheets/registration-handler.php';

// Load Admin Sync Handler
require_once cnc_CHILD_DIR . '/integrations/google-sheets/admin-sync.php';

// Load Google Sheets Configuration (Admin Interface)
require_once cnc_CHILD_DIR . '/integrations/google-sheets/sheets-config.php';

// Load WhatsApp API Class
require_once cnc_CHILD_DIR . '/integrations/whatsapp/class-cnc-whatsapp-api.php';

// Note: Integration hooks are now loaded via inc/hooks.php