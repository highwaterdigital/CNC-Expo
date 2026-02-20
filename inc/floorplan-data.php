<?php
/**
 * Centralized Floor Plan Data
 * Returns the array of stall definitions for Hall 3
 */

require_once get_stylesheet_directory() . '/inc/floorplan-config.php';

function cnc_get_floorplan_data() {
    // 1. Fetch Booking Data
    $booking_data = [];
    $args = [
        'post_type'      => 'cnc_booking',
        'post_status'    => 'publish', 
        'posts_per_page' => -1,
        'orderby'        => 'modified', // Ensure we get latest changes if caching is weird
        'order'          => 'DESC',
        'meta_query'     => [
            [
                'key'     => 'cnc_booking_status',
                'value'   => ['approved', 'pending'],
                'compare' => 'IN'
            ]
        ]
    ];

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $sid = get_post_meta(get_the_ID(), 'cnc_stall_id', true);
            $status = get_post_meta(get_the_ID(), 'cnc_booking_status', true);
            $company = get_the_title();
            
            if ($sid) {
                $booking_data[$sid] = [
                    'status'  => $status,
                    'company' => $company,
                    'post_id' => get_the_ID()
                ];
            }
        }
        wp_reset_postdata();
    }

    // CHECK FOR SAVED LAYOUT
    $saved_layout = get_option('cnc_floorplan_layout');
    
    // Fallback to JSON file if no saved layout
    if (empty($saved_layout)) {
        $json_file = get_stylesheet_directory() . '/hall3_layout.json';
        if (file_exists($json_file)) {
            $saved_layout = json_decode(file_get_contents($json_file), true);
        }
    }
    
    $stalls = [];
    if (!empty($saved_layout) && is_array($saved_layout)) {
        foreach ($saved_layout as $item) {
            $id = $item['id'];
            
            // Default Status from Layout
            $status = isset($item['status']) ? $item['status'] : 'available';
            $type = isset($item['type']) ? $item['type'] : 'stall';
            $company = '';
            $post_id = 0;

            // OVERRIDE with Real Booking Data
            if (isset($booking_data[$id])) {
                $real_status = $booking_data[$id]['status'];
                // Map booking status to visual status
                if ($real_status === 'approved') $status = 'booked';
                elseif ($real_status === 'pending') $status = 'pending';
                
                $company = $booking_data[$id]['company'];
                $post_id = $booking_data[$id]['post_id'];
            } else {
                // FIX: If layout says 'booked' or 'pending' but no post exists, reset to available
                // This ensures deleting a post frees the stall dynamically
                if ($type === 'stall' && ($status === 'booked' || $status === 'pending')) {
                    $status = 'available';
                }
            }
            
            // Calculate area if missing
            $w = intval($item['w']);
            $h = intval($item['h']);
            $area = isset($item['area']) ? $item['area'] : ($w * $h); // Approximate if missing
            
            // Check if this is a common area (non-clickable)
            $common_area_types = [
                'fire-exit', 'entry', 'exit', 'freight-entry', 'washroom', 
                'stairs', 'stage', 'service', 'registration', 'lounge', 
                'storage', 'electrical', 'pillar', 'corridor', 'blank', 'custom-area',
                'food-court', 'networking'
            ];
            $is_common_area = isset($item['is_common_area']) ? $item['is_common_area'] : in_array($type, $common_area_types);

            $stalls[] = [
                'id' => $id,
                'r' => intval($item['r']), 
                'c' => intval($item['c']), 
                'h' => $h, 
                'w' => $w,
                'type' => $type,
                'status' => $status, // This is the LIVE status
                'price' => isset($item['price']) ? $item['price'] : 0,
                'dim' => isset($item['dim']) ? $item['dim'] : '',
                'area' => $area,
                'company' => $company,
                'post_id' => $post_id,
                'fill' => isset($item['fill']) ? $item['fill'] : '',
                'border' => isset($item['border']) ? $item['border'] : '',
                'custom_color' => isset($item['custom_color']) ? $item['custom_color'] : '',
                // New Common Area Fields
                'display_label' => isset($item['display_label']) ? $item['display_label'] : '',
                'text_dir' => isset($item['text_dir']) ? $item['text_dir'] : 'horizontal',
                'text_color' => isset($item['text_color']) ? $item['text_color'] : '#ffffff',
                'font_size' => isset($item['font_size']) ? $item['font_size'] : 'auto',
                'show_border' => isset($item['show_border']) ? $item['show_border'] : false,
                'border_color' => isset($item['border_color']) ? $item['border_color'] : '#333333',
                'is_common_area' => $is_common_area
            ];
        }
    }
    
    return $stalls;
}
