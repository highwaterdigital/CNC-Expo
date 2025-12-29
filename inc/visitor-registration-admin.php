<?php
// BOM Fix: Removed invisible character
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('CNC_VISITOR_REG_OPTION')) {
    define('CNC_VISITOR_REG_OPTION', 'cnc_visitor_registration_status');
}

function cnc_is_visitor_registration_enabled() {
    return 'on' === get_option(CNC_VISITOR_REG_OPTION, 'off');
}

function cnc_update_visitor_registration_status($status) {
    $clean = $status === 'on' ? 'on' : 'off';
    update_option(CNC_VISITOR_REG_OPTION, $clean);
}

function cnc_register_visitor_admin_menu() {
    add_menu_page(
        'Visitor Registration',
        'Visitor Registration',
        'manage_options',
        'cnc-visitor-registration',
        'cnc_render_visitor_registration_admin_page',
        'dashicons-groups',
        58
    );
}
add_action('admin_menu', 'cnc_register_visitor_admin_menu');

add_action('admin_post_cnc_visitor_registration_status', function() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized', 'cnc_child'), 403);
    }
    check_admin_referer('cnc_visitor_registration_status');
    $status = isset($_POST['registration_status']) ? sanitize_text_field($_POST['registration_status']) : 'off';
    cnc_update_visitor_registration_status($status);
    wp_safe_redirect(admin_url('admin.php?page=cnc-visitor-registration&updated=1'));
    exit;
});

function cnc_render_visitor_registration_admin_page() {
    $current = cnc_is_visitor_registration_enabled() ? 'on' : 'off';
    ?>
    <div class="wrap">
        <h1>Visitor Registration Control</h1>
        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Registration status saved.</p>
            </div>
        <?php endif; ?>
        <p>Use this tool to enable/disable the visitor registration form without modifying templates. The shortcode <code>[cnc_visitor_registration_form]</code> always honors this toggle.</p>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" style="max-width:480px;">
            <?php wp_nonce_field('cnc_visitor_registration_status'); ?>
            <input type="hidden" name="action" value="cnc_visitor_registration_status">
            <table class="form-table">
                <tr>
                    <th scope="row">Registration Status</th>
                    <td>
                        <label><input type="radio" name="registration_status" value="on" <?php checked('on', $current); ?>> Enabled</label><br>
                        <label><input type="radio" name="registration_status" value="off" <?php checked('off', $current); ?>> Disabled</label>
                        <p class="description">When disabled the form will render in a greyed-out state and submissions are blocked.</p>
                    </td>
                </tr>
            </table>
            <p class="submit"><button type="submit" class="button button-primary">Save Status</button></p>
        </form>
        <h2>Shortcode</h2>
        <p>Insert the shortcode <code>[cnc_visitor_registration_form]</code> inside any Elementor HTML widget, Gutenberg block, or template.</p>
        <h2>Google Sheet Sync</h2>
        <p>Submitted entries are automatically passed to the Google Sheet synchronization already wired into <code>cnc_register_visitor</code>. Nothing additional is required.</p>
    </div>
    <?php
}
