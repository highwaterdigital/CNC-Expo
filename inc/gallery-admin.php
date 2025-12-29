<?php
// BOM Fix: Removed invisible character
if (!defined('ABSPATH')) {
    exit;
}

function cnc_register_gallery_admin_tool() {
    add_submenu_page(
        'cnc-visitor-registration',
        'Gallery Manager',
        'Gallery Manager',
        'manage_options',
        'cnc-gallery-manager',
        'cnc_render_gallery_admin_tool'
    );
}
add_action('admin_menu', 'cnc_register_gallery_admin_tool');

function cnc_get_gallery_shortcode($id) {
    return sprintf('[cnc_gallery id="%d"]', absint($id));
}

function cnc_render_gallery_admin_tool() {
    $galleries = get_posts([
        'post_type' => 'cnc_gallery',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
    ?>
    <div class="wrap">
        <h1>Gallery Manager</h1>
        <p>Create or manage gallery albums and use the shortcodes below to display them on any page.</p>
        <p>
            <a class="page-title-action" href="<?php echo admin_url('post-new.php?post_type=cnc_gallery'); ?>">Add New Gallery</a>
        </p>
        <h2>Available Shortcodes</h2>
        <p><code>[cnc_gallery id="123"]</code> - renders a single gallery album.</p>
        <p><code>[cnc_gallery_grid]</code> - renders the full archive grid.</p>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($galleries) : foreach ($galleries as $gallery) : ?>
                    <tr>
                        <td><?php echo esc_html($gallery->ID); ?></td>
                        <td><?php echo esc_html(get_the_title($gallery)); ?></td>
                        <td><code><?php echo esc_html(cnc_get_gallery_shortcode($gallery->ID)); ?></code></td>
                        <td>
                            <a href="<?php echo get_edit_post_link($gallery->ID); ?>">Edit</a> |
                            <a target="_blank" href="<?php echo get_permalink($gallery); ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; else : ?>
                    <tr><td colspan="4">No galleries found. Use the button above to add one.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
