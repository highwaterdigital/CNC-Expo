<?php
/**
 * Participants slider admin settings.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('CNC_PARTICIPANTS_SLIDER_OPTION')) {
    define('CNC_PARTICIPANTS_SLIDER_OPTION', 'cnc_participants_slider_settings');
}

function cnc_participants_slider_clamp_int($value, $min, $max) {
    $value = (int) $value;
    if ($value < $min) {
        return $min;
    }
    if ($value > $max) {
        return $max;
    }
    return $value;
}

function cnc_get_participants_slider_defaults() {
    return [
        'title' => 'Our Participants',
        'subtitle' => 'Leading companies trusting our platform',
        'scroll_duration' => 30,
        'logo_width' => 180,
        'logo_height' => 100,
        'logo_gap' => 60,
        'section_padding' => 60,
        'max_items' => 0,
        'pause_on_hover' => 1,
        'grayscale' => 1,
    ];
}

function cnc_get_participants_slider_settings() {
    $defaults = cnc_get_participants_slider_defaults();
    $saved = get_option(CNC_PARTICIPANTS_SLIDER_OPTION, []);
    if (!is_array($saved)) {
        $saved = [];
    }
    $settings = wp_parse_args($saved, $defaults);

    $settings['title'] = sanitize_text_field($settings['title']);
    $settings['subtitle'] = sanitize_text_field($settings['subtitle']);
    $settings['scroll_duration'] = cnc_participants_slider_clamp_int($settings['scroll_duration'], 5, 180);
    $settings['logo_width'] = cnc_participants_slider_clamp_int($settings['logo_width'], 80, 400);
    $settings['logo_height'] = cnc_participants_slider_clamp_int($settings['logo_height'], 40, 240);
    $settings['logo_gap'] = cnc_participants_slider_clamp_int($settings['logo_gap'], 10, 220);
    $settings['section_padding'] = cnc_participants_slider_clamp_int($settings['section_padding'], 0, 200);
    $settings['max_items'] = cnc_participants_slider_clamp_int($settings['max_items'], 0, 200);
    $settings['pause_on_hover'] = !empty($settings['pause_on_hover']) ? 1 : 0;
    $settings['grayscale'] = !empty($settings['grayscale']) ? 1 : 0;

    return $settings;
}

function cnc_sanitize_participants_slider_settings($input) {
    $defaults = cnc_get_participants_slider_defaults();
    $input = is_array($input) ? $input : [];

    return [
        'title' => sanitize_text_field($input['title'] ?? $defaults['title']),
        'subtitle' => sanitize_text_field($input['subtitle'] ?? $defaults['subtitle']),
        'scroll_duration' => cnc_participants_slider_clamp_int($input['scroll_duration'] ?? $defaults['scroll_duration'], 5, 180),
        'logo_width' => cnc_participants_slider_clamp_int($input['logo_width'] ?? $defaults['logo_width'], 80, 400),
        'logo_height' => cnc_participants_slider_clamp_int($input['logo_height'] ?? $defaults['logo_height'], 40, 240),
        'logo_gap' => cnc_participants_slider_clamp_int($input['logo_gap'] ?? $defaults['logo_gap'], 10, 220),
        'section_padding' => cnc_participants_slider_clamp_int($input['section_padding'] ?? $defaults['section_padding'], 0, 200),
        'max_items' => cnc_participants_slider_clamp_int($input['max_items'] ?? $defaults['max_items'], 0, 200),
        'pause_on_hover' => !empty($input['pause_on_hover']) ? 1 : 0,
        'grayscale' => !empty($input['grayscale']) ? 1 : 0,
    ];
}

function cnc_register_participants_slider_settings() {
    register_setting('cnc_participants_slider_group', CNC_PARTICIPANTS_SLIDER_OPTION, [
        'type' => 'array',
        'sanitize_callback' => 'cnc_sanitize_participants_slider_settings',
        'default' => cnc_get_participants_slider_defaults(),
    ]);

    add_settings_section(
        'cnc_participants_slider_main',
        'Slider Controls',
        function() {
            echo '<p>Control the Participants slider from admin. Set max items to 0 to show all exhibitors with logos.</p>';
        },
        'cnc-participants-slider'
    );

    add_settings_field('title', 'Title', 'cnc_render_participants_slider_text_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'title',
        'description' => 'Leave empty to hide the title.',
    ]);

    add_settings_field('subtitle', 'Subtitle', 'cnc_render_participants_slider_text_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'subtitle',
        'description' => 'Leave empty to hide the subtitle.',
    ]);

    add_settings_field('scroll_duration', 'Scroll Duration', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'scroll_duration',
        'min' => 5,
        'max' => 180,
        'suffix' => 'seconds',
        'description' => 'Lower value means faster scrolling.',
    ]);

    add_settings_field('logo_width', 'Logo Width', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'logo_width',
        'min' => 80,
        'max' => 400,
        'suffix' => 'px',
    ]);

    add_settings_field('logo_height', 'Logo Height', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'logo_height',
        'min' => 40,
        'max' => 240,
        'suffix' => 'px',
    ]);

    add_settings_field('logo_gap', 'Logo Gap', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'logo_gap',
        'min' => 10,
        'max' => 220,
        'suffix' => 'px',
    ]);

    add_settings_field('section_padding', 'Section Padding', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'section_padding',
        'min' => 0,
        'max' => 200,
        'suffix' => 'px',
    ]);

    add_settings_field('max_items', 'Max Items', 'cnc_render_participants_slider_number_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'max_items',
        'min' => 0,
        'max' => 200,
        'description' => '0 = show all available exhibitors.',
    ]);

    add_settings_field('pause_on_hover', 'Pause On Hover', 'cnc_render_participants_slider_checkbox_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'pause_on_hover',
        'label' => 'Pause animation when mouse hovers over the slider',
    ]);

    add_settings_field('grayscale', 'Logo Effect', 'cnc_render_participants_slider_checkbox_field', 'cnc-participants-slider', 'cnc_participants_slider_main', [
        'key' => 'grayscale',
        'label' => 'Show logos in grayscale until hover',
    ]);
}
add_action('admin_init', 'cnc_register_participants_slider_settings');

function cnc_render_participants_slider_text_field($args) {
    $settings = cnc_get_participants_slider_settings();
    $key = $args['key'];
    $description = $args['description'] ?? '';
    ?>
    <input
        type="text"
        class="regular-text"
        name="<?php echo esc_attr(CNC_PARTICIPANTS_SLIDER_OPTION); ?>[<?php echo esc_attr($key); ?>]"
        value="<?php echo esc_attr($settings[$key] ?? ''); ?>"
    >
    <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
    <?php endif; ?>
    <?php
}

function cnc_render_participants_slider_number_field($args) {
    $settings = cnc_get_participants_slider_settings();
    $key = $args['key'];
    $min = isset($args['min']) ? (int) $args['min'] : 0;
    $max = isset($args['max']) ? (int) $args['max'] : 9999;
    $suffix = $args['suffix'] ?? '';
    $description = $args['description'] ?? '';
    ?>
    <input
        type="number"
        class="small-text"
        min="<?php echo esc_attr($min); ?>"
        max="<?php echo esc_attr($max); ?>"
        name="<?php echo esc_attr(CNC_PARTICIPANTS_SLIDER_OPTION); ?>[<?php echo esc_attr($key); ?>]"
        value="<?php echo esc_attr($settings[$key] ?? 0); ?>"
    >
    <?php if ($suffix) : ?>
        <span><?php echo esc_html($suffix); ?></span>
    <?php endif; ?>
    <?php if ($description) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
    <?php endif; ?>
    <?php
}

function cnc_render_participants_slider_checkbox_field($args) {
    $settings = cnc_get_participants_slider_settings();
    $key = $args['key'];
    $label = $args['label'] ?? '';
    ?>
    <label>
        <input
            type="checkbox"
            name="<?php echo esc_attr(CNC_PARTICIPANTS_SLIDER_OPTION); ?>[<?php echo esc_attr($key); ?>]"
            value="1"
            <?php checked(!empty($settings[$key])); ?>
        >
        <?php echo esc_html($label); ?>
    </label>
    <?php
}

function cnc_register_participants_slider_admin_page() {
    add_submenu_page(
        'themes.php',
        'Participants Slider',
        'Participants Slider',
        'manage_options',
        'cnc-participants-slider',
        'cnc_render_participants_slider_settings_page'
    );
}
add_action('admin_menu', 'cnc_register_participants_slider_admin_page');

function cnc_render_participants_slider_settings_page() {
    ?>
    <div class="wrap">
        <h1>Participants Slider Settings</h1>
        <p>Use this tool to control slider speed, logo sizing, spacing, and behavior from the WordPress dashboard.</p>
        <p>Shortcode: <code>[cnc_participants_slider]</code></p>

        <form method="post" action="options.php">
            <?php
            settings_fields('cnc_participants_slider_group');
            do_settings_sections('cnc-participants-slider');
            submit_button('Save Slider Settings');
            ?>
        </form>
    </div>
    <?php
}
