<?php
/**
 * Admin Floor Plan Manager
 * Used for managing BOOKINGS (Status), not Layout.
 */

require_once get_stylesheet_directory() . '/inc/floorplan-config.php';

function cnc_register_floorplan_page() {
    add_submenu_page(
        'edit.php?post_type=cnc_booking',
        'Floor Plan Manager',
        'Floor Plan & Layout',
        'manage_options',
        'cnc-floorplan',
        'cnc_render_floorplan_page'
    );
}
add_action('admin_menu', 'cnc_register_floorplan_page');

function cnc_render_floorplan_page() {
    // Check if we are in Designer Mode
    if (isset($_GET['view']) && $_GET['view'] === 'designer') {
        require_once get_stylesheet_directory() . '/inc/admin-floorplan-designer.php';
        cnc_render_designer_page();
        return;
    }

    // Handle Status Updates & Manual Bookings
    if (isset($_POST['cnc_update_stall']) && check_admin_referer('cnc_update_stall_nonce')) {
        $post_id = intval($_POST['post_id']);
        $stall_id = sanitize_text_field($_POST['stall_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        if ($post_id > 0) {
            // Update Existing
            if ($new_status === 'trash') {
                wp_trash_post($post_id);
                echo '<div class="notice notice-success"><p>Booking deleted (moved to trash).</p></div>';
            } else {
                update_post_meta($post_id, 'cnc_booking_status', $new_status);
                echo '<div class="notice notice-success"><p>Stall status updated.</p></div>';

                // 🔔 Send WhatsApp Notification
                if (class_exists('cnc_WhatsApp_API')) {
                    $phone = get_post_meta($post_id, 'cnc_phone', true);
                    $contact = get_post_meta($post_id, 'cnc_contact_person', true) ?: get_the_title($post_id);
                    $stall_val = get_post_meta($post_id, 'cnc_stall_id', true);
                    
                    // Template: cnc_notify_stall_updated
                    // Params: {{1}}=Name, {{2}}=Stall, {{3}}=Link/Status
                    $template_name = 'cnc_notify_stall_updated';
                    $components = [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $contact],
                                ['type' => 'text', 'text' => $stall_val],
                                ['type' => 'text', 'text' => site_url('/my-account/')] // Link to dashboard
                            ]
                        ]
                    ];
                    
                    cnc_WhatsApp_API::send_template($phone, $template_name, 'en', $components, ['post_id' => $post_id]);
                }
            }
        } else {
            // Create New Booking
            if ($new_status !== 'trash' && !empty($_POST['company_name'])) {
                $company = sanitize_text_field($_POST['company_name']);
                $phone = sanitize_text_field($_POST['phone']);
                $email = sanitize_email($_POST['email']);
                
                $new_post_id = wp_insert_post([
                    'post_title' => $company,
                    'post_type' => 'cnc_booking',
                    'post_status' => 'publish'
                ]);
                
                if ($new_post_id) {
                    update_post_meta($new_post_id, 'cnc_stall_id', $stall_id);
                    update_post_meta($new_post_id, 'cnc_contact_person', $company);
                    update_post_meta($new_post_id, 'cnc_phone', $phone);
                    update_post_meta($new_post_id, 'cnc_email', $email);
                    update_post_meta($new_post_id, 'cnc_booking_status', $new_status);
                    echo '<div class="notice notice-success"><p>New booking created successfully.</p></div>';
                }
            }
        }
    }

    require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
    $stalls = cnc_get_floorplan_data();
    
    // Use Config Constants
    $grid_rows = CNC_GRID_ROWS;
    $grid_cols = CNC_GRID_COLS;
    $grid_gap = CNC_GRID_GAP_PX;
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Hall 3 Floor Plan Manager</h1>
        <a href="<?php echo admin_url('edit.php?post_type=cnc_booking&page=cnc-floorplan&view=designer'); ?>" class="page-title-action">📐 Edit Layout (Designer)</a>
        <hr class="wp-header-end">
        
        <p>Click on a stall to manage its booking status or manually allot it. <br>
        <em>To move, resize, or add stalls, use the <a href="<?php echo admin_url('edit.php?post_type=cnc_booking&page=cnc-floorplan&view=designer'); ?>">Layout Designer</a>.</em></p>
        
        <style>
            .cnc-admin-layout {
                display: grid;
                grid-template-columns: minmax(0, 3fr) minmax(260px, 1fr);
                gap: 24px;
                align-items: start;
                margin-top: 20px;
            }
            .cnc-admin-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo $grid_cols; ?>, 1fr);
                grid-template-rows: repeat(<?php echo $grid_rows; ?>, 1fr);
                gap: <?php echo $grid_gap; ?>px;
                background: #fff;
                border: 1px solid #ccc;
                width: 100%;
                aspect-ratio: <?php echo $grid_cols; ?>/<?php echo $grid_rows; ?>;
                padding: <?php echo $grid_gap; ?>px;
                background-color: <?php echo CNC_COLOR_GAP; ?>;
                box-sizing: border-box;
            }
            .stall {
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 8px;
                color: white;
                cursor: pointer;
                border: 1px solid rgba(0,0,0,0.1);
                overflow: hidden;
                text-align: center;
            }
            .stall:hover { opacity: 0.88; }
            .status-available { background: <?php echo CNC_COLOR_AVAILABLE; ?>; }
            .status-pending { background: <?php echo CNC_COLOR_PENDING; ?>; }
            .status-booked { background: <?php echo CNC_COLOR_BOOKED; ?>; color: #333; }
            .status-feature { background: #eee; color: #333; border: none; cursor: default; }
            .status-exit { background: <?php echo CNC_COLOR_EXIT; ?>; color: white; }
            .status-washroom { background: <?php echo CNC_COLOR_WASHROOM; ?>; color: white; }

            .cnc-admin-aside {
                display: flex;
                flex-direction: column;
                gap: 14px;
                position: sticky;
                top: 90px;
            }
            .cnc-admin-card {
                background: #fff;
                border: 1px solid #dfe3e8;
                border-radius: 8px;
                padding: 14px 16px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            }
            .cnc-legend-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 8px;
                font-size: 13px;
            }
            .cnc-legend-swatch {
                width: 16px;
                height: 16px;
                border-radius: 4px;
                border: 1px solid rgba(0,0,0,0.08);
            }
            .cnc-direction {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
                text-align: center;
                font-weight: 700;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }
            .cnc-direction span {
                padding: 8px;
                background: #f6f7fb;
                border: 1px solid #e7e9ef;
                border-radius: 6px;
            }
            .cnc-price-info {
                font-size: 12px;
                color: #555;
                line-height: 1.5;
            }

            /* Modal */
            .cnc-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; }
            .cnc-modal-content { background: #fff; width: 400px; margin: 50px auto; padding: 25px; border-radius: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
            .cnc-modal input[type="text"], .cnc-modal input[type="email"], .cnc-modal select { width: 100%; margin-bottom: 10px; padding: 8px; }

            .stall.cat-as { background: #E91E63; border-color: #C2185B; }
            .stall.cat-bs { background: #4CAF50; border-color: #388E3C; }
            .stall.cat-ds { background: #795548; border-color: #5D4037; }
            .stall.cat-es { background: #2196F3; border-color: #1976D2; }
            .stall.cat-fs { background: #FFC107; border-color: #FFB300; color: #2d2d2d; }
            .stall.cat-gs { background: #9C27B0; border-color: #7B1FA2; }
            .stall.cat-std { background: #607D8B; border-color: #455A64; }

            .stall.status-booked { background: #95A5A6 !important; border-color: #7F8C8D !important; color: #fff !important; }
            .stall.status-pending { background: #F39C12 !important; border-color: #D35400 !important; color: #fff !important; }

            @media (max-width: 1100px) {
                .cnc-admin-layout {
                    grid-template-columns: 1fr;
                }
                .cnc-admin-aside { position: relative; top: 0; }
            }
        </style>

        <div class="cnc-admin-layout">
            <div class="cnc-admin-grid">
                <?php foreach($stalls as $s): 
                    $classes = 'stall status-' . $s['status'];
                    $catClass = '';
                    if ($s['status'] === 'available') {
                        $stallId = strtoupper($s['id'] ?? '');
                        if (strpos($stallId, 'AS') !== false) {
                            $catClass = 'cat-as';
                        } elseif (strpos($stallId, 'BS') !== false) {
                            $catClass = 'cat-bs';
                        } elseif (strpos($stallId, 'DS') !== false) {
                            $catClass = 'cat-ds';
                        } elseif (strpos($stallId, 'ES') !== false) {
                            $catClass = 'cat-es';
                        } elseif (strpos($stallId, 'FS') !== false) {
                            $catClass = 'cat-fs';
                        } elseif (strpos($stallId, 'GS') !== false) {
                            $catClass = 'cat-gs';
                        } elseif (preg_match('/\\d+\\.([A-F])/', $stallId)) {
                            $catClass = 'cat-std';
                        } elseif (preg_match('/^([A-F])\\d/', $stallId)) {
                            $catClass = 'cat-std';
                        }
                    }
                    if ($catClass) {
                        $classes .= ' ' . $catClass;
                    }
                    if(isset($s['type']) && $s['type'] !== 'stall') $classes .= ' status-feature';
                    $style = "grid-row: {$s['r']} / span {$s['h']}; grid-column: {$s['c']} / span {$s['w']};";
                    
                    $data_attr = "";
                    if ($s['type'] === 'stall') {
                        $data_attr = "data-id='{$s['id']}' data-postid='{$s['post_id']}' data-status='{$s['status']}' data-company='{$s['company']}'";
                    }
                ?>
                    <div class="<?php echo $classes; ?>" style="<?php echo $style; ?>" <?php echo $data_attr; ?> onclick="openAdminModal(this)">
                        <?php echo str_replace('3.', '', $s['id']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="cnc-admin-aside">
                <div class="cnc-admin-card">
                    <strong style="display:block; margin-bottom:10px;">Orientation</strong>
                    <div class="cnc-direction">
                        <span>North</span>
                        <span>East</span>
                        <span>South</span>
                        <span>West</span>
                        <span>Floor</span>
                        <span>Entry/Exit</span>
                    </div>
                    <div class="cnc-price-info" style="margin-top:10px;">
                        Use the layout designer to verify stall dimensions and pricing bands before allotment.
                    </div>
                </div>

                <div class="cnc-admin-card">
                    <strong style="display:block; margin-bottom:10px;">Legend</strong>
                    <div class="cnc-legend-row"><span class="cnc-legend-swatch" style="background: <?php echo CNC_COLOR_AVAILABLE; ?>;"></span> Available</div>
                    <div class="cnc-legend-row"><span class="cnc-legend-swatch" style="background: <?php echo CNC_COLOR_PENDING; ?>;"></span> Pending approval</div>
                    <div class="cnc-legend-row"><span class="cnc-legend-swatch" style="background: <?php echo CNC_COLOR_BOOKED; ?>;"></span> Booked</div>
                    <div class="cnc-legend-row"><span class="cnc-legend-swatch" style="background: <?php echo CNC_COLOR_EXIT; ?>;"></span> Entry / Fire exit</div>
                    <div class="cnc-legend-row"><span class="cnc-legend-swatch" style="background: <?php echo CNC_COLOR_WASHROOM; ?>;"></span> Washrooms / Service</div>
                    <div class="cnc-price-info" style="margin-top:10px;">
                        Click a stall to update status or manually allot. Use “Edit Layout (Designer)” for resizing or moving stalls.
                    </div>
                </div>
            </aside>
        </div>

        <!-- Edit Modal -->
        <div id="adminModal" class="cnc-modal">
            <div class="cnc-modal-content">
                <h2 id="modalTitle">Manage Stall</h2>
                <p id="modalInfo"></p>
                
                <form method="POST">
                    <?php wp_nonce_field('cnc_update_stall_nonce'); ?>
                    <input type="hidden" name="cnc_update_stall" value="1">
                    <input type="hidden" name="post_id" id="modalPostId">
                    <input type="hidden" name="stall_id" id="modalStallId">
                    
                    <div id="manualBookingFields" style="display:none; background:#f9f9f9; padding:10px; margin-bottom:15px; border:1px solid #eee;">
                        <strong>Manual Booking Details:</strong><br>
                        <input type="text" name="company_name" placeholder="Company Name" id="inputCompany">
                        <input type="text" name="phone" placeholder="Phone Number">
                        <input type="email" name="email" placeholder="Email Address">
                    </div>

                    <p>
                        <label>Status:</label>
                        <select name="new_status" id="modalStatus">
                            <option value="approved">Approved (Booked)</option>
                            <option value="pending">Pending</option>
                            <option value="trash">Available (Delete Booking)</option>
                        </select>
                    </p>
                    
                    <p class="submit">
                        <input type="submit" class="button button-primary" value="Update Status">
                        <button type="button" class="button" onclick="document.getElementById('adminModal').style.display='none'">Cancel</button>
                    </p>
                </form>
            </div>
        </div>

        <script>
        function openAdminModal(el) {
            if (el.classList.contains('status-feature')) return;
            
            const id = el.dataset.id;
            const postId = el.dataset.postid;
            const status = el.dataset.status;
            const company = el.dataset.company;
            
            document.getElementById('modalTitle').innerText = 'Manage Stall: ' + id;
            document.getElementById('modalPostId').value = postId;
            document.getElementById('modalStallId').value = id;
            
            let info = 'Current Status: <strong>' + status + '</strong><br>';
            if (company) info += 'Company: ' + company;
            
            document.getElementById('modalInfo').innerHTML = info;
            
            const fields = document.getElementById('manualBookingFields');
            const inputCompany = document.getElementById('inputCompany');
            
            if (postId == 0) {
                // Available - Allow Manual Booking
                fields.style.display = 'block';
                inputCompany.required = true;
                document.getElementById('modalStatus').value = 'approved';
                document.querySelector('input[type="submit"]').value = 'Create Booking';
            } else {
                // Existing - Edit Status
                fields.style.display = 'none';
                inputCompany.required = false;
                document.getElementById('modalStatus').value = (status === 'booked') ? 'approved' : status;
                document.querySelector('input[type="submit"]').value = 'Update Status';
            }
            
            document.getElementById('adminModal').style.display = 'block';
        }

        // Hook into approval to trigger downstream notifications (handled server-side)
        document.querySelector('#adminModal form').addEventListener('submit', function() {
            const status = document.getElementById('modalStatus').value;
            const stallId = document.getElementById('modalStallId').value;
            if (status === 'approved' && stallId) {
                // server hook below will handle notifying integrations
            }
        });
        </script>
    </div>
    <?php
}

// Hook after booking status update to notify on approval
add_action('updated_post_meta', function($meta_id, $post_id, $meta_key, $_meta_value) {
    if ($meta_key !== 'cnc_booking_status') {
        return;
    }
    $new_status = get_post_meta($post_id, 'cnc_booking_status', true);
    if ($new_status !== 'approved') {
        return;
    }

    $stall_ids = get_post_meta($post_id, 'cnc_stall_id');
    $stall_ids = is_array($stall_ids) ? $stall_ids : [$stall_ids];
    $company   = get_post_meta($post_id, 'cnc_company_name', true);
    $phone     = get_post_meta($post_id, 'cnc_phone', true);

    do_action('cnc_notify_stall_updated', [
        'post_id' => $post_id,
        'stalls'  => $stall_ids,
        'company' => $company,
        'phone'   => $phone,
        'status'  => $new_status,
    ]);
}, 10, 4);
