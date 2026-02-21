<?php
/**
 * CNC Floor Plan PDF AJAX Handler
 * Provides endpoints for downloading dynamic floor plan PDFs
 * 
 * @package CNC_Child_Theme
 * @since 4.4.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Register PDF download endpoint
 */
function cnc_register_pdf_endpoints() {
    // Public endpoint for PDF view page
    add_rewrite_rule(
        '^floorplan-pdf/?$',
        'index.php?cnc_floorplan_pdf=1',
        'top'
    );
    
    // Add query var
    add_filter('query_vars', function($vars) {
        $vars[] = 'cnc_floorplan_pdf';
        return $vars;
    });
}
add_action('init', 'cnc_register_pdf_endpoints');

/**
 * Handle PDF page template
 */
function cnc_handle_pdf_template() {
    $pdf_request = get_query_var('cnc_floorplan_pdf');
    
    if ($pdf_request) {
        require_once get_stylesheet_directory() . '/inc/pdf/class-cnc-floorplan-pdf.php';
        
        // Output the printable HTML
        echo CNC_Floorplan_PDF::generate_printable_html();
        exit;
    }
}
add_action('template_redirect', 'cnc_handle_pdf_template', 0);

/**
 * AJAX: Generate Floor Plan PDF View
 */
function cnc_ajax_floorplan_pdf() {
    // Allow both logged-in and non-logged-in users
    
    // Verify nonce if provided
    if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'cnc_floorplan_pdf')) {
        wp_send_json_error(['message' => 'Invalid security token']);
    }
    
    // Generate PDF URL
    $pdf_url = home_url('/floorplan-pdf/');
    
    // Add auto-print parameter if requested
    if (isset($_POST['auto_print']) && $_POST['auto_print'] === '1') {
        $pdf_url = add_query_arg('print', '1', $pdf_url);
    }
    
    wp_send_json_success([
        'pdf_url' => $pdf_url,
        'message' => 'Floor plan generated successfully',
    ]);
}
add_action('wp_ajax_cnc_floorplan_pdf', 'cnc_ajax_floorplan_pdf');
add_action('wp_ajax_nopriv_cnc_floorplan_pdf', 'cnc_ajax_floorplan_pdf');

/**
 * AJAX: Get Floor Plan JSON Data
 */
function cnc_ajax_floorplan_json() {
    require_once get_stylesheet_directory() . '/inc/pdf/class-cnc-floorplan-pdf.php';
    
    $data = CNC_Floorplan_PDF::get_floorplan_json();
    wp_send_json($data);
}
add_action('wp_ajax_cnc_floorplan_json', 'cnc_ajax_floorplan_json');
add_action('wp_ajax_nopriv_cnc_floorplan_json', 'cnc_ajax_floorplan_json');

/**
 * AJAX: Get Floor Plan Statistics Only
 */
function cnc_ajax_floorplan_stats() {
    require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
    require_once get_stylesheet_directory() . '/inc/pdf/class-cnc-floorplan-pdf.php';
    
    $stalls = cnc_get_floorplan_data();
    
    $stats = [
        'available' => 0,
        'pending' => 0,
        'booked' => 0,
        'total' => 0,
    ];
    
    $common_area_types = [
        'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
        'stairs', 'stage', 'service', 'registration', 'lounge', 
        'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
        'food-court', 'networking', 'feature', 'wall', 'boundary', 'partition'
    ];
    
    foreach ($stalls as $s) {
        $type = isset($s['type']) ? $s['type'] : 'stall';
        if (in_array($type, $common_area_types) || $type !== 'stall') continue;
        
        $stats['total']++;
        $stats[$s['status']]++;
    }
    
    wp_send_json_success([
        'stats' => $stats,
        'updated_at' => current_time('c'),
    ]);
}
add_action('wp_ajax_cnc_floorplan_stats', 'cnc_ajax_floorplan_stats');
add_action('wp_ajax_nopriv_cnc_floorplan_stats', 'cnc_ajax_floorplan_stats');

/**
 * Flush rewrite rules on theme activation
 */
function cnc_pdf_flush_rewrite_rules() {
    cnc_register_pdf_endpoints();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'cnc_pdf_flush_rewrite_rules');

/**
 * Add admin notice to flush permalinks
 */
function cnc_pdf_admin_notice() {
    if (!get_option('cnc_pdf_permalinks_flushed')) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>CNC Floor Plan PDF:</strong> Please <a href="<?php echo admin_url('options-permalink.php'); ?>">save your permalinks</a> to enable the PDF download feature.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'cnc_pdf_admin_notice');

/**
 * Mark permalinks as flushed after visiting permalink settings
 */
function cnc_pdf_mark_permalinks_flushed() {
    if (isset($_GET['settings-updated']) && strpos($_SERVER['REQUEST_URI'], 'options-permalink.php') !== false) {
        update_option('cnc_pdf_permalinks_flushed', true);
    }
}
add_action('admin_init', 'cnc_pdf_mark_permalinks_flushed');
