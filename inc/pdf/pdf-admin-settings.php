<?php
/**
 * CNC Floor Plan PDF Admin Settings
 * Admin page for configuring PDF export settings
 * 
 * @package CNC_Child_Theme
 * @since 4.4.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Register admin menu page
 */
function cnc_register_pdf_admin_page() {
    add_submenu_page(
        'edit.php?post_type=cnc_booking',
        'Floor Plan PDF Settings',
        'PDF Export',
        'manage_options',
        'cnc-pdf-settings',
        'cnc_render_pdf_admin_page'
    );
}
add_action('admin_menu', 'cnc_register_pdf_admin_page');

/**
 * Register settings
 */
function cnc_register_pdf_settings() {
    register_setting('cnc_pdf_settings', 'cnc_event_title');
    register_setting('cnc_pdf_settings', 'cnc_event_venue');
    register_setting('cnc_pdf_settings', 'cnc_event_date');
    register_setting('cnc_pdf_settings', 'cnc_pdf_contact_email');
    register_setting('cnc_pdf_settings', 'cnc_pdf_contact_phone');
    register_setting('cnc_pdf_settings', 'cnc_pdf_shell_rate');
    register_setting('cnc_pdf_settings', 'cnc_pdf_raw_rate');
}
add_action('admin_init', 'cnc_register_pdf_settings');

/**
 * Render admin page
 */
