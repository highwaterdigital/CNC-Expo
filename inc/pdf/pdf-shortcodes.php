<?php
/**
 * CNC Floor Plan PDF Shortcodes
 * Provides shortcodes for download button and stats widget
 * 
 * @package CNC_Child_Theme
 * @since 4.4.0
 */

if (!defined('ABSPATH')) exit;

/**
 * PDF Download Button Shortcode
 * Usage: [cnc_pdf_download_button text="Download Floor Plan" class="custom-class"]
 */
function cnc_pdf_download_button_shortcode($atts) {
    $atts = shortcode_atts([
        'text'  => 'Download Floor Plan',
        'class' => '',
        'icon'  => 'true',
        'style' => 'primary', // primary, secondary, outline
    ], $atts, 'cnc_pdf_download_button');
    
    $pdf_url = home_url('/floorplan-pdf/');
    $custom_class = !empty($atts['class']) ? ' ' . sanitize_html_class($atts['class']) : '';
    $show_icon = $atts['icon'] === 'true';
    
    $style_classes = [
        'primary'   => 'cnc-pdf-btn--primary',
        'secondary' => 'cnc-pdf-btn--secondary',
        'outline'   => 'cnc-pdf-btn--outline',
    ];
    $style_class = isset($style_classes[$atts['style']]) ? $style_classes[$atts['style']] : $style_classes['primary'];
    
    ob_start();
    ?>
    <style>
        .cnc-pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            line-height: 1;
        }
        
        .cnc-pdf-btn--primary {
            background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
            color: #fff;
            border-color: transparent;
        }
        
        .cnc-pdf-btn--primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(198, 48, 140, 0.4);
            color: #fff;
        }
        
        .cnc-pdf-btn--secondary {
            background: #34495E;
            color: #fff;
            border-color: #34495E;
        }
        
        .cnc-pdf-btn--secondary:hover {
            background: #2C3E50;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 73, 94, 0.3);
            color: #fff;
        }
        
        .cnc-pdf-btn--outline {
            background: transparent;
            color: #C6308C;
            border-color: #C6308C;
        }
        
        .cnc-pdf-btn--outline:hover {
            background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
            color: #fff;
            transform: translateY(-2px);
        }
        
        .cnc-pdf-btn svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }
        
        .cnc-pdf-btn .cnc-pdf-btn__pulse {
            position: relative;
        }
        
        .cnc-pdf-btn .cnc-pdf-btn__pulse::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 8px;
            height: 8px;
            background: #2ECC71;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: cnc-pulse 2s infinite;
        }
        
        @keyframes cnc-pulse {
            0%, 100% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0.5; transform: translate(-50%, -50%) scale(1.5); }
        }
    </style>
    
    <a href="<?php echo esc_url($pdf_url); ?>" 
       target="_blank" 
       rel="noopener" 
       class="cnc-pdf-btn <?php echo esc_attr($style_class . $custom_class); ?>">
        <?php if ($show_icon): ?>
        <svg fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
        <?php endif; ?>
        <span><?php echo esc_html($atts['text']); ?></span>
        <span class="cnc-pdf-btn__pulse" title="Live Data"></span>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('cnc_pdf_download_button', 'cnc_pdf_download_button_shortcode');

/**
 * Stall Statistics Widget Shortcode
 * Usage: [cnc_stall_stats style="cards|inline|minimal"]
 */
