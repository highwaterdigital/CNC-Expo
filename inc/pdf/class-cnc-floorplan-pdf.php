<?php
/**
 * CNC Floor Plan PDF Generator
 * Generates dynamic PDF/printable layouts showing current stall booking status
 * 
 * @package CNC_Child_Theme
 * @since 4.4.0
 */

if (!defined('ABSPATH')) exit;

class CNC_Floorplan_PDF {
    
    /**
     * Stall type definitions with colors and dimensions
     */
    private static $stall_types = [
        'AS' => ['name' => 'Premium Large', 'color' => '#E67E22', 'dim' => '6m x 6m', 'area' => 36],
        'BS' => ['name' => 'Large Suite', 'color' => '#9B59B6', 'dim' => '6x4 & 3x3', 'area' => 33],
        'DS' => ['name' => 'Medium Suite', 'color' => '#3498DB', 'dim' => '6x3 & 3x3', 'area' => 27],
        'ES' => ['name' => 'Standard Large', 'color' => '#1ABC9C', 'dim' => '6m x 4m', 'area' => 24],
        'FS' => ['name' => 'Standard Medium', 'color' => '#E74C3C', 'dim' => '6m x 3m', 'area' => 18],
        'GS' => ['name' => 'Compact', 'color' => '#F39C12', 'dim' => '4m x 3m', 'area' => 12],
        'SHELL' => ['name' => 'Shell Scheme', 'color' => '#2ECC71', 'dim' => '3m x 3m', 'area' => 9],
    ];
    
    /**
     * Status colors
     */
    private static $status_colors = [
        'available' => '#2ECC71',
        'pending'   => '#F39C12',
        'booked'    => '#34495E',
    ];
    
    /**
     * Generate printable HTML floor plan
     * 
     * @return string Complete HTML document for printing
     */
    public static function generate_printable_html() {
        require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
        require_once get_stylesheet_directory() . '/inc/floorplan-config.php';
        
        $stalls = cnc_get_floorplan_data();
        $grid_cols = CNC_GRID_COLS;
        $grid_rows = CNC_GRID_ROWS;
        
        // Get event settings
        $event_title = get_option('cnc_event_title', 'Cable Net Convergence 2026');
        $event_venue = get_option('cnc_event_venue', 'HITEX Exhibition Centre, Hyderabad');
        $event_date = get_option('cnc_event_date', 'August 13-15, 2026');
        
        // Calculate stats
        $stats = self::calculate_statistics($stalls);
        
        // Start building HTML
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floor Plan - <?php echo esc_html($event_title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Print-optimized styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fff;
            color: #333;
            line-height: 1.4;
        }
        
        .pdf-page {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #C6308C;
        }
        
        .pdf-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .pdf-logo img {
            height: 60px;
            width: auto;
        }
        
        .pdf-title h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #5E3A8E;
            margin-bottom: 4px;
        }
        
        .pdf-title p {
            font-size: 14px;
            color: #666;
        }
        
        .pdf-meta {
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        
        .pdf-meta .date-generated {
            font-weight: 600;
            color: #C6308C;
        }
        
        /* Statistics Bar */
        .pdf-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
        }
        
        .stat-item {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }
        
