<?php
/**
 * CNC Router Logic
 * Handles URL mapping to shortcodes
 */

if (!defined('ABSPATH')) exit;

/**
 * Get the shortcode for the current request
 */
function cnc_get_current_route_shortcode() {
    $slug   = cnc_get_request_slug();
    $routes = cnc_get_routes_map();

    if (array_key_exists($slug, $routes)) {
        // Force 200 OK status and fix WP Query flags
        global $wp_query;
        $wp_query->is_404 = false;
        status_header(200);
        return $routes[$slug];
    }

    // Default to 404
    status_header(404);
    return '[cnc_404_page]';
}

/**
 * Compute the current request slug (without query string or site prefix)
 */
function cnc_get_request_slug() {
    $request_uri = strtok($_SERVER['REQUEST_URI'], '?');
    $site_path   = parse_url(home_url(), PHP_URL_PATH);

    $slug = $request_uri;
    if ($site_path && $site_path !== '/') {
        $slug = preg_replace('#^' . rtrim(preg_quote($site_path, '#'), '/') . '#', '', $request_uri);
    }

    return trim($slug, '/');
}

/**
 * Get the routed shortcode map (filterable)
 */
function cnc_get_routes_map() {
    $routes = [
        ''                  => '[cnc_home_page]',
        'home'              => '[cnc_home_page]',
        'book-space'        => '[cnc_book_space_page]',
        'floorplan'         => '[cnc_book_space_page]', // Alias so /floorplan shows the booking map
        'visitors'          => '[cnc_visitors_page]',
        'exhibitors'        => '[cnc_exhibitors_page]',
        'privacy-policy'    => '[cnc_privacy_page]',
        'terms-conditions'  => '[cnc_terms_page]',
        'cookie-policy'     => '[cnc_cookie_page]',
        'participants'      => '[cnc_participants_page]',
        'about'             => '[cnc_about_page]',
        'contact'           => '[cnc_contact_page]',
        'sponsors-partners' => '[cnc_sponsorship_page]',
        'sponsors'          => '[cnc_sponsors_page]',
        'gallery'           => '[cnc_gallery_page]',
        'nearby-stays'      => '[cnc_nearby_stays_page]',
        'sponsorship'       => '[cnc_sponsorship_page]',
        'my-account'        => '[expo_customer_dashboard]',
        'admin-dashboard'   => '[expo_admin_dashboard]',
        // Add new pages here
    ];

    return apply_filters('cnc_routes', $routes);
}