function cnc_stall_stats_shortcode($atts) {
    $atts = shortcode_atts([
        'style'     => 'cards', // cards, inline, minimal
        'show_link' => 'true',
    ], $atts, 'cnc_stall_stats');
    
    // Get live stats
    require_once get_stylesheet_directory() . '/inc/floorplan-data.php';
    $stalls = cnc_get_floorplan_data();
    
    $stats = ['available' => 0, 'pending' => 0, 'booked' => 0, 'total' => 0];
    $common_types = ['fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 'stairs', 'stage', 'service', 'registration', 'lounge', 'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area', 'food-court', 'networking', 'feature', 'wall', 'boundary', 'partition'];
    
    foreach ($stalls as $s) {
        $type = isset($s['type']) ? $s['type'] : 'stall';
        if (in_array($type, $common_types) || $type !== 'stall') continue;
        $stats['total']++;
        if (isset($stats[$s['status']])) $stats[$s['status']]++;
    }
    
    ob_start();
    
    if ($atts['style'] === 'cards'):
    ?>
    <style>
        .cnc-stats-widget {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            font-family: 'Inter', sans-serif;
        }
        
        .cnc-stats-widget__item {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #eee;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .cnc-stats-widget__item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .cnc-stats-widget__value {
            font-size: 32px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .cnc-stats-widget__value--available { color: #2ECC71; }
        .cnc-stats-widget__value--pending { color: #F39C12; }
        .cnc-stats-widget__value--booked { color: #34495E; }
        .cnc-stats-widget__value--total { color: #5E3A8E; }
        
        .cnc-stats-widget__label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .cnc-stats-widget__indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .cnc-stats-widget__indicator--available { background: #2ECC71; }
        .cnc-stats-widget__indicator--pending { background: #F39C12; }
        .cnc-stats-widget__indicator--booked { background: #34495E; }
    </style>
    
    <div class="cnc-stats-widget">
        <div class="cnc-stats-widget__item">
            <div class="cnc-stats-widget__value cnc-stats-widget__value--available"><?php echo $stats['available']; ?></div>
            <div class="cnc-stats-widget__label">
                <span class="cnc-stats-widget__indicator cnc-stats-widget__indicator--available"></span>Available
            </div>
        </div>
        <div class="cnc-stats-widget__item">
            <div class="cnc-stats-widget__value cnc-stats-widget__value--pending"><?php echo $stats['pending']; ?></div>
            <div class="cnc-stats-widget__label">
                <span class="cnc-stats-widget__indicator cnc-stats-widget__indicator--pending"></span>Pending
            </div>
        </div>
        <div class="cnc-stats-widget__item">
            <div class="cnc-stats-widget__value cnc-stats-widget__value--booked"><?php echo $stats['booked']; ?></div>
            <div class="cnc-stats-widget__label">
                <span class="cnc-stats-widget__indicator cnc-stats-widget__indicator--booked"></span>Booked
            </div>
        </div>
        <div class="cnc-stats-widget__item">
            <div class="cnc-stats-widget__value cnc-stats-widget__value--total"><?php echo $stats['total']; ?></div>
            <div class="cnc-stats-widget__label">Total Stalls</div>
        </div>
    </div>
    
    <?php if ($atts['show_link'] === 'true'): ?>
    <div style="text-align: center; margin-top: 20px;">
        <?php echo cnc_pdf_download_button_shortcode(['text' => 'Download Floor Plan', 'style' => 'outline']); ?>
    </div>
    <?php endif; ?>
    
    <?php
    elseif ($atts['style'] === 'inline'):
    ?>
    <style>
        .cnc-stats-inline {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }
        
        .cnc-stats-inline__item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .cnc-stats-inline__dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        
        .cnc-stats-inline__dot--available { background: #2ECC71; }
        .cnc-stats-inline__dot--pending { background: #F39C12; }
        .cnc-stats-inline__dot--booked { background: #34495E; }
        
        .cnc-stats-inline__value {
            font-weight: 700;
        }
    </style>
    
    <div class="cnc-stats-inline">
        <div class="cnc-stats-inline__item">
            <span class="cnc-stats-inline__dot cnc-stats-inline__dot--available"></span>
            <span>Available: <strong class="cnc-stats-inline__value"><?php echo $stats['available']; ?></strong></span>
        </div>
        <div class="cnc-stats-inline__item">
            <span class="cnc-stats-inline__dot cnc-stats-inline__dot--pending"></span>
            <span>Pending: <strong class="cnc-stats-inline__value"><?php echo $stats['pending']; ?></strong></span>
        </div>
        <div class="cnc-stats-inline__item">
            <span class="cnc-stats-inline__dot cnc-stats-inline__dot--booked"></span>
            <span>Booked: <strong class="cnc-stats-inline__value"><?php echo $stats['booked']; ?></strong></span>
        </div>
    </div>
    
    <?php
    else: // minimal
    ?>
    <span style="font-family: 'Inter', sans-serif; font-size: 14px;">
        <span style="color: #2ECC71; font-weight: 600;"><?php echo $stats['available']; ?></span> available / 
        <span style="color: #666;"><?php echo $stats['total']; ?></span> total stalls
    </span>
    <?php
    endif;
    
    return ob_get_clean();
}
add_shortcode('cnc_stall_stats', 'cnc_stall_stats_shortcode');

/**
 * Floor Plan Legend Shortcode
 * Usage: [cnc_floorplan_legend]
 */
function cnc_floorplan_legend_shortcode($atts) {
    $atts = shortcode_atts([
        'show_pricing' => 'true',
        'show_types'   => 'true',
    ], $atts, 'cnc_floorplan_legend');
    
    $shell_rate = get_option('cnc_pdf_shell_rate', '11500');
    $raw_rate = get_option('cnc_pdf_raw_rate', '10500');
    
    $stall_types = [
        'AS' => ['name' => 'Premium Large', 'dim' => '6m x 6m', 'area' => 36],
        'BS' => ['name' => 'Large Suite', 'dim' => '6x4 & 3x3', 'area' => 33],
        'DS' => ['name' => 'Medium Suite', 'dim' => '6x3 & 3x3', 'area' => 27],
        'ES' => ['name' => 'Standard Large', 'dim' => '6m x 4m', 'area' => 24],
        'FS' => ['name' => 'Standard Medium', 'dim' => '6m x 3m', 'area' => 18],
        'GS' => ['name' => 'Compact', 'dim' => '4m x 3m', 'area' => 12],
        'A-F' => ['name' => 'Shell Scheme', 'dim' => '3m x 3m', 'area' => 9],
    ];
    
    ob_start();
    ?>
    <style>
        .cnc-legend {
            font-family: 'Inter', sans-serif;
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .cnc-legend__title {
            font-family: 'Poppins', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #C6308C;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .cnc-legend__section {
            margin-bottom: 20px;
        }
        
        .cnc-legend__section:last-child {
            margin-bottom: 0;
        }
        
        .cnc-legend__subtitle {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        
        .cnc-legend__status-list {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .cnc-legend__status-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .cnc-legend__color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
        }
        
        .cnc-legend__color--available { background: #2ECC71; }
        .cnc-legend__color--pending { background: #F39C12; }
        .cnc-legend__color--booked { background: #34495E; }
        
        .cnc-legend__types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px;
        }
        
        .cnc-legend__type-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
        }
        
        .cnc-legend__type-code {
            font-weight: 700;
            color: #5E3A8E;
            width: 35px;
        }
        
        .cnc-legend__type-name {
            flex: 1;
            color: #333;
        }
        
        .cnc-legend__type-area {
            font-weight: 600;
            color: #666;
            background: #fff;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .cnc-legend__pricing {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .cnc-legend__price-box {
            background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
            color: #fff;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        
        .cnc-legend__price-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .cnc-legend__price-value {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 700;
        }
        
        .cnc-legend__price-note {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 3px;
        }
    </style>
    
    <div class="cnc-legend">
        <h3 class="cnc-legend__title">
            <svg width="20" height="20" fill="#C6308C" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
            Legend & Configuration
        </h3>
        
        <div class="cnc-legend__section">
            <div class="cnc-legend__subtitle">Status</div>
            <div class="cnc-legend__status-list">
                <div class="cnc-legend__status-item">
                    <span class="cnc-legend__color cnc-legend__color--available"></span>
                    <span>Available</span>
                </div>
                <div class="cnc-legend__status-item">
                    <span class="cnc-legend__color cnc-legend__color--pending"></span>
                    <span>Pending</span>
                </div>
                <div class="cnc-legend__status-item">
                    <span class="cnc-legend__color cnc-legend__color--booked"></span>
                    <span>Booked</span>
                </div>
            </div>
        </div>
        
        <?php if ($atts['show_types'] === 'true'): ?>
        <div class="cnc-legend__section">
            <div class="cnc-legend__subtitle">Stall Types</div>
            <div class="cnc-legend__types-grid">
                <?php foreach ($stall_types as $code => $type): ?>
                <div class="cnc-legend__type-item">
                    <span class="cnc-legend__type-code"><?php echo $code; ?></span>
                    <span class="cnc-legend__type-name"><?php echo $type['name']; ?></span>
                    <span class="cnc-legend__type-area"><?php echo $type['area']; ?> m²</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($atts['show_pricing'] === 'true'): ?>
        <div class="cnc-legend__section">
            <div class="cnc-legend__subtitle">Pricing Rates</div>
            <div class="cnc-legend__pricing">
                <div class="cnc-legend__price-box">
                    <div class="cnc-legend__price-label">Shell Scheme</div>
                    <div class="cnc-legend__price-value">₹<?php echo number_format($shell_rate); ?></div>
                    <div class="cnc-legend__price-note">per m² (Min 9 m²)</div>
                </div>
                <div class="cnc-legend__price-box">
                    <div class="cnc-legend__price-label">Raw Space</div>
                    <div class="cnc-legend__price-value">₹<?php echo number_format($raw_rate); ?></div>
                    <div class="cnc-legend__price-note">per m² (Min 18 m²)</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('cnc_floorplan_legend', 'cnc_floorplan_legend_shortcode');
