<?php
/**
 * Simple mail template manager (admin).
 */

if (!defined('ABSPATH')) {
    exit;
}

function cnc_register_mail_template_settings() {
    register_setting('cnc_mail_templates', 'cnc_mail_templates', [
        'type' => 'array',
        'sanitize_callback' => function($input) {
            $out = [];
            foreach ((array)$input as $key => $val) {
                $out[sanitize_key($key)] = wp_kses_post($val);
            }
            return $out;
        },
        'default' => [],
    ]);

    add_settings_section('cnc_mail_templates_main', 'Mail Templates', function() {
        echo '<p>Manage email templates used for booking approvals and notifications. Placeholders: {company}, {stall_list}, {status}, {contact_name}.</p>';
    }, 'cnc_mail_templates');

    $fields = [
        'booking_received' => 'Booking Received (Customer)',
        'booking_approved' => 'Booking Approved (Customer)',
        'visitor_registered' => 'Visitor Registered (Customer)',
    ];

    foreach ($fields as $key => $label) {
        add_settings_field(
            $key,
            esc_html($label),
            'cnc_render_mail_template_field',
            'cnc_mail_templates',
            'cnc_mail_templates_main',
            ['key' => $key]
        );
    }
}
add_action('admin_init', 'cnc_register_mail_template_settings');

function cnc_render_mail_template_field($args) {
    $templates = get_option('cnc_mail_templates', []);
    $key = $args['key'];
    $val = isset($templates[$key]) ? $templates[$key] : '';
    echo '<textarea name="cnc_mail_templates[' . esc_attr($key) . ']" rows="6" cols="60" class="large-text code">' . esc_textarea($val) . '</textarea>';
}

function cnc_mail_templates_menu() {
    add_submenu_page(
        'edit.php?post_type=cnc_booking',
        'CNC Mail Templates',
        'Mail Templates',
        'manage_options',
        'cnc-mail-templates',
        'cnc_render_mail_templates_page'
    );
}
add_action('admin_menu', 'cnc_mail_templates_menu');

function cnc_render_mail_templates_page() {
    ?>
    <div class="wrap">
        <h1>CNC Mail Templates</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cnc_mail_templates');
            do_settings_sections('cnc_mail_templates');
            submit_button('Save Templates');
            ?>
        </form>
    </div>
    <?php
}

/**
 * Helper to fetch a template with replacements.
 */
function cnc_get_mail_template($key, $replacements = []) {
    $templates = get_option('cnc_mail_templates', []);
    $tpl = $templates[$key] ?? '';
    if (empty($tpl)) {
        return '';
    }
    foreach ($replacements as $k => $v) {
        $tpl = str_replace('{' . $k . '}', $v, $tpl);
    }
    return $tpl;
}
