<?php
/**
 * Cleanup and Conflict Resolution
 * Handles dequeuing of conflicting scripts and styles.
 */

if (!defined('ABSPATH')) exit;

/**
 * Dequeue conflicting scripts on CNC custom pages
 */
function cnc_dequeue_conflicts() {
    // Only run on our custom pages
    // We can't easily check is_page() because of our aggressive override, 
    // but we can check the URL or if our template is active.
    
    // List of handles to dequeue
    $scripts_to_remove = [
        'litespeed-lazy-load',
        'rocket-lazy-load',
        'smush-lazy-load',
        'autoptimize',
        'wc-cart-fragments', // WooCommerce often causes issues if not needed
        'jquery-ui-core',    // We use vanilla JS
        'jquery-migrate'
    ];

    foreach ($scripts_to_remove as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }
}
// add_action('wp_enqueue_scripts', 'cnc_dequeue_conflicts', 9999);

/**
 * Remove conflicting actions from wp_head/wp_footer
 */
function cnc_remove_head_conflicts() {
    // LiteSpeed Cache
    remove_action('wp_head', 'litespeed_buffer_before');
    remove_action('wp_footer', 'litespeed_buffer_after');
    
    // Remove emoji script
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
// add_action('init', 'cnc_remove_head_conflicts');
