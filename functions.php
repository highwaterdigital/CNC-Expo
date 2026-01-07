<?php
/**
 * CNC Child Theme Functions v5.0
 * REFACTORED: Removed legacy cleanup scripts (cnc_hide_default_elements, cnc_cleanup_js)
 * and now let WordPress templates render header/footer for the routed content.
 *
 * @package cnc_child
 */

if (!defined('ABSPATH')) exit;
define('CNC_VERSION', '5.1.2'); // Fixed: Login popup integration and home page

/**
 * Detect whether the current request is the Elementor preview/editor for the home page.
 *
 * @return bool
 */
function cnc_is_elementor_preview_request() {
    if (!class_exists('\\Elementor\\Plugin')) {
        return false;
    }

    if (is_customize_preview()) {
        return true;
    }

    $preview_keys = ['elementor-preview', 'elementor-template-preview', 'elementor-editor', 'elementor-library-editor'];
    foreach ($preview_keys as $key) {
        if (!empty($_GET[$key])) {
            return true;
        }
    }

    if (!empty($_GET['action']) && in_array($_GET['action'], ['elementor', 'elementor_ajax'], true)) {
        return true;
    }

    $elementor = \Elementor\Plugin::instance();
    if ($elementor && property_exists($elementor, 'preview')) {
        $preview = $elementor->preview;
        if ($preview && method_exists($preview, 'is_preview') && $preview->is_preview()) {
            return true;
        }
    }

    return false;
}

/**
 * Returns a lightweight script that re‑runs Elementor ready hooks for elements injected by the CNC home shortcode.
 *
 * @return string
 */
