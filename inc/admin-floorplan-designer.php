<?php
/**
 * CNC Floor Plan Designer
 * A visual drag-and-drop grid editor for the admin area.
 * Now integrated into the main Floor Plan Manager.
 */

require_once get_stylesheet_directory() . '/inc/floorplan-config.php';

// REMOVED STANDALONE MENU REGISTRATION
// The render function is now called by admin-floorplan.php

// AJAX Handler for saving layout
add_action('wp_ajax_cnc_save_layout', 'cnc_save_layout_callback');
add_action('wp_ajax_cnc_load_default_json', 'cnc_load_default_json_callback');

function cnc_load_default_json_callback() {
    check_ajax_referer('cnc_designer_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permission denied');
    
    $json_file = get_stylesheet_directory() . '/hall3_layout.json';
    if (file_exists($json_file)) {
        $data = json_decode(file_get_contents($json_file), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error('JSON Decode Error: ' . json_last_error_msg());
        }
    } else {
        wp_send_json_error('hall3_layout.json not found.');
    }
}

function cnc_save_layout_callback() {
    check_ajax_referer('cnc_designer_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }

    $type = isset($_POST['save_type']) ? $_POST['save_type'] : 'publish';
    $layout = isset($_POST['layout']) ? json_decode(stripslashes($_POST['layout']), true) : [];
    $title = isset($_POST['event_title']) ? sanitize_text_field($_POST['event_title']) : '';
    $rows = isset($_POST['grid_rows']) ? intval($_POST['grid_rows']) : 67;
    $cols = isset($_POST['grid_cols']) ? intval($_POST['grid_cols']) : 48;
    
    if ($type === 'publish') {
        update_option('cnc_event_title', $title);
        update_option('cnc_grid_rows', $rows);
        update_option('cnc_grid_cols', $cols);
        if (update_option('cnc_floorplan_layout', $layout)) {
            wp_send_json_success('Layout & Title published to Frontend successfully!');
        } else {
            // Check if title changed even if layout didn't
            wp_send_json_success('Layout published (no changes detected).');
        }
    }
    elseif ($type === 'version') {
        $name = sanitize_text_field($_POST['version_name']);
        $versions = get_option('cnc_floorplan_versions', []);
        if (!is_array($versions)) $versions = [];
        
        // Add new version
        $new_version = [
            'name' => $name,
            'timestamp' => current_time('mysql'),
            'layout' => $layout,
            'title' => $title,
            'rows' => $rows,
            'cols' => $cols
        ];
        
        // Prepend to keep newest first
        array_unshift($versions, $new_version);
        
        // Limit to last 20 versions
        $versions = array_slice($versions, 0, 20);
        
        update_option('cnc_floorplan_versions', $versions);
        wp_send_json_success(['message' => 'Version saved!', 'versions' => $versions]);
    }
    elseif ($type === 'delete_version') {
        $index = intval($_POST['version_index']);
        $versions = get_option('cnc_floorplan_versions', []);
        
        if (isset($versions[$index])) {
            array_splice($versions, $index, 1);
            update_option('cnc_floorplan_versions', $versions);
            wp_send_json_success(['message' => 'Version deleted.', 'versions' => $versions]);
        } else {
            wp_send_json_error('Version not found.');
        }
    }
}

function cnc_render_designer_page() {
    // Ensure data function is available
    if (!function_exists('cnc_get_floorplan_data')) {
        require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
    }

    // Get existing layout (Merged with Live Booking Data)
    // This is the "Live Layout" the user requested
    $saved_layout = cnc_get_floorplan_data();
    
    // If empty, try to load from JSON directly as fallback
    if (empty($saved_layout)) {
        $json_file = get_stylesheet_directory() . '/hall3_layout.json';
        if (file_exists($json_file)) {
            $saved_layout = json_decode(file_get_contents($json_file), true);
        }
    }

    $saved_versions = get_option('cnc_floorplan_versions', []);
    $saved_title = get_option('cnc_event_title', 'Hall 3 Floor Plan');
    
    // USE CONSTANTS FOR DIMENSIONS
    $grid_rows = CNC_GRID_ROWS;
    $grid_cols = CNC_GRID_COLS;
    
    // Enqueue scripts manually since we are not in a separate hook anymore
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-resizable');
    wp_enqueue_script('jquery-ui-droppable'); // Ensure droppable is loaded
    
    $is_embed = isset($_GET['embed']) && $_GET['embed'] === 'true';
    if ($is_embed) {
        echo '<style>
            #wpadminbar, #adminmenumain, #wpfooter { display: none !important; }
            #wpcontent, #wpbody-content { margin-left: 0 !important; padding: 0 !important; }
            .wrap > h1, .wrap > hr, .page-title-action { display: none !important; }
            html.wp-toolbar { padding-top: 0 !important; }
            .cnc-designer-toolbar { top: 0 !important; position: sticky; z-index: 100; background: #f0f0f1; padding: 10px 0; border-bottom: 1px solid #ccc; }
            .wrap { margin: 0 20px !important; }
        </style>';
    }
    
    ?>
    <div class="wrap <?php echo $is_embed ? 'cnc-embed-mode' : ''; ?>">
        <h1 class="wp-heading-inline">Floor Plan Designer (Pro)</h1>
        <a href="<?php echo admin_url('edit.php?post_type=cnc_booking&page=cnc-floorplan'); ?>" class="page-title-action">⬅ Back to Manager</a>
        <hr class="wp-header-end">
        
        <div class="cnc-designer-toolbar">
            <div class="toolbar-group">
                <input type="text" id="event-title" value="<?php echo esc_attr($saved_title); ?>" placeholder="Event Title" style="width: 200px; font-weight:bold;">
                <label>Height (m):</label> <input type="number" id="grid-rows" value="<?php echo esc_attr($grid_rows); ?>" style="width: 60px;" readonly title="Fixed by Config">
                <label>Width (m):</label> <input type="number" id="grid-cols" value="<?php echo esc_attr($grid_cols); ?>" style="width: 60px;" readonly title="Fixed by Config">
                <button class="button button-primary button-large" id="btn-publish">🚀 Publish</button>
                <button class="button button-large" id="btn-save-version">💾 Save Ver</button>
            </div>
            
            <div class="toolbar-group">
                <button class="button" id="btn-add-stall" title="Add 3x3 Stall">+ Stall</button>
                <button class="button" id="btn-add-common-area" title="Add Common Area" style="background:#9B59B6; color:white; border-color:#8E44AD;">+ Common Area</button>
                <button class="button" id="btn-add-wall" title="Add Wall/Border Frame" style="background:#34495E; color:white; border-color:#2C3E50;">+ Wall</button>
                <button class="button" id="btn-merge" title="Merge Selected Items (Same ID)">🔗 Merge</button>
                <button class="button" id="btn-json-io" title="Import/Export JSON">JSON</button>
                <button class="button" id="btn-import-default" title="Reset to Default Layout (hall3_layout.json)">📥 Import Default</button>
                <button class="button" id="btn-reload-live" title="Reload Live Data from Database">🔄 Reload Live</button>
                <button class="button" id="btn-clear" style="color: #b32d2e;">🗑️ Clear</button>
            </div>
            
            <span class="spinner" id="save-spinner"></span>
        </div>
        
        <!-- Version Manager -->
        <div class="cnc-versions-panel">
            <h3>Saved Versions</h3>
            <select id="version-select">
                <option value="">-- Select a Version to Load --</option>
                <?php foreach($saved_versions as $idx => $v): ?>
                    <option value="<?php echo $idx; ?>">
                        <?php echo esc_html($v['name'] . ' (' . $v['timestamp'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="button" id="btn-load-version">Load</button>
            <button class="button" id="btn-delete-version" style="color: #b32d2e;">Delete</button>
        </div>

        <div class="cnc-designer-container" id="container">
            <div id="designer-grid" class="designer-grid">
                <!-- Grid cells generated by JS -->
            </div>
            <div id="designer-overlay" class="designer-overlay">
                <!-- Placed items go here -->
            </div>
            <div id="selection-box" class="selection-box"></div>
            <div id="draw-preview" class="draw-preview"></div>
        </div>

        <!-- Properties Modal -->
        <div id="prop-modal" class="prop-modal">
            <h3>Element Properties</h3>
            <div class="form-row">
                <label>Type</label>
                <select id="input-type">
                    <optgroup label="🏢 Stalls (Clickable)">
                        <option value="stall">Stall (Standard)</option>
                    </optgroup>
                    <optgroup label="🚧 Common Areas (Non-Clickable)">
                        <option value="fire-exit">🚨 Fire Exit</option>
                        <option value="entry">🚪 Entry</option>
                        <option value="exit">🚪 Exit</option>
                        <option value="freight-entry">🚚 Freight Entry</option>
                        <option value="washroom">🚻 Washroom / Toilets</option>
                        <option value="stairs">🪜 Stairs / Steps</option>
                        <option value="stage">🎤 Stage / Podium</option>
                        <option value="service">ℹ️ Service / Info Room</option>
                        <option value="registration">📋 Registration Desk</option>
                        <option value="lounge">🛋️ Lounge / Waiting Area</option>
                        <option value="storage">📦 Storage Room</option>
                        <option value="electrical">⚡ Electrical Room</option>
                        <option value="pillar">⬜ Pillar / Obstruction</option>
                        <option value="corridor">➡️ Corridor / Pathway</option>
                        <option value="blank">⬜ Blank Space</option>
                        <option value="custom-area">✏️ Custom Common Area</option>
                    </optgroup>
                    <optgroup label="✨ Special Features">
                        <option value="feature">⭐ Feature / Sponsor Zone</option>
                        <option value="food-court">🍽️ Food Court</option>
                        <option value="networking">🤝 Networking Zone</option>
                    </optgroup>
                    <optgroup label="🧱 Walls & Boundaries">
                        <option value="wall">🧱 Wall / Border Frame</option>
                        <option value="boundary">⬜ Boundary Line</option>
                        <option value="partition">➖ Partition</option>
                    </optgroup>
                </select>
            </div>
            
            <!-- Text Direction -->
            <div class="form-row">
                <label>Text Direction</label>
                <div class="text-direction-btns">
                    <button type="button" class="btn-text-dir active" data-dir="horizontal" title="Horizontal">➡️ Horizontal</button>
                    <button type="button" class="btn-text-dir" data-dir="vertical-up" title="Vertical (Up)">⬆️ Vertical Up</button>
                    <button type="button" class="btn-text-dir" data-dir="vertical-down" title="Vertical (Down)">⬇️ Vertical Down</button>
                </div>
                <input type="hidden" id="input-text-dir" value="horizontal">
            </div>
            <div class="form-row">
                <label>ID / Label (Same ID = Merged Stall)</label>
                <input type="text" id="input-id" placeholder="e.g. 3.A 1">
            </div>
            <div class="form-row">
                <label>Display Label (Optional - shows on map)</label>
                <input type="text" id="input-display-label" placeholder="e.g. WASHROOMS, FIRE EXIT">
                <small style="color:#666;">Leave empty to use ID as label</small>
            </div>
            <div class="form-row">
                <label>Dimensions</label>
                <span id="disp-dims">--</span>
            </div>
            <div class="form-row stall-only-option">
                <label>Status / Color Theme (Stalls Only)</label>
                <select id="input-color-theme">
                    <option value="available">Available (Green)</option>
                    <option value="booked">Booked (Grey)</option>
                    <option value="reserved">Reserved (Orange)</option>
                    <option value="pending">Pending (Orange)</option>
                </select>
            </div>
            <!-- Wall Options -->
            <div class="form-row wall-options" style="display:none;">
                <label>🧱 Wall Settings</label>
                <div style="background:#f5f5f5; padding:12px; border-radius:6px; margin-top:5px;">
                    <div style="margin-bottom:10px;">
                        <label style="font-size:12px;">Border Thickness</label>
                        <select id="input-wall-thickness" style="width:100%; margin-top:3px;">
                            <option value="1">1px - Thin Line</option>
                            <option value="2">2px - Light</option>
                            <option value="3" selected>3px - Medium</option>
                            <option value="4">4px - Bold</option>
                            <option value="5">5px - Heavy</option>
                            <option value="6">6px - Extra Heavy</option>
                            <option value="8">8px - Thick</option>
                            <option value="10">10px - Very Thick</option>
                        </select>
                    </div>
                    <div style="margin-bottom:10px;">
                        <label style="font-size:12px;">Border Style</label>
                        <select id="input-wall-style" style="width:100%; margin-top:3px;">
                            <option value="solid" selected>Solid ━━━</option>
                            <option value="dashed">Dashed ┅┅┅</option>
                            <option value="dotted">Dotted ┈┈┈</option>
                            <option value="double">Double ═══</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12px;">Wall Color</label>
                        <div style="display:flex; gap:8px; margin-top:3px;">
                            <input type="color" id="input-wall-color" value="#333333" style="width:50px; height:30px; padding:0; border:1px solid #ccc;">
                            <select id="input-wall-color-preset" style="flex:1;">
                                <option value="#333333">Dark Gray (Default)</option>
                                <option value="#000000">Black</option>
                                <option value="#666666">Medium Gray</option>
                                <option value="#E74C3C">Red</option>
                                <option value="#3498DB">Blue</option>
                                <option value="#27AE60">Green</option>
                                <option value="#F39C12">Orange</option>
                                <option value="#9B59B6">Purple</option>
                            </select>
                        </div>
                    </div>
                </div>
                <small style="color:#666; margin-top:5px; display:block;">Walls are transparent frames - they don't cover stalls underneath</small>
            </div>
            
            <div class="form-row common-area-colors" style="display:none;">
                <label>Common Area Color Presets</label>
                <select id="input-common-color">
                    <option value="">-- Select Preset Color --</option>
                    <option value="#E74C3C">🔴 Red (Fire Exit)</option>
                    <option value="#3498DB">🔵 Blue (Washroom)</option>
                    <option value="#9B59B6">🟣 Purple (Info Room)</option>
                    <option value="#F39C12">🟠 Orange (Entry/Exit)</option>
                    <option value="#1ABC9C">🟢 Teal (Stage)</option>
                    <option value="#34495E">⚫ Dark Gray (Pillar)</option>
                    <option value="#95A5A6">⚪ Light Gray (Freight/Storage)</option>
                    <option value="#27AE60">🟢 Green (Corridor)</option>
                    <option value="#FFC107">🟡 Yellow (Feature)</option>
                    <option value="#E91E63">💜 Magenta (VIP/Sponsor)</option>
                </select>
            </div>
            <div class="form-row">
                <label>Stall Category (Auto-Color)</label>
                <select id="input-category-color">
                    <option value="">-- Select Category --</option>
                    <option value="#E91E63">AS - Premium Large (Pink)</option>
                    <option value="#4CAF50">BS - Large Suite (Green)</option>
                    <option value="#ee7c00">DS - Medium Suite (Orange)</option>
                    <option value="#2196F3">ES - Standard Large (Blue)</option>
                    <option value="#FFC107">FS - Standard Medium (Yellow)</option>
                    <option value="#9C27B0">GS - Compact (Purple)</option>
                    <option value="#607D8B">AF - Shell Scheme (Grey-Blue)</option>
                    <option value="#f7abad">C - Special (Light Pink)</option>
                </select>
                <small style="color:#666;">Selecting a category sets the custom color automatically.</small>
            </div>
            <div class="form-row">
                <label>Custom Color (Manual Override)</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="color" id="input-custom-color" value="#ffffff" style="width:50px; height:30px; padding:0;">
                    <input type="text" id="input-custom-color-hex" placeholder="#FFFFFF" style="width:80px; font-family:monospace;">
                    <button class="button" id="btn-clear-color" style="font-size:11px;">Clear</button>
                </div>
            </div>
            <div class="form-row">
                <label>Text Color</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="color" id="input-text-color" value="#ffffff" style="width:50px; height:30px; padding:0;">
                    <select id="input-text-color-preset" style="flex:1;">
                        <option value="#ffffff">White</option>
                        <option value="#000000">Black</option>
                        <option value="#333333">Dark Gray</option>
                        <option value="#f0f0f0">Light Gray</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label>Font Size</label>
                <select id="input-font-size">
                    <option value="auto">Auto (Based on Size)</option>
                    <option value="8">8px - Tiny</option>
                    <option value="10">10px - Small</option>
                    <option value="12">12px - Medium</option>
                    <option value="14">14px - Large</option>
                    <option value="18">18px - X-Large</option>
                </select>
            </div>
            <div class="form-row">
                <label>
                    <input type="checkbox" id="input-show-border"> Show Border
                </label>
                <input type="color" id="input-border-color" value="#333333" style="width:50px; height:25px; padding:0; margin-left:10px;">
            </div>
            <div class="form-actions">
                <button class="button button-primary" id="btn-add">Update / Add</button>
            </div>
            <div class="form-actions" style="margin-top:10px; border-top:1px solid #eee; padding-top:10px;">
                <button class="button" id="btn-cancel">Cancel</button>
                <button class="button" id="btn-delete" style="color:red; border-color:red;">Delete</button>
            </div>
        </div>

        <!-- JSON Modal -->
        <div id="json-modal" class="prop-modal" style="width: 500px;">
            <h3>JSON Import/Export</h3>
            <textarea id="json-data" style="width:100%; height: 300px; font-family:monospace;"></textarea>
            <div class="form-actions" style="margin-top:10px;">
                <button class="button button-primary" id="btn-import-json">Import</button>
                <button class="button" onclick="jQuery('#json-modal').hide()">Close</button>
            </div>
        </div>

        <!-- Context Menu -->
        <div id="context-menu" class="context-menu">
            <div class="context-menu-item" id="ctx-edit">✏️ Edit Properties</div>
            <div class="context-menu-item" id="ctx-merge">🔗 Merge Selected</div>
            <div class="context-menu-item danger" id="ctx-delete">🗑️ Delete</div>
        </div>

    </div>

    <style>
        /* === SHARED DESIGN SYSTEM (Matches Frontend) === */
        :root {
            --bms-green: <?php echo CNC_COLOR_AVAILABLE; ?>;
            --bms-orange: <?php echo CNC_COLOR_PENDING; ?>;
            --bms-grey: <?php echo CNC_COLOR_BOOKED; ?>;
            --bms-dark: #333;
            --bms-red: <?php echo CNC_COLOR_EXIT; ?>;
            --bms-blue: <?php echo CNC_COLOR_WASHROOM; ?>;
            --grid-gap: <?php echo CNC_GRID_GAP_PX; ?>px;       /* CRITICAL: Must match frontend gap */
            --cell-size: 20px;     /* Visual size in backend */
        }

        .cnc-designer-toolbar { 
            margin: 20px 0; padding: 15px; background: #fff; border: 1px solid #ccd0d4; 
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 1px rgba(0,0,0,0.04);
            position: sticky; top: 32px; z-index: 100;
        }
        .toolbar-group { display: flex; gap: 10px; align-items: center; }
        
        .cnc-versions-panel {
            margin-bottom: 20px; padding: 10px 15px; background: #f0f0f1; border: 1px solid #ccd0d4;
            display: flex; align-items: center; gap: 10px;
        }
        .cnc-versions-panel h3 { margin: 0; font-size: 14px; margin-right: 10px; }
        
        .cnc-designer-container {
            position: relative;
            /* Width/Height set by JS based on grid */
            border: 1px solid #ccc;
            background: #f0f0f0; /* Gap color (Grid Lines) */
            user-select: none;
            overflow: auto; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 50px;
        }

        .designer-grid {
            display: grid;
            /* Columns/Rows set by JS */
            gap: var(--grid-gap);
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: auto;
            padding: var(--grid-gap); /* Outer padding */
            box-sizing: border-box;
        }

        .grid-cell {
            background-color: #fff; /* White cells */
            box-sizing: border-box;
            /* Ensure cells don't collapse */
            min-width: 0; min-height: 0;
        }
        .grid-cell:hover { background-color: #f9f9f9; }

        .designer-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            padding: var(--grid-gap); /* Match grid padding */
            box-sizing: border-box;
            
            /* CSS GRID FOR ITEMS - The Magic Fix */
            display: grid;
            gap: var(--grid-gap);
            /* Columns/Rows set by JS to match background grid */
        }

        /* ITEM STYLES - EXACT MATCH TO FRONTEND */
        .design-item {
            /* Positioned by Grid Area now! */
            position: relative; 
            background: var(--bms-green);
            border: 1px solid #27AE60;
            color: white;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: move;
            pointer-events: auto;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            z-index: 10;
            font-weight: 600;
            text-align: center;
            line-height: 1.1;
            padding: 2px;
            border-radius: 2px;
        }
        /* When dragging, jQuery UI makes it absolute. We need to handle that. */
        .design-item.ui-draggable-dragging {
            position: absolute !important;
            width: var(--drag-width) !important;
            height: var(--drag-height) !important;
            z-index: 1000;
            opacity: 0.8;
        }
        .design-item:hover { opacity: 0.9; z-index: 20; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .design-item.selected { 
            border: 2px solid #2196F3 !important; 
            z-index: 30; 
            box-shadow: 0 0 0 2px #fff inset; 
        }

        /* Status Colors */
        .status-available { background: var(--bms-green); border-color: #27AE60; }
        .status-pending { background: var(--bms-orange); border-color: #D35400; }
        .status-booked { background: var(--bms-grey); border-color: #95A5A6; color: #555; }
        .status-reserved { background: var(--bms-orange); border-color: #D35400; }
        
        /* Type Overrides - Common Areas */
        .item-fire-exit, .status-exit { background: #E74C3C; border-color: #C0392B; font-weight: bold; }
        .item-washroom, .status-washroom { background: #3498DB; border-color: #2980B9; }
        .item-service { background: #9B59B6; border-color: #8E44AD; font-size: 9px; }
        .item-entry { background: #F39C12; border-color: #E67E22; font-weight: 800; letter-spacing: 1px; }
        .item-exit { background: #E67E22; border-color: #D35400; font-weight: 800; }
        .item-freight-entry { background: #95A5A6; border-color: #7F8C8D; font-weight: 600; }
        .item-stairs { background: #34495E; border-color: #2C3E50; }
        .item-stage { background: #1ABC9C; border-color: #16A085; font-weight: 700; }
        .item-registration { background: #9B59B6; border-color: #8E44AD; }
        .item-lounge { background: #27AE60; border-color: #1E8449; }
        .item-storage { background: #7F8C8D; border-color: #626567; }
        .item-electrical { background: #F1C40F; border-color: #D4AC0D; color: #333; }
        .item-pillar { background: #34495E; border-color: #1C2833; }
        .item-corridor { background: #27AE60; border-color: #1E8449; opacity: 0.7; }
        .item-blank { background: #ECF0F1; border-color: #BDC3C7; color: #666; }
        .item-custom-area { background: #8E44AD; border-color: #6C3483; }
        .item-feature { background: #FFC107; border-color: #F39C12; color: #333; }
        .item-food-court { background: #E74C3C; border-color: #C0392B; }
        .item-networking { background: #2ECC71; border-color: #27AE60; }
        
        /* Wall / Border Frame Styles - Transparent with border only */
        .item-wall, .item-boundary, .item-partition {
            background: transparent !important;
            pointer-events: none; /* Allow clicking through to stalls underneath */
            z-index: 50; /* Above stalls but below selection */
        }
        .item-wall {
            border: 3px solid #333333;
        }
        .item-boundary {
            border: 2px dashed #666666;
        }
        .item-partition {
            border: 2px solid #999999;
        }
        /* Wall label styling */
        .item-wall .item-label,
        .item-boundary .item-label,
        .item-partition .item-label {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255,255,255,0.9);
            padding: 2px 6px;
            font-size: 9px;
            border-radius: 3px;
            white-space: nowrap;
            color: #333;
        }
        /* Make walls selectable in designer mode */
        .design-item.item-wall,
        .design-item.item-boundary,
        .design-item.item-partition {
            pointer-events: auto;
        }
        /* Wall indicator icon */
        .design-item.wall-type::before {
            content: '🧱';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            opacity: 0.3;
        }
        
        /* Common Area Badge - Non-clickable indicator */
        .design-item.common-area::after {
            content: '🔒';
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 8px;
            opacity: 0.7;
        }
        
        /* Text Direction Classes */
        .text-vertical-up {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
        }
        .text-vertical-down {
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }
        .text-horizontal {
            writing-mode: horizontal-tb;
        }
        
        /* Text Direction Buttons in Modal */
        .text-direction-btns {
            display: flex;
            gap: 5px;
        }
        .btn-text-dir {
            flex: 1;
            padding: 8px 5px;
            border: 2px solid #ddd;
            background: #f9f9f9;
            cursor: pointer;
            border-radius: 4px;
            font-size: 11px;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-text-dir:hover {
            background: #e9e9e9;
            border-color: #bbb;
        }
        .btn-text-dir.active {
            background: #0073aa;
            color: white;
            border-color: #005177;
        }

        /* Resize Handles */
        .ui-resizable-handle { display: none; }
        .design-item.selected .ui-resizable-handle { display: block; }
        .ui-resizable-se { bottom: 0; right: 0; width: 10px; height: 10px; background: #333; cursor: se-resize; position: absolute; }

        .selection-box {
            position: absolute;
            background: rgba(33, 150, 243, 0.3);
            border: 2px solid #2196F3;
            display: none;
            pointer-events: none;
            z-index: 100;
        }

        .prop-modal {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 1000;
            width: 380px;
            max-height: 90vh;
            overflow-y: auto;
            display: none;
            border: 1px solid #ccc;
        }
        .form-row { margin-bottom: 15px; }
        .form-row label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-row input, .form-row select { width: 100%; padding: 5px; }
        .form-actions { display: flex; justify-content: space-between; gap: 10px; }
        .form-actions button { flex: 1; }

        /* Context Menu */
        .context-menu {
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            z-index: 2000;
            display: none;
            min-width: 150px;
        }
        .context-menu-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .context-menu-item:hover {
            background-color: #f0f0f0;
        }
        .context-menu-item:last-child {
            border-bottom: none;
        }
        .context-menu-item.danger {
            color: #d32f2f;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        let ROWS = <?php echo intval($grid_rows); ?>;
        let COLS = <?php echo intval($grid_cols); ?>;
        const CELL_SIZE = 20;
        const GAP = <?php echo CNC_GRID_GAP_PX; ?>; // CRITICAL: Must match CSS --grid-gap
        const UNIT_SIZE = CELL_SIZE + GAP;
        
        const gridEl = $('#designer-grid');
        const overlayEl = $('#designer-overlay');
        const selectionEl = $('#selection-box');
        const modal = $('#prop-modal');
        const container = $('#container');
        
        // Update Grid Size
        function updateGridSize() {
            // ROWS/COLS are fixed by PHP now
            
            // Calculate total container size including gaps
            // Width = (Cols * Cell) + ((Cols-1) * Gap) + (2 * Padding)
            // Simplified: Cols * UnitSize + Gap (if padding=gap)
            // Let's stick to: Inner Content = Cols * Cell + (Cols-1)*Gap
            // Padding = Gap
            // Total Width = (Cols * CELL_SIZE) + ((Cols + 1) * GAP)
            
            let totalW = (COLS * CELL_SIZE) + ((COLS + 1) * GAP);
            let totalH = (ROWS * CELL_SIZE) + ((ROWS + 1) * GAP);
            
            container.css({ width: totalW, height: totalH });
            
            let gridTemplate = { 
                gridTemplateColumns: `repeat(${COLS}, ${CELL_SIZE}px)`,
                gridTemplateRows: `repeat(${ROWS}, ${CELL_SIZE}px)`
            };
            
            gridEl.css(gridTemplate);
            overlayEl.css(gridTemplate); // Apply Grid to Overlay too!
            
            // Regenerate Grid Cells
            gridEl.empty();
            for(let i=0; i<ROWS*COLS; i++) {
                let r = Math.floor(i / COLS) + 1;
                let c = (i % COLS) + 1;
                gridEl.append(`<div class="grid-cell" data-r="${r}" data-c="${c}"></div>`);
            }
        }
        
        // Initial Grid
        updateGridSize();
        
        // Listen for changes
        $('#grid-rows, #grid-cols').change(updateGridSize);
        
        // State
        let items = <?php echo json_encode($saved_layout ?: []); ?>;
        let savedVersions = <?php echo json_encode($saved_versions ?: []); ?>;
        let selectedIndices = [];
        let isDragging = false;
        let startCell = null;
        let currentSelectionRect = null;

        // Common Area Types (non-clickable on frontend)
        const COMMON_AREA_TYPES = [
            'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
            'stairs', 'stage', 'service', 'registration', 'lounge', 
            'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
            'food-court', 'networking'
        ];
        
        // Wall Types (transparent border-only frames)
        const WALL_TYPES = ['wall', 'boundary', 'partition'];
        
        // Helper: Check if type is wall
        function isWallType(type) {
            return WALL_TYPES.includes(type);
        }
        
        // Helper: Darken a hex color
        function darkenColor(hex, percent) {
            hex = hex.replace('#', '');
            let r = parseInt(hex.substring(0,2), 16);
            let g = parseInt(hex.substring(2,4), 16);
            let b = parseInt(hex.substring(4,6), 16);
            r = Math.floor(r * (100 - percent) / 100);
            g = Math.floor(g * (100 - percent) / 100);
            b = Math.floor(b * (100 - percent) / 100);
            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        
        // Helper: Check if type is common area
        function isCommonAreaType(type) {
            return COMMON_AREA_TYPES.includes(type);
        }
        
        // Toggle form sections based on type
        function updateFormSections(type) {
            // Hide all conditional sections first
            $('.stall-only-option').hide();
            $('.common-area-colors').hide();
            $('.wall-options').hide();
            
            if (isWallType(type)) {
                // Show wall options
                $('.wall-options').show();
            } else if (isCommonAreaType(type)) {
                // Show common area colors
                $('.common-area-colors').show();
            } else {
                // Show stall options
                $('.stall-only-option').show();
            }
        }

        // 2. Render Items
        function renderItems() {
            overlayEl.empty();
            items.forEach((item, index) => {
                createDOMItem(item, index);
            });
        }

        function createDOMItem(item, index) {
            // CSS GRID POSITIONING
            // grid-column: start / span width
            // grid-row: start / span height
            
            // Use display_label if set, otherwise use ID
            let label = item.display_label || (item.id || '').replace('3.', '');
            
            // Check if this is a common area (non-clickable) or wall
            let isCommonArea = COMMON_AREA_TYPES.includes(item.type);
            let isWall = isWallType(item.type);
            
            let extraClasses = isCommonArea ? 'common-area' : '';
            if (isWall) extraClasses += ' wall-type';
            
            let el = $(`<div class="design-item item-${item.type} ${extraClasses}" data-index="${index}" title="${item.id}">
                <span class="item-label">${label}</span>
            </div>`);
            
            // Apply Grid Styles directly
            el.css({
                'grid-column': `${item.c} / span ${item.w}`,
                'grid-row': `${item.r} / span ${item.h}`
            });
            
            // Store pixel dimensions for dragging helper
            let pixelW = (item.w * CELL_SIZE) + ((item.w - 1) * GAP);
            let pixelH = (item.h * CELL_SIZE) + ((item.h - 1) * GAP);
            el.css('--drag-width', pixelW + 'px');
            el.css('--drag-height', pixelH + 'px');
            
            // WALL RENDERING - Transparent with border only
            if (isWall) {
                el.css('background', 'transparent');
                let wallThickness = item.wall_thickness || 3;
                let wallStyle = item.wall_style || 'solid';
                let wallColor = item.wall_color || '#333333';
                el.css('border', `${wallThickness}px ${wallStyle} ${wallColor}`);
                // Walls have higher z-index to appear above stalls
                el.css('z-index', '50');
            }
            // Apply Status Class if exists (only for stalls)
            else if(item.type === 'stall' && item.status) {
                el.addClass('status-' + item.status);
            } else if(item.type === 'stall') {
                el.addClass('status-available');
            }

            // Apply Custom Color (not for walls)
            if (item.custom_color && !isWall) {
                el.css('background-color', item.custom_color);
                // Darken border color
                el.css('border-color', darkenColor(item.custom_color, 20));
            }
            
            // Apply Text Color
            if (item.text_color) {
                el.css('color', item.text_color);
            }
            
            // Apply Font Size
            if (item.font_size && item.font_size !== 'auto') {
                el.css('font-size', item.font_size + 'px');
            }
            
            // Apply Text Direction
            if (item.text_dir) {
                el.find('.item-label').addClass('text-' + item.text_dir);
            }
            
            // Apply Border
            if (item.show_border && item.border_color) {
                el.css('border', '2px solid ' + item.border_color);
            } else if (!item.show_border && isCommonArea) {
                el.css('border', 'none');
            }
            
            // Add visual indicator for stall availability
            if (item.type === 'stall' && item.custom_color && (item.status === 'available' || !item.status)) {
                el.append('<div style="position:absolute; bottom:0; left:0; width:100%; height:3px; background:#27AE60;"></div>');
            }
            
            if(selectedIndices.includes(index)) el.addClass('selected');
            
            // Click Selection (Modified for Multi-select)
            el.on('mousedown', function(e) {
                // If right click, don't clear selection if item is already selected
                if(e.button === 2 && selectedIndices.includes(index)) {
                    return;
                }

                e.stopPropagation();
                
                if (e.ctrlKey || e.shiftKey) {
                    // Toggle selection
                    if (selectedIndices.includes(index)) {
                        selectedIndices = selectedIndices.filter(i => i !== index);
                    } else {
                        selectedIndices.push(index);
                    }
                } else {
                    // Single select
                    selectedIndices = [index];
                }
                renderSelectionState();
            });

            // Context Menu
            el.on('contextmenu', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Ensure clicked item is selected
                if (!selectedIndices.includes(index)) {
                    selectedIndices = [index];
                    renderSelectionState();
                }
                
                // Show Context Menu
                $('#context-menu').css({
                    top: e.pageY,
                    left: e.pageX,
                    display: 'block'
                });
            });
            
            // Double click to edit
            el.on('dblclick', function(e) {
                e.stopPropagation();
                selectedIndices = [index];
                renderSelectionState();
                openModal(true);
            });

            // Draggable
            el.draggable({
                // grid: [UNIT_SIZE, UNIT_SIZE], // Not needed for CSS Grid approach, we calc manually
                containment: ".cnc-designer-container",
                helper: "clone", // Drag a clone so original stays in grid until drop
                appendTo: "body", // Append to body to avoid grid constraints during drag
                start: function(event, ui) {
                    if(!selectedIndices.includes(index)) {
                        selectedIndices = [index];
                        renderSelectionState();
                    }
                    // Style the helper to look like the item
                    ui.helper.css({
                        width: pixelW,
                        height: pixelH,
                        zIndex: 1000
                    });
                    $(this).css('opacity', 0.3); // Dim original
                },
                stop: function(event, ui) {
                    $(this).css('opacity', 1);
                }
            });

            // Resizable
            el.resizable({
                grid: [UNIT_SIZE, UNIT_SIZE],
                containment: ".cnc-designer-container",
                handles: "se",
                stop: function(event, ui) {
                    // Calculate new W/H based on pixel size
                    // Width = (w * CELL) + ((w-1) * GAP)
                    // Width = w * (CELL + GAP) - GAP
                    // Width + GAP = w * UNIT_SIZE
                    // w = (Width + GAP) / UNIT_SIZE
                    
                    let newW = Math.round((ui.size.width + GAP) / UNIT_SIZE);
                    let newH = Math.round((ui.size.height + GAP) / UNIT_SIZE);
                    
                    items[index].w = Math.max(1, newW);
                    items[index].h = Math.max(1, newH);
                    
                    // Update Area/Price if stall
                    if(items[index].type === 'stall') {
                        items[index].area = items[index].w * items[index].h; // Approx area in m2
                        items[index].price = items[index].area * 11500;
                        items[index].dim = `${items[index].w}x${items[index].h}`;
                    }
                    
                    renderItems();
                }
            });
            
            // Make Grid Droppable
            container.droppable({
                accept: ".design-item",
                drop: function(event, ui) {
                    let draggedItem = ui.draggable;
                    let index = draggedItem.data('index');
                    
                    // Calculate Grid Position based on Offset relative to Container
                    let containerOffset = container.offset();
                    // Add scroll offset if container is scrolled
                    let relLeft = ui.offset.left - containerOffset.left + container.scrollLeft();
                    let relTop = ui.offset.top - containerOffset.top + container.scrollTop();
                    
                    // Convert to Grid Coords
                    // Pos = (Col-1) * UnitSize + Padding
                    // Col-1 = (Pos - Padding) / UnitSize
                    let c = Math.round((relLeft - GAP) / UNIT_SIZE) + 1;
                    let r = Math.round((relTop - GAP) / UNIT_SIZE) + 1;
                    
                    // Clamp to grid
                    c = Math.max(1, Math.min(c, COLS - items[index].w + 1));
                    r = Math.max(1, Math.min(r, ROWS - items[index].h + 1));
                    
                    // Update Data
                    items[index].c = c;
                    items[index].r = r;
                    
                    renderItems();
                }
            });
            
            overlayEl.append(el);
        }
        
        function renderSelectionState() {
            $('.design-item').removeClass('selected');
            selectedIndices.forEach(idx => {
                $(`.design-item[data-index="${idx}"]`).addClass('selected');
            });
            
            // Show/Hide Merge Button
            if(selectedIndices.length > 1) {
                $('#btn-merge').show();
            } else {
                $('#btn-merge').hide();
            }
        }
        renderItems();

        // Merge Logic
        $('#btn-merge').click(function() {
            if(selectedIndices.length < 2) return;
            
            let firstIdx = selectedIndices[0];
            let targetID = items[firstIdx].id;
            let targetType = items[firstIdx].type;
            let targetStatus = items[firstIdx].status;
            
            let newID = prompt("Enter ID to merge selected items into:", targetID);
            if(newID === null) return; // Cancelled
            
            selectedIndices.forEach(idx => {
                items[idx].id = newID;
                // Optionally sync type/status too
                items[idx].type = targetType;
                items[idx].status = targetStatus;
            });
            
            renderItems();
            alert(`Merged ${selectedIndices.length} items into ID: ${newID}`);
        });

        // Context Menu Actions
        $(document).on('click', function() {
            $('#context-menu').hide();
        });

        $('#ctx-edit').click(function() {
            if(selectedIndices.length > 0) openModal(true);
        });

        $('#ctx-merge').click(function() {
            $('#btn-merge').click();
        });

        $('#ctx-delete').click(function() {
            if(selectedIndices.length === 0) return;
            if(!confirm(`Delete ${selectedIndices.length} selected items?`)) return;
            
            // Sort indices descending to delete safely
            selectedIndices.sort((a, b) => b - a);
            
            selectedIndices.forEach(idx => {
                items.splice(idx, 1);
            });
            
            selectedIndices = [];
            renderItems();
            renderSelectionState();
        });

        // Keyboard Shortcuts
        $(document).on('keydown', function(e) {
            if((e.key === 'Delete' || e.key === 'Backspace') && selectedIndices.length > 0) {
                // Don't delete if editing text in an input
                if($(e.target).is('input, textarea')) return;
                
                $('#ctx-delete').click();
            }
        });

        // 3. Grid Interaction (Marquee Selection)
        gridEl.on('mousedown', '.grid-cell', function(e) {
            e.preventDefault();
            isDragging = true;
            let r = parseInt($(this).data('r'));
            let c = parseInt($(this).data('c'));
            startCell = {r, c};
            
            // Start Marquee
            selectionEl.show();
            if(!e.shiftKey && !e.ctrlKey) {
                selectedIndices = [];
                renderSelectionState();
            }
            updateMarquee(r, c);
        });

        gridEl.on('mouseenter', '.grid-cell', function(e) {
            if(!isDragging) return;
            let r = parseInt($(this).data('r'));
            let c = parseInt($(this).data('c'));
            updateMarquee(r, c);
        });

        $(document).on('mouseup', function() {
            if(isDragging) {
                isDragging = false;
                if(currentSelectionRect) {
                    // Select items inside rect
                    selectItemsInRect(currentSelectionRect);
                    
                    // If empty space selected, open modal to add
                    if(selectedIndices.length === 0) {
                        openModal(false);
                    } else {
                        selectionEl.hide();
                        currentSelectionRect = null;
                    }
                }
            }
        });
        
        function updateMarquee(endR, endC) {
            let minR = Math.min(startCell.r, endR);
            let maxR = Math.max(startCell.r, endR);
            let minC = Math.min(startCell.c, endC);
            let maxC = Math.max(startCell.c, endC);
            
            currentSelectionRect = { r: minR, c: minC, h: maxR - minR + 1, w: maxC - minC + 1 };
            
            // Marquee Visuals (Use same math as items)
            // But wait, marquee is absolute positioned on top of grid
            // We need to convert grid coords to pixels including gaps
            
            let top = (minR - 1) * UNIT_SIZE;
            let left = (minC - 1) * UNIT_SIZE;
            let width = (currentSelectionRect.w * CELL_SIZE) + ((currentSelectionRect.w - 1) * GAP);
            let height = (currentSelectionRect.h * CELL_SIZE) + ((currentSelectionRect.h - 1) * GAP);

            selectionEl.css({
                top: top, left: left, height: height, width: width
            }).show();
        }
        
        function selectItemsInRect(rect) {
            selectedIndices = [];
            items.forEach((item, idx) => {
                if (item.r < rect.r + rect.h && item.r + item.h > rect.r &&
                    item.c < rect.c + rect.w && item.c + item.w > rect.c) {
                    selectedIndices.push(idx);
                }
            });
            renderSelectionState();
        }

        // 5. Modal & Actions
        function openModal(isEdit) {
            if(isEdit) {
                // Edit first selected item (or common props)
                let item = items[selectedIndices[0]];
                $('#input-type').val(item.type || 'stall');
                $('#input-id').val(item.id);
                $('#input-display-label').val(item.display_label || '');
                $('#input-color-theme').val(item.status || 'available');
                
                let cColor = item.custom_color || '#ffffff';
                $('#input-custom-color').val(cColor);
                $('#input-custom-color-hex').val(cColor);
                
                // Text Direction
                let textDir = item.text_dir || 'horizontal';
                $('#input-text-dir').val(textDir);
                $('.btn-text-dir').removeClass('active');
                $(`.btn-text-dir[data-dir="${textDir}"]`).addClass('active');
                
                // Text Color
                $('#input-text-color').val(item.text_color || '#ffffff');
                
                // Font Size
                $('#input-font-size').val(item.font_size || 'auto');
                
                // Border
                $('#input-show-border').prop('checked', item.show_border || false);
                $('#input-border-color').val(item.border_color || '#333333');
                
                // Wall Properties
                $('#input-wall-thickness').val(item.wall_thickness || '3');
                $('#input-wall-style').val(item.wall_style || 'solid');
                $('#input-wall-color').val(item.wall_color || '#333333');
                $('#input-wall-color-preset').val(item.wall_color || '#333333');
                
                // Common area preset color
                if(isCommonAreaType(item.type) && item.custom_color) {
                    $('#input-common-color').val(item.custom_color);
                } else {
                    $('#input-common-color').val('');
                }
                
                // Sync category dropdown
                let found = false;
                $('#input-category-color option').each(function() {
                    if($(this).val().toLowerCase() === cColor.toLowerCase()) {
                        $('#input-category-color').val($(this).val());
                        found = true;
                    }
                });
                if(!found) $('#input-category-color').val("");

                $('#disp-dims').text(`${item.w}m x ${item.h}m`);
                $('#btn-add').text('Update');
                $('#btn-delete').show();
                
                // Update form sections based on type
                updateFormSections(item.type || 'stall');
            } else {
                // Add New
                $('#input-type').val('stall');
                $('#input-id').val('');
                $('#input-display-label').val('');
                $('#input-color-theme').val('available');
                $('#input-custom-color').val('#ffffff');
                $('#input-custom-color-hex').val('#ffffff');
                $('#input-category-color').val("");
                $('#input-common-color').val("");
                
                // Reset text direction
                $('#input-text-dir').val('horizontal');
                $('.btn-text-dir').removeClass('active');
                $('.btn-text-dir[data-dir="horizontal"]').addClass('active');
                
                // Reset text color, font size, border
                $('#input-text-color').val('#ffffff');
                $('#input-font-size').val('auto');
                $('#input-show-border').prop('checked', false);
                $('#input-border-color').val('#333333');
                
                // Reset wall settings
                $('#input-wall-thickness').val('3');
                $('#input-wall-style').val('solid');
                $('#input-wall-color').val('#333333');
                $('#input-wall-color-preset').val('#333333');
                
                $('#disp-dims').text(`${currentSelectionRect.w}m x ${currentSelectionRect.h}m`);
                $('#btn-add').text('Add');
                $('#btn-delete').hide();
                
                // Show stall options by default
                updateFormSections('stall');
            }
            modal.show();
            $('#input-id').focus();
        }
        
        // Type change handler
        $('#input-type').change(function() {
            updateFormSections($(this).val());
        });
        
        // Text Direction Buttons
        $('.btn-text-dir').click(function(e) {
            e.preventDefault();
            $('.btn-text-dir').removeClass('active');
            $(this).addClass('active');
            $('#input-text-dir').val($(this).data('dir'));
        });

        // Sync Category -> Custom Color
        $('#input-category-color').change(function() {
            let val = $(this).val();
            if(val) {
                $('#input-custom-color').val(val);
                $('#input-custom-color-hex').val(val);
            }
        });
        
        // Sync Common Area Preset -> Custom Color
        $('#input-common-color').change(function() {
            let val = $(this).val();
            if(val) {
                $('#input-custom-color').val(val);
                $('#input-custom-color-hex').val(val);
            }
        });

        // Sync Custom Color -> Hex Input
        $('#input-custom-color').change(function() {
             let val = $(this).val();
             $('#input-custom-color-hex').val(val);
             let found = false;
             $('#input-category-color option').each(function() {
                if($(this).val().toLowerCase() === val.toLowerCase()) {
                    $('#input-category-color').val($(this).val());
                    found = true;
                }
             });
             if(!found) $('#input-category-color').val("");
        });
        
        // Sync Hex Input -> Color Picker
        $('#input-custom-color-hex').on('blur change', function() {
            let val = $(this).val();
            if(/^#[0-9A-Fa-f]{6}$/.test(val)) {
                $('#input-custom-color').val(val);
            }
        });
        
        // Sync Text Color Preset -> Picker
        $('#input-text-color-preset').change(function() {
            $('#input-text-color').val($(this).val());
        });
        
        // Sync Wall Color Preset -> Picker
        $('#input-wall-color-preset').change(function() {
            $('#input-wall-color').val($(this).val());
        });
        
        // Sync Wall Color Picker -> Preset
        $('#input-wall-color').change(function() {
            let val = $(this).val();
            let found = false;
            $('#input-wall-color-preset option').each(function() {
                if($(this).val().toLowerCase() === val.toLowerCase()) {
                    $('#input-wall-color-preset').val($(this).val());
                    found = true;
                }
            });
            if(!found) $('#input-wall-color-preset').val($('#input-wall-color-preset option:first').val());
        });

        $('#btn-cancel').click(function() { modal.hide(); });
        
        $('#btn-clear-color').click(function(e) {
            e.preventDefault();
            $('#input-custom-color').val('#ffffff');
            $('#input-custom-color-hex').val('#ffffff');
        });
        
        $('#btn-add').click(function() {
            let type = $('#input-type').val();
            let id = $('#input-id').val();
            let displayLabel = $('#input-display-label').val().trim();
            let status = isCommonAreaType(type) || isWallType(type) ? 'service' : $('#input-color-theme').val();
            let customColor = $('#input-custom-color').val();
            let textDir = $('#input-text-dir').val();
            let textColor = $('#input-text-color').val();
            let fontSize = $('#input-font-size').val();
            let showBorder = $('#input-show-border').is(':checked');
            let borderColor = $('#input-border-color').val();
            
            // Wall Properties
            let wallThickness = parseInt($('#input-wall-thickness').val()) || 3;
            let wallStyle = $('#input-wall-style').val() || 'solid';
            let wallColor = $('#input-wall-color').val() || '#333333';
            
            if (customColor === '#ffffff') customColor = ''; // Treat white as empty/default
            
            if(!id) { alert('Enter ID'); return; }
            
            if(selectedIndices.length > 0) {
                // Update Selected
                selectedIndices.forEach(idx => {
                    items[idx].type = type;
                    items[idx].id = id;
                    items[idx].display_label = displayLabel;
                    items[idx].status = status;
                    items[idx].custom_color = customColor;
                    items[idx].text_dir = textDir;
                    items[idx].text_color = textColor;
                    items[idx].font_size = fontSize;
                    items[idx].show_border = showBorder;
                    items[idx].border_color = borderColor;
                    items[idx].is_common_area = isCommonAreaType(type);
                    items[idx].is_wall = isWallType(type);
                    // Wall properties
                    items[idx].wall_thickness = wallThickness;
                    items[idx].wall_style = wallStyle;
                    items[idx].wall_color = wallColor;
                    if(type === 'stall') items[idx].price = items[idx].area * 11500;
                });
            } else if (currentSelectionRect) {
                // Create New from Marquee
                let newItem = {
                    id: id, 
                    type: type,
                    display_label: displayLabel,
                    r: currentSelectionRect.r, 
                    c: currentSelectionRect.c,
                    h: currentSelectionRect.h, 
                    w: currentSelectionRect.w,
                    area: currentSelectionRect.h * currentSelectionRect.w,
                    dim: `${currentSelectionRect.w}x${currentSelectionRect.h}`,
                    status: status,
                    custom_color: customColor,
                    text_dir: textDir,
                    text_color: textColor,
                    font_size: fontSize,
                    show_border: showBorder,
                    border_color: borderColor,
                    is_common_area: isCommonAreaType(type),
                    is_wall: isWallType(type),
                    // Wall properties
                    wall_thickness: wallThickness,
                    wall_style: wallStyle,
                    wall_color: wallColor
                };
                if(type === 'stall') newItem.price = newItem.area * 11500;
                items.push(newItem);
            }
            
            renderItems();
            modal.hide();
            selectedIndices = [];
        });
        
        $('#btn-add-stall').click(function() {
            // Find a spot that isn't 1,1 if possible, or just offset
            let r = 1, c = 1;
            // Simple collision check for 1,1
            let collision = items.some(i => i.r === 1 && i.c === 1);
            if(collision) {
                // Try to find a spot in the first few rows
                for(let tr=1; tr<10; tr+=4) {
                    for(let tc=1; tc<10; tc+=4) {
                        if(!items.some(i => i.r === tr && i.c === tc)) {
                            r = tr; c = tc;
                            break;
                        }
                    }
                    if(r !== 1 || c !== 1) break;
                }
            }

            let newItem = {
                id: 'New Stall ' + (items.length + 1), 
                type: 'stall',
                r: r, c: c, h: 3, w: 3,
                area: 9, dim: '3x3', status: 'available', price: 9 * 11500
            };
            items.push(newItem);
            renderItems();
            // Select it
            selectedIndices = [items.length - 1];
            renderSelectionState();
        });
        
        // Add Common Area Button
        $('#btn-add-common-area').click(function() {
            // Find an empty spot
            let r = 1, c = 1;
            let collision = items.some(i => i.r === 1 && i.c === 1);
            if(collision) {
                for(let tr=1; tr<15; tr+=3) {
                    for(let tc=1; tc<15; tc+=3) {
                        if(!items.some(i => i.r === tr && i.c === tc)) {
                            r = tr; c = tc;
                            break;
                        }
                    }
                    if(r !== 1 || c !== 1) break;
                }
            }

            let newItem = {
                id: 'Common Area ' + (items.length + 1), 
                display_label: 'LABEL',
                type: 'custom-area',
                r: r, c: c, h: 2, w: 4,
                area: 8, dim: '4x2', 
                status: 'service',
                custom_color: '#9B59B6',
                text_color: '#ffffff',
                text_dir: 'horizontal',
                font_size: 'auto',
                show_border: false,
                is_common_area: true
            };
            items.push(newItem);
            renderItems();
            
            // Select and open modal to customize
            selectedIndices = [items.length - 1];
            renderSelectionState();
            openModal(true);
        });
        
        // Add Wall/Border Button
        $('#btn-add-wall').click(function() {
            // Find an empty spot
            let r = 1, c = 1;
            let collision = items.some(i => i.r === 1 && i.c === 1);
            if(collision) {
                for(let tr=1; tr<15; tr+=5) {
                    for(let tc=1; tc<15; tc+=5) {
                        if(!items.some(i => i.r === tr && i.c === tc)) {
                            r = tr; c = tc;
                            break;
                        }
                    }
                    if(r !== 1 || c !== 1) break;
                }
            }

            let newItem = {
                id: 'Wall ' + (items.length + 1), 
                display_label: '',
                type: 'wall',
                r: r, c: c, h: 5, w: 8,
                area: 40, dim: '8x5', 
                status: 'service',
                custom_color: '',
                text_color: '#333333',
                text_dir: 'horizontal',
                font_size: 'auto',
                show_border: false,
                is_common_area: false,
                is_wall: true,
                wall_thickness: 3,
                wall_style: 'solid',
                wall_color: '#333333'
            };
            items.push(newItem);
            renderItems();
            
            // Select and open modal to customize
            selectedIndices = [items.length - 1];
            renderSelectionState();
            openModal(true);
        });

        $('#btn-reload-live').click(function() {
            if(confirm("Reloading will discard unsaved changes. Continue?")) {
                location.reload();
            }
        });

        $('#btn-import-default').click(function() {
            if(!confirm("This will OVERWRITE your current design with the default 'hall3_layout.json'. Continue?")) return;
            
            $.post(ajaxurl, {
                action: 'cnc_load_default_json',
                nonce: '<?php echo wp_create_nonce("cnc_designer_nonce"); ?>'
            }, function(res) {
                if(res.success) {
                    items = res.data;
                    renderItems();
                    alert('Default layout imported successfully!');
                } else {
                    alert('Error importing default: ' + res.data);
                }
            });
        });
        
        $('#btn-delete').click(function() {
            // Delete all selected
            // Sort indices descending to avoid shift issues
            selectedIndices.sort((a,b) => b-a);
            selectedIndices.forEach(idx => items.splice(idx, 1));
            selectedIndices = [];
            renderItems();
            modal.hide();
        });
        
        // Version Management
        $('#btn-save-version').click(function() {
            let name = prompt("Enter Version Name (e.g. 'Draft 1'):");
            if(name) saveLayout('version', name);
        });

        $('#btn-load-version').click(function() {
            let idx = $('#version-select').val();
            if(idx === "") return;
            if(!confirm("Load this version? Unsaved changes will be lost.")) return;
            
            let v = savedVersions[idx];
            if(v && v.layout) {
                items = v.layout;
                $('#event-title').val(v.title || '');
                $('#grid-rows').val(v.rows || 67);
                $('#grid-cols').val(v.cols || 48);
                updateGridSize();
                renderItems();
            }
        });

        $('#btn-delete-version').click(function() {
            let idx = $('#version-select').val();
            if(idx === "") return;
            if(!confirm("Delete this version?")) return;
            
            $.post(ajaxurl, {
                action: 'cnc_save_layout',
                save_type: 'delete_version',
                version_index: idx,
                nonce: '<?php echo wp_create_nonce("cnc_designer_nonce"); ?>'
            }, function(res) {
                if(res.success) {
                    alert('Version deleted');
                    location.reload();
                } else {
                    alert('Error: ' + res.data);
                }
            });
        });
        
        // JSON IO
        $('#btn-json-io').click(function() {
            $('#json-data').val(JSON.stringify(items, null, 2));
            $('#json-modal').show();
        });
        
        $('#btn-import-json').click(function() {
            try {
                let data = JSON.parse($('#json-data').val());
                if(Array.isArray(data)) {
                    items = data;
                    
                    // Auto-resize grid to fit imported items
                    let maxR = ROWS;
                    let maxC = COLS;
                    items.forEach(item => {
                        if(item.r + item.h - 1 > maxR) maxR = item.r + item.h - 1;
                        if(item.c + item.w - 1 > maxC) maxC = item.c + item.w - 1;
                    });
                    
                    if(maxR > ROWS || maxC > COLS) {
                        if(confirm(`Imported layout is larger than current grid (${maxR}x${maxC}). Resize grid?`)) {
                            $('#grid-rows').val(maxR);
                            $('#grid-cols').val(maxC);
                            updateGridSize();
                        }
                    }
                    
                    renderItems();
                    $('#json-modal').hide();
                } else {
                    alert('Invalid JSON format (must be array)');
                }
            } catch(e) {
                alert('JSON Parse Error');
            }
        });

        // Publish
        $('#btn-publish').click(function() {
            if(!confirm('Publish to Frontend?')) return;
            saveLayout('publish');
        });
        
        function saveLayout(type, versionName = '') {
            $('#save-spinner').addClass('is-active');
            $.post(ajaxurl, {
                action: 'cnc_save_layout',
                layout: JSON.stringify(items),
                event_title: $('#event-title').val(),
                grid_rows: $('#grid-rows').val(),
                grid_cols: $('#grid-cols').val(),
                save_type: type,
                version_name: versionName,
                nonce: '<?php echo wp_create_nonce("cnc_designer_nonce"); ?>'
            }, function(res) {
                $('#save-spinner').removeClass('is-active');
                if(res.success) {
                    alert(res.data.message || res.data);
                } else {
                    alert('Error: ' + res.data);
                }
            });
        }

    });
    </script>
    <?php
}
