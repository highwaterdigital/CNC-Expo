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
                    <option value="stall">Stall (Standard)</option>
                    <option value="fire-exit">Fire Exit</option>
                    <option value="washroom">Washroom</option>
                    <option value="service">Service / Info</option>
                    <option value="entry">Entry / Exit</option>
                </select>
            </div>
            <div class="form-row">
                <label>ID / Label (Same ID = Merged Stall)</label>
                <input type="text" id="input-id" placeholder="e.g. 3.A 1">
            </div>
            <div class="form-row">
                <label>Dimensions</label>
                <span id="disp-dims">--</span>
            </div>
            <div class="form-row">
                <label>Status / Color Theme</label>
                <select id="input-color-theme">
                    <option value="available">Available (Green)</option>
                    <option value="booked">Booked (Grey)</option>
                    <option value="reserved">Reserved (Orange)</option>
                    <option value="exit">Fire Exit (Red)</option>
                    <option value="washroom">Washroom (Blue)</option>
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
                <div style="display:flex; gap:10px;">
                    <input type="color" id="input-custom-color" value="#ffffff" style="width:50px; height:30px; padding:0;">
                    <button class="button" id="btn-clear-color" style="font-size:11px;">Clear</button>
                </div>
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
        
        /* Type Overrides */
        .item-fire-exit, .status-exit { background: var(--bms-red); border-color: #C0392B; font-weight: bold; }
        .item-washroom, .status-washroom { background: var(--bms-blue); border-color: #2980B9; }
        .item-service { background: #95A5A6; border-color: #7F8C8D; font-size: 9px; }
        .item-entry { background: var(--bms-orange); border-color: #E67E22; font-weight: 800; letter-spacing: 1px; }
        .item-feature { background: #FFC107; border-color: #F39C12; color: #333; }

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
            width: 300px;
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
            
            let label = (item.id || '').replace('3.', ''); 
            
            let el = $(`<div class="design-item item-${item.type}" data-index="${index}" title="${item.id}">
                ${label}
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
            
            // Apply Status Class if exists
            if(item.status) {
                el.addClass('status-' + item.status);
            } else {
                // Default status based on type if not set
                if(item.type === 'stall') el.addClass('status-available');
            }

            // Apply Custom Color (Only if Available or Feature)
            if (item.custom_color && (item.status === 'available' || !item.status || item.type === 'feature')) {
                el.css('background-color', item.custom_color);
                el.css('border-color', item.custom_color); // Or darken it?
                // Add visual indicator for availability if it's a stall
                if (item.type === 'stall') {
                    el.append('<div style="position:absolute; bottom:0; left:0; width:100%; height:3px; background:#27AE60;"></div>');
                }
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
                $('#input-color-theme').val(item.status || 'available');
                
                let cColor = item.custom_color || '#ffffff';
                $('#input-custom-color').val(cColor);
                
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
            } else {
                // Add New
                $('#input-type').val('stall');
                $('#input-id').val('');
                $('#input-color-theme').val('available');
                $('#input-custom-color').val('#ffffff'); // Default
                $('#input-category-color').val("");
                $('#disp-dims').text(`${currentSelectionRect.w}m x ${currentSelectionRect.h}m`);
                $('#btn-add').text('Add');
                $('#btn-delete').hide();
            }
            modal.show();
            $('#input-id').focus();
        }

        // Sync Category -> Custom Color
        $('#input-category-color').change(function() {
            let val = $(this).val();
            if(val) {
                $('#input-custom-color').val(val);
            }
        });

        // Sync Custom Color -> Category (if match found)
        $('#input-custom-color').change(function() {
             let val = $(this).val();
             let found = false;
             $('#input-category-color option').each(function() {
                if($(this).val().toLowerCase() === val.toLowerCase()) {
                    $('#input-category-color').val($(this).val());
                    found = true;
                }
             });
             if(!found) $('#input-category-color').val("");
        });

        $('#btn-cancel').click(function() { modal.hide(); });
        
        $('#btn-clear-color').click(function(e) {
            e.preventDefault();
            $('#input-custom-color').val('#ffffff');
        });
        
        $('#btn-add').click(function() {
            let type = $('#input-type').val();
            let id = $('#input-id').val();
            let status = $('#input-color-theme').val();
            let customColor = $('#input-custom-color').val();
            if (customColor === '#ffffff') customColor = ''; // Treat white as empty/default
            
            if(!id) { alert('Enter ID'); return; }
            
            if(selectedIndices.length > 0) {
                // Update Selected
                selectedIndices.forEach(idx => {
                    items[idx].type = type;
                    items[idx].id = id;
                    items[idx].status = status;
                    items[idx].custom_color = customColor;
                    if(type === 'stall') items[idx].price = items[idx].area * 11500;
                });
            } else if (currentSelectionRect) {
                // Create New from Marquee
                let newItem = {
                    id: id, type: type,
                    r: currentSelectionRect.r, c: currentSelectionRect.c,
                    h: currentSelectionRect.h, w: currentSelectionRect.w,
                    area: currentSelectionRect.h * currentSelectionRect.w,
                    dim: `${currentSelectionRect.w}x${currentSelectionRect.h}`,
                    status: status,
                    custom_color: customColor
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