        .stat-value.available { color: #2ECC71; }
        .stat-value.pending { color: #F39C12; }
        .stat-value.booked { color: #34495E; }
        .stat-value.total { color: #5E3A8E; }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Main Content */
        .pdf-content {
            display: flex;
            gap: 20px;
        }
        
        .pdf-floorplan {
            flex: 1;
            min-width: 0;
        }
        
        .pdf-sidebar {
            width: 280px;
            flex-shrink: 0;
        }
        
        /* Floor Plan Grid */
        .floorplan-container {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .floorplan-grid {
            display: grid;
            grid-template-columns: repeat(<?php echo intval($grid_cols); ?>, 1fr);
            grid-template-rows: repeat(<?php echo intval($grid_rows); ?>, 1fr);
            gap: 2px;
            background-color: #e0e0e0;
            padding: 2px;
            aspect-ratio: <?php echo intval($grid_cols); ?>/<?php echo intval($grid_rows); ?>;
            min-height: 400px;
        }
        
        .stall-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 600;
            background: #fff;
            text-align: center;
            line-height: 1.1;
            padding: 2px;
            position: relative;
            overflow: hidden;
        }
        
        .stall-cell.available { background: #2ECC71; color: #fff; }
        .stall-cell.pending { background: #F39C12; color: #fff; }
        .stall-cell.booked { background: #34495E; color: #fff; }
        
        .stall-cell .stall-id { font-size: 10px; font-weight: 700; }
        .stall-cell .stall-company { font-size: 7px; opacity: 0.9; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        
        /* Large stalls get bigger text */
        .stall-cell[data-area="36"] .stall-id,
        .stall-cell[data-area="33"] .stall-id,
        .stall-cell[data-area="27"] .stall-id { font-size: 14px; }
        .stall-cell[data-area="24"] .stall-id { font-size: 12px; }
        
        /* Common Areas */
        .stall-cell.common-area { pointer-events: none; }
        .stall-cell.type-fire-exit { background: #E74C3C; color: #fff; }
        .stall-cell.type-entry { background: #F39C12; color: #fff; }
        .stall-cell.type-washroom { background: #3498DB; color: #fff; }
        .stall-cell.type-service { background: #9B59B6; color: #fff; }
        .stall-cell.type-stairs { background: #34495E; color: #fff; }
        .stall-cell.type-stage { background: #1ABC9C; color: #fff; }
        .stall-cell.type-corridor { background: #27AE60; color: #fff; opacity: 0.7; }
        .stall-cell.type-pillar { background: #666; color: #fff; }
        .stall-cell.type-blank { background: #f0f0f0; color: #999; }
        
        /* Legend & Sidebar */
        .legend-box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .legend-box h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #C6308C;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            flex-shrink: 0;
        }
        
        .legend-text {
            flex: 1;
        }
        
        .legend-count {
            font-weight: 600;
            color: #666;
        }
        
        /* Stall Types Legend */
        .type-legend-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        
        .type-legend-item:last-child { border-bottom: none; }
        
        .type-code {
            font-weight: 700;
            color: #5E3A8E;
            width: 30px;
        }
        
        .type-name { flex: 1; }
        .type-dim { color: #666; font-size: 10px; }
        .type-area { font-weight: 600; color: #333; }
        
        /* Pricing Info */
        .pricing-box {
            background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
            color: #fff;
            border-radius: 8px;
            padding: 15px;
        }
        
        .pricing-box h3 {
            font-size: 14px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 8px;
        }
        
        .pricing-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 12px;
        }
        
        .pricing-label { opacity: 0.9; }
        .pricing-value { font-weight: 600; }
        
        /* Booked List */
        .booked-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .booked-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        
        .booked-stall { font-weight: 600; color: #5E3A8E; }
        .booked-company { color: #666; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        
        /* Footer */
        .pdf-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: #666;
        }
        
        .pdf-footer-left {
            display: flex;
            gap: 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .pdf-footer-right {
            text-align: right;
        }
        
        /* Print Styles */
        @media print {
            body { font-size: 10pt; }
            .pdf-page { padding: 0; max-width: 100%; }
            .pdf-stats { break-inside: avoid; }
            .pdf-content { break-inside: avoid; }
            .no-print { display: none !important; }
            
            @page {
                size: A3 landscape;
                margin: 10mm;
            }
        }
        
        /* Print Button (hidden in print) */
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn-print {
            background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(198, 48, 140, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(198, 48, 140, 0.4);
        }
        
        .btn-close-pdf {
            background: #34495E;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        
        @media print {
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-actions no-print">
        <button class="btn-print" onclick="window.print()">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v1a2 2 0 002 2h6a2 2 0 002-2v-1h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm2 0h6v3H7V4zm6 8H7v3h6v-3z"/></svg>
            Print / Save PDF
        </button>
        <button class="btn-close-pdf" onclick="window.close()">Close</button>
    </div>
    
    <div class="pdf-page">
        <!-- Header -->
        <header class="pdf-header">
            <div class="pdf-logo">
                <div class="pdf-title">
                    <h1><?php echo esc_html($event_title); ?></h1>
                    <p><?php echo esc_html($event_venue); ?> | <?php echo esc_html($event_date); ?></p>
                </div>
            </div>
            <div class="pdf-meta">
                <div>Hall 3 Floor Plan</div>
                <div class="date-generated">Generated: <?php echo current_time('F j, Y g:i A'); ?></div>
                <div>Total Area: <?php echo number_format($stats['total_area']); ?> m²</div>
            </div>
        </header>
        
        <!-- Statistics -->
        <div class="pdf-stats">
            <div class="stat-item">
                <div class="stat-value available"><?php echo $stats['available']; ?></div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-item">
                <div class="stat-value pending"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-item">
                <div class="stat-value booked"><?php echo $stats['booked']; ?></div>
                <div class="stat-label">Booked</div>
            </div>
            <div class="stat-item">
                <div class="stat-value total"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Stalls</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #2ECC71;"><?php echo number_format($stats['available_area']); ?> m²</div>
                <div class="stat-label">Available Area</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #34495E;"><?php echo number_format($stats['booked_area']); ?> m²</div>
                <div class="stat-label">Booked Area</div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="pdf-content">
            <!-- Floor Plan -->
            <div class="pdf-floorplan">
                <div class="floorplan-container">
                    <div class="floorplan-grid">
                        <?php echo self::render_grid_cells($stalls); ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="pdf-sidebar">
                <!-- Status Legend -->
                <div class="legend-box">
                    <h3>Status Legend</h3>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #2ECC71;"></div>
                        <span class="legend-text">Available</span>
                        <span class="legend-count"><?php echo $stats['available']; ?></span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #F39C12;"></div>
                        <span class="legend-text">Pending Approval</span>
                        <span class="legend-count"><?php echo $stats['pending']; ?></span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #34495E;"></div>
                        <span class="legend-text">Booked</span>
                        <span class="legend-count"><?php echo $stats['booked']; ?></span>
                    </div>
                </div>
                
                <!-- Stall Types -->
                <div class="legend-box">
                    <h3>Stall Configuration</h3>
                    <?php foreach (self::$stall_types as $code => $type): ?>
                    <div class="type-legend-item">
                        <span class="type-code"><?php echo $code; ?></span>
                        <span class="type-name"><?php echo $type['name']; ?></span>
                        <span class="type-area"><?php echo $type['area']; ?> m²</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pricing -->
                <div class="pricing-box">
                    <h3>Pricing Rates</h3>
                    <div class="pricing-row">
                        <span class="pricing-label">Shell Scheme (Min 9 m²)</span>
                        <span class="pricing-value">₹11,500/m²</span>
                    </div>
                    <div class="pricing-row">
                        <span class="pricing-label">Raw Space (Min 18 m²)</span>
                        <span class="pricing-value">₹10,500/m²</span>
                    </div>
                    <div class="pricing-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.2);">
                        <span class="pricing-label">USD Rate</span>
                        <span class="pricing-value">$260 - $290/m²</span>
                    </div>
                </div>
                
                <?php if (!empty($stats['booked_list'])): ?>
                <!-- Booked Stalls -->
                <div class="legend-box" style="margin-top: 15px;">
                    <h3>Booked Exhibitors</h3>
                    <div class="booked-list">
                        <?php foreach ($stats['booked_list'] as $item): ?>
                        <div class="booked-item">
                            <span class="booked-stall"><?php echo esc_html($item['id']); ?></span>
                            <span class="booked-company"><?php echo esc_html($item['company']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="pdf-footer">
            <div class="pdf-footer-left">
                <div class="contact-item">
                    <strong>Email:</strong> info@cablenetconvergence.com
                </div>
                <div class="contact-item">
                    <strong>Phone:</strong> +91 40 2354 5678
                </div>
                <div class="contact-item">
                    <strong>Web:</strong> www.cablenetconvergence.com
                </div>
            </div>
            <div class="pdf-footer-right">
                <div>© <?php echo date('Y'); ?> Cable Net Convergence. All rights reserved.</div>
                <div style="font-size: 10px; color: #999;">This layout is subject to change. Prices exclude GST.</div>
            </div>
        </footer>
    </div>
    
    <script>
        // Auto-trigger print dialog if requested
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render grid cells for the floor plan
     */
    private static function render_grid_cells($stalls) {
        $html = '';
        
        $common_area_types = [
            'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
            'stairs', 'stage', 'service', 'registration', 'lounge', 
            'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
            'food-court', 'networking', 'feature'
        ];
        
        foreach ($stalls as $s) {
            $type = isset($s['type']) ? $s['type'] : 'stall';
            $is_common_area = isset($s['is_common_area']) ? $s['is_common_area'] : in_array($type, $common_area_types);
            $is_wall = isset($s['is_wall']) ? $s['is_wall'] : false;
            
            // Skip walls in PDF (they're decorative)
            if ($is_wall) continue;
            
            $classes = 'stall-cell';
            $classes .= ' ' . $s['status'];
            $classes .= ' type-' . $type;
            if ($is_common_area) {
                $classes .= ' common-area';
            }
            
            $style = "grid-row: {$s['r']} / span {$s['h']}; grid-column: {$s['c']} / span {$s['w']};";
            
            // Custom color
            if (!empty($s['custom_color'])) {
                $style .= " background-color: " . esc_attr($s['custom_color']) . ";";
            }
            
            // Display label
            $display_label = !empty($s['display_label']) ? $s['display_label'] : str_replace('3.', '', $s['id']);
            $company = !empty($s['company']) ? $s['company'] : '';
            
            $html .= '<div class="' . esc_attr($classes) . '" style="' . esc_attr($style) . '" data-area="' . esc_attr($s['area']) . '">';
            
            if ($type === 'stall' && !$is_common_area) {
                $html .= '<span class="stall-id">' . esc_html($display_label) . '</span>';
                if ($company) {
                    $html .= '<span class="stall-company">' . esc_html($company) . '</span>';
                }
            } else {
                $html .= '<span class="stall-id">' . esc_html($display_label) . '</span>';
            }
            
            $html .= '</div>';
        }
        
        return $html;
    }
    
    /**
     * Calculate statistics from stall data
     */
    private static function calculate_statistics($stalls) {
        $stats = [
            'available' => 0,
            'pending' => 0,
            'booked' => 0,
            'total' => 0,
            'available_area' => 0,
            'booked_area' => 0,
            'pending_area' => 0,
            'total_area' => 0,
            'booked_list' => [],
        ];
        
        $common_area_types = [
            'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
            'stairs', 'stage', 'service', 'registration', 'lounge', 
            'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
            'food-court', 'networking', 'feature', 'wall', 'boundary', 'partition'
        ];
        
        foreach ($stalls as $s) {
            $type = isset($s['type']) ? $s['type'] : 'stall';
            
            // Skip non-stall items
            if (in_array($type, $common_area_types) || $type !== 'stall') {
                continue;
            }
            
            $area = isset($s['area']) ? intval($s['area']) : ($s['w'] * $s['h']);
            
            $stats['total']++;
            $stats['total_area'] += $area;
            
            switch ($s['status']) {
                case 'available':
                    $stats['available']++;
                    $stats['available_area'] += $area;
                    break;
                case 'pending':
                    $stats['pending']++;
                    $stats['pending_area'] += $area;
                    break;
                case 'booked':
                    $stats['booked']++;
                    $stats['booked_area'] += $area;
                    $stats['booked_list'][] = [
                        'id' => $s['id'],
                        'company' => $s['company'],
                    ];
                    break;
            }
        }
        
        // Sort booked list by stall ID
        usort($stats['booked_list'], function($a, $b) {
            return strcmp($a['id'], $b['id']);
        });
        
        return $stats;
    }
    
    /**
     * Generate JSON data for API
     */
    public static function get_floorplan_json() {
        require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
        
        $stalls = cnc_get_floorplan_data();
        $stats = self::calculate_statistics($stalls);
        
        return [
            'success' => true,
            'generated_at' => current_time('c'),
            'event' => [
                'title' => get_option('cnc_event_title', 'Cable Net Convergence 2026'),
                'venue' => get_option('cnc_event_venue', 'HITEX Exhibition Centre, Hyderabad'),
                'date' => get_option('cnc_event_date', 'August 13-15, 2026'),
            ],
            'statistics' => $stats,
            'stalls' => array_map(function($s) {
                return [
                    'id' => $s['id'],
                    'status' => $s['status'],
                    'company' => $s['company'],
                    'type' => $s['type'],
                    'area' => $s['area'],
                    'position' => ['row' => $s['r'], 'col' => $s['c'], 'height' => $s['h'], 'width' => $s['w']],
                ];
            }, $stalls),
        ];
    }
}