function cnc_get_elementor_ready_script() {
    ob_start();
    ?>
    <script>
    (function runCncElementorReady() {
        const selector = '.cnc-elementor-content-wrapper .elementor-element';
        let attempt = 0;
        const maxAttempts = 120;

        function initWidgets() {
            const frontend = window.elementorFrontend;
            if (!frontend || !frontend.elementsHandler) {
                if (attempt < maxAttempts) {
                    attempt++;
                    requestAnimationFrame(initWidgets);
                }
                return;
            }

            const widgets = document.querySelectorAll(selector);
            widgets.forEach(widget => {
                if (widget instanceof HTMLElement) {
                    frontend.elementsHandler.runReadyTrigger(widget);
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initWidgets);
        } else {
            initWidgets();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}

/**
 * 💬 WHATSAPP CLOUD API (CUSTOMER NOTIFICATIONS)
 * ────────────────────────────────────────────────────────────────────────────
 * Production WhatsApp Business API for customer communications
 * Features: Order confirmations, status updates, delivery notifications
 */
if (!defined('TK_WHATSAPP_API_URL')) {
    define( 'TK_WHATSAPP_API_URL',         'https://graph.facebook.com/v18.0' );
}
if (!defined('TK_WHATSAPP_API_TOKEN')) {
    define( 'TK_WHATSAPP_API_TOKEN',       'CnIKLgjyirKv8c/cAxIGZW50OndhIhVDYWJsZSBOZXQgQ29udmVyZ2VuY2VQzMigyQYaQNXwDcGCLVx0E+aCFvphTTlbpSdgLvwrZHeI1cbTeL2VcwxfkHxkvZBocupm1t7KjXDcUh8NNPXji+EHorqLiwUSL20JT93s7ca58Fqyu5mlZC2cWOzmVcP5BYoEIYGLHPy/OMePw4sSrxD0REmcevVz' );
}
if (!defined('TK_WHATSAPP_PHONE_NUMBER_ID')) {
    define( 'TK_WHATSAPP_PHONE_NUMBER_ID', '1163744555384343' );
}
if (!defined('TK_WHATSAPP_VERIFY_TOKEN')) {
    define( 'TK_WHATSAPP_VERIFY_TOKEN',    '12345' );
}

// Include Helper Modules
require_once get_stylesheet_directory() . '/inc/cleanup.php';
require_once get_stylesheet_directory() . '/inc/router.php';
require_once get_stylesheet_directory() . '/inc/cpt/bookings.php';
require_once get_stylesheet_directory() . '/inc/cpt/exhibitors.php';
require_once get_stylesheet_directory() . '/inc/cpt/gallery.php';
require_once get_stylesheet_directory() . '/inc/admin-exhibitor-manager.php';
require_once get_stylesheet_directory() . '/inc/forms/visitor-registration-form.php';
require_once get_stylesheet_directory() . '/inc/forms/visitor-hooks.php';
require_once get_stylesheet_directory() . '/inc/forms/booking-hooks.php';

/** Shortcode that renders the visitor registration form. */
function cnc_visitor_registration_form_shortcode($atts = []) {
    $is_disabled = function_exists('cnc_is_visitor_registration_enabled') ? !cnc_is_visitor_registration_enabled() : true;
    return cnc_get_visitor_registration_form_html($is_disabled);
}
add_shortcode('cnc_visitor_registration_form', 'cnc_visitor_registration_form_shortcode');
require_once get_stylesheet_directory() . '/inc/admin-floorplan.php';
require_once get_stylesheet_directory() . '/inc/admin-floorplan-designer.php';
require_once get_stylesheet_directory() . '/inc/rest/routes.php';
require_once get_stylesheet_directory() . '/inc/visitor-registration-admin.php';
require_once get_stylesheet_directory() . '/inc/gallery-admin.php';

// Include Components
require_once get_stylesheet_directory() . '/components/login-popup/login-popup.php';

// Include Integrations
require_once get_stylesheet_directory() . '/integrations/google-sheets/credentials.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/sheets-config.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/registration-logger.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/class-cnc-sheet-sync.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/class-cnc-sheet-importer.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/webhook-handler.php';
require_once get_stylesheet_directory() . '/integrations/google-sheets/ajax-registration-handler.php';
require_once get_stylesheet_directory() . '/integrations/whatsapp/class-cnc-whatsapp-api.php';
require_once get_stylesheet_directory() . '/inc/auth/class-cnc-custom-otp.php';
require_once get_stylesheet_directory() . '/inc/notifications.php';
require_once get_stylesheet_directory() . '/inc/debug-logger.php';
require_once get_stylesheet_directory() . '/inc/mail-templates.php';

// Hide admin bar for non-admin users (customers)
add_filter('show_admin_bar', function($show) {
    return current_user_can('manage_options') ? $show : false;
});

/**
 * Disable intermediate image sizes so only the original file is stored/served.
 */
function cnc_disable_intermediate_image_sizes($sizes) {
    return array();
}
add_filter('intermediate_image_sizes_advanced', 'cnc_disable_intermediate_image_sizes', 10, 1);
add_filter('big_image_size_threshold', '__return_false');

/**
 * Force Single Exhibitor Template
 */
function cnc_force_exhibitor_template($template) {
    if (is_singular('cnc_exhibitor')) {
        $custom_template = get_stylesheet_directory() . '/pages/exhibitors/single-exhibitor.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'cnc_force_exhibitor_template');

/**
 * Helper: fetch published page content by slug/front page.
 * Used for home fallback when modular section files are missing.
 */
function cnc_get_page_content_by_slug($slug) {
    $page_object = null;

    if ($slug === '' || $slug === 'home') {
        $front_id = absint(get_option('page_on_front'));
        if ($front_id) {
            $page_object = get_post($front_id);
        }
    }

    if (!$page_object && $slug) {
        $page_object = get_page_by_path($slug);
    }

    if ($page_object instanceof WP_Post) {
        // Prevent shortcode recursion by stripping our own shortcode from fallback content.
        $content = preg_replace('/\\[cnc_home_page[^\\]]*\\]/i', '', $page_object->post_content);
        return apply_filters('the_content', $content);
    }

    return '';
}

/**
 * Return the filtered published content for a page built with Elementor.
 * Strips our `[cnc_home_page]` shortcode to avoid recursive rendering.
 */
function cnc_get_elementor_page_content($post_id) {
    // Prevent recursion if we are already in Elementor preview/editor
    if (cnc_is_elementor_preview_request()) {
        return '';
    }

    if (empty($post_id)) {
        return '';
    }

    $elementor_post = get_post($post_id);
    if (!$elementor_post instanceof WP_Post) {
        return '';
    }

    $elementor_data = get_post_meta($post_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        return '';
    }

    $elementor_content = '';
    if (class_exists('\\Elementor\\Plugin')) {
        global $post;
        $original_post = $post;
        $post = $elementor_post;
        setup_postdata($post);

        $elementor = \Elementor\Plugin::instance();
        if ($elementor && isset($elementor->frontend)) {
            $elementor_content = $elementor->frontend->get_builder_content_for_display($post_id);
        }

        wp_reset_postdata();
        $post = $original_post;
    }

    if (!empty($elementor_content)) {
        return sprintf(
            '<div class="elementor elementor-%1$d" data-elementor-id="%1$d" data-elementor-type="wp-page">%2$s</div>',
            $post_id,
            $elementor_content
        );
    }

    $content = preg_replace('/\\[cnc_home_page[^\\]]*\\]/i', '', $elementor_post->post_content);
    if (trim($content) === '') {
        return '';
    }

    return apply_filters('the_content', $content);
}

/**
 * Return Elementor-rendered content (plus ready script) for a page identified by slug.
 *
 * @param string $slug
 * @return string
 */
function cnc_get_elementor_page_by_slug($slug) {
    if (empty($slug)) {
        return '';
    }

    $page = get_page_by_path($slug);
    if (!$page instanceof WP_Post) {
        return '';
    }

    $elementor_content = cnc_get_elementor_page_content($page->ID);
    if (empty($elementor_content)) {
        return '';
    }

    return '<div class="cnc-elementor-content-wrapper">' . $elementor_content . '</div>' . cnc_get_elementor_ready_script();
}
/**
 * Enqueue Assets
 */
function cnc_enqueue_assets() {
    $ver = CNC_VERSION;
    
    // Parent theme stylesheet
    if (!wp_style_is('cnc-parent', 'enqueued')) {
        wp_enqueue_style('cnc-parent', get_template_directory_uri() . '/style.css', array(), $ver);
    }
    
    // Child theme stylesheet
    if (!wp_style_is('cnc-child', 'enqueued')) {
        wp_enqueue_style('cnc-child', get_stylesheet_uri(), array('cnc-parent'), $ver);
    }
    
    // Main JavaScript
    if (!wp_script_is('cnc-main', 'enqueued')) {
        wp_enqueue_script('cnc-main', get_stylesheet_directory_uri() . '/assets/js/main.js', array(), $ver, true);
    }
}
add_action('wp_enqueue_scripts', 'cnc_enqueue_assets');

/**
 * Append Participants link to supported header menus.
 */
function shape_append_participants_menu_item($items, $args) {
    $url = esc_url(home_url('/participants'));
    $label = esc_html__('Participants', 'shapexpo-child');
    if (false === strpos($items, 'shape-participants-link')) {
        $items .= '<li class="menu-item shape-participants-link"><a href="' . $url . '">' . $label . '</a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'shape_append_participants_menu_item', 20, 2);

function shape_append_sponsorship_menu_item($items, $args) {
    if (false === strpos($items, 'shape-sponsorship-link')) {
        $url = esc_url(home_url('/sponsorship'));
        $label = esc_html__('Sponsorship', 'shapexpo-child');
        $items .= '<li class="menu-item shape-sponsorship-link"><a href="' . $url . '">' . $label . '</a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'shape_append_sponsorship_menu_item', 25, 2);


/**
 * Register Home Page Shortcode
 */
function cnc_home_page_shortcode() {
    error_log('dYZ? CNC v5.0: Shortcode [cnc_home_page] executing...');
    
    // Prevent recursion
    static $executing = false;
    if ($executing) {
        error_log('?s??,? CNC v5.0: Prevented shortcode recursion');
        return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Shortcode recursion prevented</div>';
    }
    $executing = true;
    $front_page_id = absint(get_option('page_on_front'));

    // Fix: Prevent recursive rendering in Elementor Editor
    if (cnc_is_elementor_preview_request()) {
        $executing = false;
        return '<div class="cnc-shortcode-placeholder" style="padding:20px; border:2px dashed #ccc; background:#f9f9f9; text-align:center; color:#888;">[cnc_home_page] Shortcode Placeholder (Disabled in Editor)</div>';
    }

    // Primary shortcode file (modular one)
    $shortcode_file = get_stylesheet_directory() . '/pages/home/home-shortcode.php';

    // Detect whether the modular section templates exist
    $sections_dir  = get_stylesheet_directory() . '/pages/home/sections/';
    $section_files = glob(trailingslashit($sections_dir) . '*.php');
    $has_sections  = is_array($section_files) && count($section_files) > 0;

    if (!$has_sections) {
        $fallback_content = function_exists('cnc_get_page_content_by_slug') ? cnc_get_page_content_by_slug('home') : '';
        if (!empty($fallback_content)) {
            error_log('CNC Home: No section templates found. Rendering published Home page content.');
            $executing = false;
            return '<div class="cnc-home-wrapper cnc-home-fallback">' . $fallback_content . '</div>';
        }

        error_log('CNC Home: No section templates or published Home page content found.');
        $executing = false;
        return '<div class="cnc-home-wrapper" style="padding:80px 20px; text-align:center; background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%); color:#fff;">
            <h1 style="font-size:2.5rem; margin-bottom:10px;">CNC Expo 2026</h1>
            <p style="max-width:700px; margin:0 auto 20px; line-height:1.6;">Welcome to Cable Net Convergence Expo. Content for the home page is missing. Please add home sections under <code>pages/home/sections/</code> or publish content on the Home page.</p>
            <a href="' . esc_url(home_url('/book-space')) . '" style="display:inline-block; padding:12px 28px; background:#ffeb3b; color:#0D0D0D; border-radius:6px; font-weight:700;">Book Your Stall</a>
        </div>';
    }
    
    ob_start();
    
    if (file_exists($shortcode_file)) {
        error_log('?o. CNC v5.0: Loading shortcode from: ' . $shortcode_file);
        include $shortcode_file;
        $elementor_content = cnc_get_elementor_page_content($front_page_id);
        if (!empty($elementor_content)) {
            echo '<div class="cnc-elementor-content-wrapper">' . $elementor_content . '</div>';
            echo cnc_get_elementor_ready_script();
        }
    } else {
        error_log('??O CNC v5.0: Shortcode file not found: ' . $shortcode_file);
        $executing = false;
        return '<div style="padding:3rem; background:#ffebee; border:3px solid #c62828; text-align:center;">
            <h2 style="color:#c62828;">?s??,? Home Page Content Missing</h2>
            <p>Shortcode file not found: <code>pages/home/home-shortcode.php</code></p>
        </div>';
    }
    
    $content = ob_get_clean();
    $executing = false;
    
    error_log('📏 CNC v5.0: Shortcode output length: ' . strlen($content) . ' bytes');
    return $content;
}
add_shortcode('cnc_home_page', 'cnc_home_page_shortcode');

/**
 * Register Book Space Shortcode
 */
function cnc_book_space_shortcode() {
    $file = get_stylesheet_directory() . '/pages/book-space/book-space-shortcode.php';
    if (file_exists($file)) {
        // The file returns the content via ob_get_clean()
        return include $file;
    }
    return '<div class="expo-error">Book space template missing.</div>';
}
add_shortcode('cnc_book_space_page', 'cnc_book_space_shortcode');


/**
 * Participants Slider Shortcode
 * Usage: [cnc_participants_slider]
 */
function cnc_participants_slider_shortcode() {
    // The file returns the content via ob_get_clean(), so we just return the result of include
    $content = include get_stylesheet_directory() . '/components/participants/participants-slider.php';
    return $content;
}
add_shortcode('cnc_participants_slider', 'cnc_participants_slider_shortcode');

/**
 * Register Page Shortcode
 * This will render the booking form.
 * Usage: [cnc_register_page]
 */
function cnc_register_page_shortcode() {
    // The file returns the content via ob_get_clean(), so we just return the result of include
    $content = include get_stylesheet_directory() . '/pages/register/register-shortcode.php';
    return $content;
}
add_shortcode('cnc_register_page', 'cnc_register_page_shortcode');

/**
 * Exhibitors Page Shortcode
 * Usage: [cnc_exhibitors_page]
 */
/* 
 * REPLACED: This function is now defined later in the file to use the new grid component.
 * See cnc_exhibitors_page_shortcode() below.
 */
/*
function cnc_exhibitors_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/exhibitors/exhibitors-page.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Exhibitors page template missing.</div>';
}
add_shortcode('cnc_exhibitors_page', 'cnc_exhibitors_page_shortcode');
*/

/**
 * Sponsorship page shortcode
 */
function cnc_sponsorship_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/sponsorship/sponsorship-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div class="expo-error">Sponsorship content missing.</div>';
}
add_shortcode('cnc_sponsorship_page', 'cnc_sponsorship_page_shortcode');

/**
 * Privacy Policy Page Shortcode
 */
function cnc_privacy_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/privacy/privacy-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Privacy policy template missing.</div>';
}
add_shortcode('cnc_privacy_page', 'cnc_privacy_page_shortcode');

/**
 * Terms & Conditions Page Shortcode
 */
function cnc_terms_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/terms/terms-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Terms & Conditions template missing.</div>';
}
add_shortcode('cnc_terms_page', 'cnc_terms_page_shortcode');

/**
 * Cookie Policy Page Shortcode
 */
function cnc_cookie_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/cookie/cookie-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Cookie policy template missing.</div>';
}
add_shortcode('cnc_cookie_page', 'cnc_cookie_page_shortcode');

/**
 * Participants Page Shortcode
 */
function cnc_participants_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/participants/participants-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Participants page template missing.</div>';
}
add_shortcode('cnc_participants_page', 'cnc_participants_page_shortcode');

/**
 * About Page Shortcode
 */
function cnc_about_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/about/about-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">About page template missing.</div>';
}
add_shortcode('cnc_about_page', 'cnc_about_page_shortcode');

/**
 * Contact Page Shortcode
 */
function cnc_contact_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/contact/contact-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Contact page template missing.</div>';
}
add_shortcode('cnc_contact_page', 'cnc_contact_page_shortcode');

/**
 * Sponsors & Partners Page Shortcode
 */
function cnc_sponsors_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/sponsors/sponsors-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Sponsors page template missing.</div>';
}
add_shortcode('cnc_sponsors_page', 'cnc_sponsors_page_shortcode');

/**
 * Gallery Shortcodes
 * [cnc_gallery id="123"] -> Single Gallery
 * [cnc_gallery_grid] -> All Galleries
 */
function cnc_gallery_page_shortcode() {
    $elementor_output = cnc_get_elementor_page_by_slug('gallery');
    if ($elementor_output) {
        return $elementor_output;
    }
    return cnc_gallery_shortcode_v2(['id' => 0]);
}
add_shortcode('cnc_gallery_page', 'cnc_gallery_page_shortcode');

function cnc_gallery_shortcode_v2($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    
    ob_start();
    if ($atts['id']) {
        include get_stylesheet_directory() . '/components/gallery/single.php';
    } else {
        include get_stylesheet_directory() . '/components/gallery/archive.php';
    }
    return ob_get_clean();
}
add_shortcode('cnc_gallery', 'cnc_gallery_shortcode_v2');
add_shortcode('cnc_gallery_grid', 'cnc_gallery_shortcode_v2');

/**
 * Run CNC shortcodes inside Elementor HTML widgets so [cnc_gallery] renders when dropped inside that widget.
 *
 * @param string $content
 * @param \Elementor\Widget_Base $widget
 * @return string
 */
function cnc_elementor_html_widget_do_shortcode($content, $widget) {
    if (!class_exists('\\Elementor\\Widget_Base') || !($widget instanceof \Elementor\Widget_Base)) {
        return $content;
    }

    if ($widget->get_name() !== 'html') {
        return $content;
    }

    if (false === strpos($content, '[cnc_')) {
        return $content;
    }

    $rendered = do_shortcode($content);
    return $rendered ? $rendered : $content;
}
add_filter('elementor/widget/render_content', 'cnc_elementor_html_widget_do_shortcode', 10, 2);

/**
 * Force Single Gallery Template
 */
function cnc_force_gallery_template($template) {
    if (is_singular('cnc_gallery')) {
        $custom_template = get_stylesheet_directory() . '/pages/gallery/single-gallery.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'cnc_force_gallery_template');

/**
 * Nearby Stays Page Shortcode
 */
function cnc_nearby_stays_page_shortcode() {
    $file = get_stylesheet_directory() . '/pages/nearby-stays/nearby-stays-shortcode.php';
    if (file_exists($file)) {
        ob_start();
        include $file;
        return ob_get_clean();
    }
    return '<div style="padding:2rem; background:#ffebee; color:#c62828;">Nearby stays template missing.</div>';
}
add_shortcode('cnc_nearby_stays_page', 'cnc_nearby_stays_page_shortcode');

// Expo customer dashboard fallback shortcode (if plugin is late or missing).
function cnc_expo_customer_dashboard_shortcode() {
    // 1. Check if user is Admin -> Show Admin Dashboard
    if (current_user_can('manage_options')) {
        return do_shortcode('[expo_admin_dashboard]');
    }

    // 2. Otherwise -> Show Customer Dashboard
    // Enqueue assets directly (bypass plugin load order).
    // FORCE CHILD THEME ASSETS as per user request
    $theme_css       = get_stylesheet_directory_uri() . '/assets/css/expo-dashboard.css';
    $theme_js        = get_stylesheet_directory_uri() . '/assets/js/customer-dashboard.js';

    wp_enqueue_style('expo-booking-stall-map-fallback', $theme_css, [], '0.1.3');
    wp_enqueue_script('expo-booking-customer-dashboard', $theme_js, [], '0.2.0', true);

    // Localize REST root/nonce for dashboard JS.
    wp_localize_script(
        'expo-booking-customer-dashboard',
        'expoBooking',
        [
            'root'  => esc_url_raw(rest_url('expo/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'isLoggedIn' => is_user_logged_in(),
            'theme' => [
                'gold'   => '#D4AF37',
                'green'  => '#0B5E3C',
                'status' => [
                    'available' => '#5CB85C',
                    'pending'   => '#F39C12',
                    'booked'    => '#000000',
                ],
            ],
        ]
    );

    // FORCE CHILD THEME TEMPLATE as per user request
    $theme_template  = get_stylesheet_directory() . '/pages/account/customer-dashboard.php';
    if (!file_exists($theme_template)) {
        $theme_template = get_stylesheet_directory() . '/pages/account/expo-dashboard.php';
    }

    if (file_exists($theme_template)) {
        ob_start();
        include $theme_template;
        return ob_get_clean();
    }

    return '<div class="expo-error">Expo dashboard template missing.</div>';
}
if (!shortcode_exists('expo_customer_dashboard')) {
    add_shortcode('expo_customer_dashboard', 'cnc_expo_customer_dashboard_shortcode');
}

// Admin dashboard placeholder (theme-hosted) for approvals/quotations.
function cnc_expo_admin_dashboard_shortcode() {
    wp_enqueue_style('expo-booking-stall-map-fallback', get_stylesheet_directory_uri() . '/assets/css/expo-dashboard.css', [], '0.1.3');
    wp_enqueue_script('expo-booking-admin-dashboard', get_stylesheet_directory_uri() . '/assets/js/expo-admin-dashboard.js', [], '0.1.3', true);
    wp_localize_script(
        'expo-booking-admin-dashboard',
        'expoBookingAdmin',
        [
            'root'  => esc_url_raw(rest_url('expo/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
        ]
    );

    $template = get_stylesheet_directory() . '/pages/account/expo-admin-dashboard.php';
    if (file_exists($template)) {
        ob_start();
        include $template;
        return ob_get_clean();
    }
    return '<div class="expo-error">Admin dashboard template missing.</div>';
}
add_shortcode('expo_admin_dashboard', 'cnc_expo_admin_dashboard_shortcode');

/**
 * Expo login modal assets
 */
function cnc_enqueue_expo_login_assets() {
    $css = get_stylesheet_directory_uri() . '/assets/css/expo-login.css';
    $js  = get_stylesheet_directory_uri() . '/assets/js/expo-login.js';

    wp_enqueue_style('expo-login', $css, [], '0.1.4');
    wp_enqueue_script('expo-login', $js, [], '0.1.4', true);
    
    // Localize script data
    wp_localize_script(
        'expo-login',
        'expoLogin',
        [
            'root'      => esc_url_raw(rest_url('expo/v1')),
            'nonce'     => wp_create_nonce('wp_rest'),
            'redirect'  => home_url('/my-account'),
            'messages'  => [
                'otpSent'   => '✅ OTP sent! Check your phone.',
                'loginOk'   => '🎉 Login successful! Redirecting...',
                'error'     => '❌ Something went wrong. Please try again.'
            ],
        ]
    );
}
add_action('wp_enqueue_scripts', 'cnc_enqueue_expo_login_assets');

// Register Gallery CPT for admin uploads
function cnc_register_gallery_cpt() {
    register_post_type('cnc_gallery_item', [
        'labels' => [
            'name' => 'Gallery Items',
            'singular_name' => 'Gallery Item'
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-format-gallery',
        'supports' => ['title', 'thumbnail'],
        'has_archive' => false,
        'show_in_rest' => true
    ]);

    register_taxonomy('cnc_gallery_year', 'cnc_gallery_item', [
        'label' => 'Gallery Year',
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ]);

    add_theme_support('post-thumbnails', ['cnc_gallery_item']);
}
add_action('init', 'cnc_register_gallery_cpt');

/**
 * Gallery Meta Box (Video URL)
 */
function cnc_gallery_meta_box() {
    add_meta_box(
        'cnc_gallery_meta',
        'Gallery Details',
        'cnc_render_gallery_meta_box',
        'cnc_gallery_item',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cnc_gallery_meta_box');

function cnc_render_gallery_meta_box($post) {
    $video_url = get_post_meta($post->ID, 'cnc_gallery_video_url', true);
    wp_nonce_field('cnc_save_gallery_meta', 'cnc_gallery_nonce');
    ?>
    <p>
        <label for="cnc_gallery_video_url"><strong>Video URL (Optional)</strong></label><br>
        <input type="url" id="cnc_gallery_video_url" name="cnc_gallery_video_url" value="<?php echo esc_attr($video_url); ?>" style="width:100%;" placeholder="https://www.youtube.com/watch?v=...">
        <span class="description">Enter a YouTube URL to display a video instead of the image in the lightbox. The Featured Image will be used as the thumbnail.</span>
    </p>
    <?php
}

function cnc_save_gallery_meta($post_id) {
    if (!isset($_POST['cnc_gallery_nonce']) || !wp_verify_nonce($_POST['cnc_gallery_nonce'], 'cnc_save_gallery_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['cnc_gallery_video_url'])) {
        update_post_meta($post_id, 'cnc_gallery_video_url', sanitize_text_field($_POST['cnc_gallery_video_url']));
    }
}
add_action('save_post_cnc_gallery_item', 'cnc_save_gallery_meta');

/**
 * Testimonials CPT with designation meta
 */
function cnc_register_testimonial_cpt() {
    register_post_type('cnc_testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial'
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true
    ]);
}
add_action('init', 'cnc_register_testimonial_cpt');

function cnc_testimonial_meta_box() {
    add_meta_box(
        'cnc_testimonial_meta',
        'Testimonial Details',
        'cnc_render_testimonial_meta_box',
        'cnc_testimonial',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'cnc_testimonial_meta_box');

function cnc_render_testimonial_meta_box($post) {
    $designation = get_post_meta($post->ID, '_cnc_testimonial_designation', true);
    wp_nonce_field('cnc_save_testimonial_meta', 'cnc_testimonial_nonce');
    ?>
    <p>
        <label for="cnc_testimonial_designation">Designation / Company</label><br>
        <input type="text" id="cnc_testimonial_designation" name="cnc_testimonial_designation" value="<?php echo esc_attr($designation); ?>" style="width:100%;">
    </p>
    <p>Use the Featured Image for the person photo.</p>
    <?php
}

function cnc_save_testimonial_meta($post_id) {
    if (!isset($_POST['cnc_testimonial_nonce']) || !wp_verify_nonce($_POST['cnc_testimonial_nonce'], 'cnc_save_testimonial_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['cnc_testimonial_designation'])) {
        update_post_meta($post_id, '_cnc_testimonial_designation', sanitize_text_field($_POST['cnc_testimonial_designation']));
    }
}
add_action('save_post_cnc_testimonial', 'cnc_save_testimonial_meta');

/**
 * 404 Page Shortcode
 * Usage: [cnc_404_page]
 */
function cnc_404_page_shortcode() {
    // The file returns the content via ob_get_clean(), so we just return the result of include
    $content = include get_stylesheet_directory() . '/pages/404/404-shortcode.php';
    return $content;
}
add_shortcode('cnc_404_page', 'cnc_404_page_shortcode');

/**
 * Visitors Page Shortcode
 * Usage: [cnc_visitors_page]
 */
function cnc_visitors_page_shortcode() {
    // The file returns the content via ob_get_clean(), so we just return the result of include
    $content = include get_stylesheet_directory() . '/pages/visitors/visitors-shortcode.php';
    return $content;
}
add_shortcode('cnc_visitors_page', 'cnc_visitors_page_shortcode');

/**
 * Exhibitors Page Shortcode
 * Usage: [cnc_exhibitors_page]
 */
function cnc_exhibitors_page_shortcode() {
    $content = include get_stylesheet_directory() . '/components/exhibitors/grid.php';
    return $content;
}
add_shortcode('cnc_exhibitors_page', 'cnc_exhibitors_page_shortcode');

/**
 * Shortcode: [home_exhibitor_slider]
 * Displays an infinite scrolling slider of registered Exhibitor logos.
 * Links each logo to the Exhibitor's profile page.
 */
function cnc_dynamic_exhibitor_slider() {
    // 1. Query your registered exhibitors
    $args = array(
        'post_type'      => 'cnc_exhibitor', // Ensure this matches your CPT slug
        'posts_per_page' => -1,              // Get ALL exhibitors
        'post_status'    => 'publish',
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    );

    $query = new WP_Query($args);

    // If no exhibitors found, return nothing
    if (!$query->have_posts()) {
        return ''; 
    }

    // 2. Build the list of logo items
    $items_html = '';
    
    while ($query->have_posts()) {
        $query->the_post();
        
        // Only show if they have a Logo (Featured Image)
        if (has_post_thumbnail()) {
            $link = get_permalink(); // Link to their profile
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium'); // Use 'medium' size for speed
            $name = get_the_title();

            $items_html .= sprintf(
                '<a href="%s" class="cnc-slider-logo" title="View %s Profile"><img src="%s" alt="%s"></a>',
                esc_url($link),
                esc_attr($name),
                esc_url($image_url),
                esc_attr($name)
            );
        }
    }
    
    wp_reset_postdata();

    // If no logos were generated, exit
    if (empty($items_html)) {
        return '';
    }

    // 3. Return the HTML & CSS
    ob_start();
    ?>
    <style>
        .cnc-slider-wrapper {
            --slider-bg: #FFFFFF;
            --slider-border: rgba(0,0,0,0.06);
            --logo-height: 70px;
            --scroll-speed: 40s;
        }

        .cnc-slider-section {
            background: var(--slider-bg);
            border-bottom: 1px solid var(--slider-border);
            padding: 40px 0;
            overflow: hidden;
            position: relative;
            white-space: nowrap;
        }

        /* Vignette Fade Effect */
        .cnc-slider-section::before,
        .cnc-slider-section::after {
            content: "";
            position: absolute;
            top: 0;
            width: 120px;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        .cnc-slider-section::before { left: 0; background: linear-gradient(to right, #FFF, transparent); }
        .cnc-slider-section::after { right: 0; background: linear-gradient(to left, #FFF, transparent); }

        /* The Moving Track */
        .cnc-slider-track {
            display: inline-flex;
            align-items: center;
            gap: 80px;
            animation: cncMarquee var(--scroll-speed) linear infinite;
            padding-left: 80px;
        }

        /* Pause on Hover */
        .cnc-slider-track:hover {
            animation-play-state: paused;
        }

        /* Logo Item Styles */
        .cnc-slider-logo {
            display: block;
            transition: transform 0.3s ease;
        }
        .cnc-slider-logo:hover {
            transform: scale(1.1);
        }

        .cnc-slider-logo img {
            height: var(--logo-height);
            width: auto;
            object-fit: contain;
            filter: grayscale(100%) opacity(0.7);
            transition: all 0.3s ease;
            display: block;
        }

        .cnc-slider-logo:hover img {
            filter: grayscale(0%) opacity(1);
        }

        @keyframes cncMarquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); } /* Move 50% because we duplicate the list */
        }

        @media (max-width: 768px) {
            .cnc-slider-wrapper { --logo-height: 50px; }
            .cnc-slider-track { gap: 40px; padding-left: 40px; }
        }
    </style>

    <div class="cnc-slider-wrapper">
        <div class="cnc-slider-section">
            <div class="cnc-slider-track">
                <?php 
                // Output the list TWICE to create the seamless infinite loop
                echo $items_html; 
                echo $items_html; 
                ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('home_exhibitor_slider', 'cnc_dynamic_exhibitor_slider');

/**
 * Force long-lived sessions for all users (1 Year)
 * Ensures users stay logged in even after closing the browser tab.
 */
add_filter( 'auth_cookie_expiration', 'cnc_force_long_sessions', 99, 3 );
function cnc_force_long_sessions( $seconds, $user_id, $remember ) {
    return 365 * DAY_IN_SECONDS;
}

error_log('✅ CNC v5.0: All functions loaded successfully (legacy cleanup removed)');