function cnc_render_pdf_admin_page() {
    // Load floorplan data for preview
    require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
    $stalls = cnc_get_floorplan_data();
    
    // Calculate quick stats
    $stats = ['available' => 0, 'pending' => 0, 'booked' => 0, 'total' => 0];
    $common_types = ['fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 'stairs', 'stage', 'service', 'registration', 'lounge', 'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area', 'food-court', 'networking', 'feature', 'wall', 'boundary', 'partition'];
    
    foreach ($stalls as $s) {
        $type = isset($s['type']) ? $s['type'] : 'stall';
        if (in_array($type, $common_types) || $type !== 'stall') continue;
        $stats['total']++;
        if (isset($stats[$s['status']])) $stats[$s['status']]++;
    }
    
    // Get settings
    $event_title = get_option('cnc_event_title', 'Cable Net Convergence 2026');
    $event_venue = get_option('cnc_event_venue', 'HITEX Exhibition Centre, Hyderabad');
    $event_date = get_option('cnc_event_date', 'August 13-15, 2026');
    $contact_email = get_option('cnc_pdf_contact_email', 'info@cablenetconvergence.com');
    $contact_phone = get_option('cnc_pdf_contact_phone', '+91 40 2354 5678');
    $shell_rate = get_option('cnc_pdf_shell_rate', '11500');
    $raw_rate = get_option('cnc_pdf_raw_rate', '10500');
    
    ?>
    <div class="wrap cnc-pdf-admin">
        <h1 style="display: flex; align-items: center; gap: 15px;">
            <span class="dashicons dashicons-pdf" style="font-size: 32px; width: 32px; height: 32px;"></span>
            Floor Plan PDF Export
        </h1>
        
        <style>
            .cnc-pdf-admin { max-width: 1200px; }
            .cnc-admin-grid { display: grid; grid-template-columns: 1fr 350px; gap: 20px; margin-top: 20px; }
            .cnc-card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
            .cnc-card h2 { margin: 0 0 15px; padding: 0 0 10px; border-bottom: 2px solid #C6308C; font-size: 16px; }
            .cnc-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
            .cnc-stat-box { text-align: center; padding: 15px; border-radius: 8px; }
            .cnc-stat-box.available { background: #d4edda; }
            .cnc-stat-box.pending { background: #fff3cd; }
            .cnc-stat-box.booked { background: #f8d7da; }
            .cnc-stat-box.total { background: #e2e3e5; }
            .cnc-stat-value { font-size: 28px; font-weight: 700; }
            .cnc-stat-value.available { color: #2ECC71; }
            .cnc-stat-value.pending { color: #F39C12; }
            .cnc-stat-value.booked { color: #E74C3C; }
            .cnc-stat-value.total { color: #5E3A8E; }
            .cnc-stat-label { font-size: 12px; color: #666; text-transform: uppercase; }
            .cnc-form-row { margin-bottom: 15px; }
            .cnc-form-row label { display: block; font-weight: 600; margin-bottom: 5px; color: #333; }
            .cnc-form-row input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
            .cnc-form-row input:focus { border-color: #C6308C; outline: none; box-shadow: 0 0 0 2px rgba(198, 48, 140, 0.1); }
            .cnc-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; cursor: pointer; transition: all 0.2s; border: none; }
            .cnc-btn-primary { background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%); color: #fff; }
            .cnc-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(198, 48, 140, 0.3); color: #fff; }
            .cnc-btn-secondary { background: #34495E; color: #fff; }
            .cnc-btn-secondary:hover { background: #2C3E50; color: #fff; }
            .cnc-btn-group { display: flex; gap: 10px; margin-top: 20px; }
            .cnc-preview-box { background: #f8f9fa; border: 1px dashed #ddd; border-radius: 8px; padding: 20px; text-align: center; }
            .cnc-preview-box img { max-width: 100%; border-radius: 6px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
            .cnc-code-box { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 12px; overflow-x: auto; }
            .cnc-code-box code { color: #9CDCFE; }
            .cnc-info { background: #e7f3ff; border-left: 4px solid #0073aa; padding: 12px 15px; margin: 15px 0; font-size: 13px; }
            .cnc-shortcode-tag { background: #f0f0f0; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 13px; }
        </style>
        
        <!-- Live Statistics -->
        <div class="cnc-stats-grid">
            <div class="cnc-stat-box available">
                <div class="cnc-stat-value available"><?php echo $stats['available']; ?></div>
                <div class="cnc-stat-label">Available</div>
            </div>
            <div class="cnc-stat-box pending">
                <div class="cnc-stat-value pending"><?php echo $stats['pending']; ?></div>
                <div class="cnc-stat-label">Pending</div>
            </div>
            <div class="cnc-stat-box booked">
                <div class="cnc-stat-value booked"><?php echo $stats['booked']; ?></div>
                <div class="cnc-stat-label">Booked</div>
            </div>
            <div class="cnc-stat-box total">
                <div class="cnc-stat-value total"><?php echo $stats['total']; ?></div>
                <div class="cnc-stat-label">Total Stalls</div>
            </div>
        </div>
        
        <div class="cnc-admin-grid">
            <!-- Settings Column -->
            <div class="cnc-settings-col">
                <form method="post" action="options.php">
                    <?php settings_fields('cnc_pdf_settings'); ?>
                    
                    <div class="cnc-card">
                        <h2>📅 Event Information</h2>
                        <div class="cnc-form-row">
                            <label for="cnc_event_title">Event Title</label>
                            <input type="text" id="cnc_event_title" name="cnc_event_title" value="<?php echo esc_attr($event_title); ?>">
                        </div>
                        <div class="cnc-form-row">
                            <label for="cnc_event_venue">Venue</label>
                            <input type="text" id="cnc_event_venue" name="cnc_event_venue" value="<?php echo esc_attr($event_venue); ?>">
                        </div>
                        <div class="cnc-form-row">
                            <label for="cnc_event_date">Event Date</label>
                            <input type="text" id="cnc_event_date" name="cnc_event_date" value="<?php echo esc_attr($event_date); ?>">
                        </div>
                    </div>
                    
                    <div class="cnc-card">
                        <h2>📞 Contact Information</h2>
                        <div class="cnc-form-row">
                            <label for="cnc_pdf_contact_email">Email Address</label>
                            <input type="email" id="cnc_pdf_contact_email" name="cnc_pdf_contact_email" value="<?php echo esc_attr($contact_email); ?>">
                        </div>
                        <div class="cnc-form-row">
                            <label for="cnc_pdf_contact_phone">Phone Number</label>
                            <input type="text" id="cnc_pdf_contact_phone" name="cnc_pdf_contact_phone" value="<?php echo esc_attr($contact_phone); ?>">
                        </div>
                    </div>
                    
                    <div class="cnc-card">
                        <h2>💰 Pricing Rates (₹/m²)</h2>
                        <div class="cnc-form-row">
                            <label for="cnc_pdf_shell_rate">Shell Scheme Rate</label>
                            <input type="number" id="cnc_pdf_shell_rate" name="cnc_pdf_shell_rate" value="<?php echo esc_attr($shell_rate); ?>">
                        </div>
                        <div class="cnc-form-row">
                            <label for="cnc_pdf_raw_rate">Raw Space Rate</label>
                            <input type="number" id="cnc_pdf_raw_rate" name="cnc_pdf_raw_rate" value="<?php echo esc_attr($raw_rate); ?>">
                        </div>
                    </div>
                    
                    <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                </form>
            </div>
            
            <!-- Preview & Actions Column -->
            <div class="cnc-actions-col">
                <div class="cnc-card">
                    <h2>📄 Generate PDF</h2>
                    <p style="color: #666; font-size: 13px; margin-bottom: 15px;">
                        Generate a live floor plan PDF showing the current status of all stalls with real booking data.
                    </p>
                    
                    <div class="cnc-btn-group" style="flex-direction: column;">
                        <a href="<?php echo home_url('/floorplan-pdf/'); ?>" target="_blank" class="cnc-btn cnc-btn-primary">
                            <span class="dashicons dashicons-visibility"></span>
                            View Live Floor Plan
                        </a>
                        <a href="<?php echo home_url('/floorplan-pdf/?print=1'); ?>" target="_blank" class="cnc-btn cnc-btn-secondary">
                            <span class="dashicons dashicons-download"></span>
                            Download as PDF
                        </a>
                    </div>
                    
                    <div class="cnc-info" style="margin-top: 20px;">
                        <strong>Tip:</strong> Click "View Live Floor Plan" to preview, then use your browser's Print function (Ctrl+P) to save as PDF with the best quality.
                    </div>
                </div>
                
                <div class="cnc-card">
                    <h2>🔗 Integration</h2>
                    <p style="color: #666; font-size: 13px; margin-bottom: 15px;">
                        Add a download button anywhere using these shortcodes:
                    </p>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="font-size: 12px; display: block; margin-bottom: 5px;">Download Button:</strong>
                        <div class="cnc-code-box">
                            <code>[cnc_pdf_download_button]</code>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <strong style="font-size: 12px; display: block; margin-bottom: 5px;">With Custom Text:</strong>
                        <div class="cnc-code-box">
                            <code>[cnc_pdf_download_button text="Get Floor Plan"]</code>
                        </div>
                    </div>
                    
                    <div>
                        <strong style="font-size: 12px; display: block; margin-bottom: 5px;">Live Stats Widget:</strong>
                        <div class="cnc-code-box">
                            <code>[cnc_stall_stats]</code>
                        </div>
                    </div>
                </div>
                
                <div class="cnc-card">
                    <h2>🔌 API Endpoint</h2>
                    <p style="color: #666; font-size: 13px; margin-bottom: 10px;">
                        Get real-time stall data programmatically:
                    </p>
                    <div class="cnc-code-box">
                        <code><?php echo admin_url('admin-ajax.php?action=cnc_floorplan_json'); ?></code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
