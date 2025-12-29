<?php
/**
 * CNC Gallery CPT & Meta Boxes
 * Replaces the old 'One Post = One Image' system with 'One Post = One Album'.
 */

if (!defined('ABSPATH')) exit;

function cnc_register_gallery_cpt_v2() {
    $labels = array(
        'name'                  => 'Galleries',
        'singular_name'         => 'Gallery',
        'menu_name'             => 'Galleries',
        'add_new'               => 'Add New Gallery',
        'add_new_item'          => 'Add New Gallery',
        'edit_item'             => 'Edit Gallery',
        'new_item'              => 'New Gallery',
        'view_item'             => 'View Gallery',
        'all_items'             => 'All Galleries',
        'search_items'          => 'Search Galleries',
        'not_found'             => 'No galleries found.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'gallery'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-format-gallery',
        'supports'           => array('title', 'thumbnail', 'excerpt'), // Thumbnail = Cover Image
    );

    register_post_type('cnc_gallery', $args);
    
    // Register Taxonomy for "Display Areas" or "Years"
    register_taxonomy('cnc_gallery_category', 'cnc_gallery', [
        'label' => 'Gallery Categories',
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => true
    ]);
}
add_action('init', 'cnc_register_gallery_cpt_v2');

/**
 * Enqueue Admin Scripts
 */
function cnc_gallery_admin_scripts($hook) {
    global $post;
    // Strict check to ensure we are on the correct post type and $post object exists
    if ( ($hook == 'post-new.php' || $hook == 'post.php') && isset($post) && 'cnc_gallery' === $post->post_type ) {
        wp_enqueue_media();
        wp_enqueue_script('cnc-admin-gallery', get_stylesheet_directory_uri() . '/assets/js/admin-gallery.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'cnc_gallery_admin_scripts');

/**
 * Meta Boxes
 */
function cnc_add_gallery_meta_boxes() {
    add_meta_box(
        'cnc_gallery_images_box',
        'Gallery Images (Bulk Upload)',
        'cnc_render_gallery_images_box',
        'cnc_gallery',
        'normal',
        'high'
    );
    
    add_meta_box(
        'cnc_gallery_videos_box',
        'Gallery Videos',
        'cnc_render_gallery_videos_box',
        'cnc_gallery',
        'normal',
        'default'
    );
    
    add_meta_box(
        'cnc_gallery_shortcode_info',
        'Shortcode Info',
        'cnc_render_gallery_shortcode_info',
        'cnc_gallery',
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'cnc_add_gallery_meta_boxes');

function cnc_render_gallery_images_box($post) {
    $image_ids = get_post_meta($post->ID, 'cnc_gallery_image_ids', true);
    wp_nonce_field('cnc_save_gallery_meta', 'cnc_gallery_nonce');
    ?>
    <div class="cnc-gallery-uploader">
        <div id="cnc_gallery_preview" style="margin-bottom: 15px;">
            <?php 
            if ($image_ids) {
                $ids = explode(',', $image_ids);
                foreach ($ids as $id) {
                    $url = wp_get_attachment_image_url($id, 'thumbnail');
                    if ($url) {
                        echo '<div class="cnc-gallery-preview-item" data-id="' . esc_attr($id) . '" style="display:inline-block; margin:5px; position:relative;">';
                        echo '<img src="' . esc_url($url) . '" style="width:100px; height:100px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">';
                        echo '<span class="cnc-remove-image" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:20px; height:20px; text-align:center; line-height:20px; cursor:pointer;">&times;</span>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        
        <input type="hidden" name="cnc_gallery_image_ids" id="cnc_gallery_image_ids" value="<?php echo esc_attr($image_ids); ?>">
        <button id="cnc_gallery_upload_btn" class="button button-primary button-large">Add Images</button>
        <button id="cnc_gallery_clear_btn" class="button">Clear All</button>
        <p class="description">Click "Add Images" to select multiple photos from the Media Library.</p>
    </div>
    <?php
}

function cnc_render_gallery_videos_box($post) {
    $videos = get_post_meta($post->ID, 'cnc_gallery_videos', true);
    ?>
    <p><strong>YouTube Video URLs (One per line)</strong></p>
    <textarea name="cnc_gallery_videos" rows="5" style="width:100%;"><?php echo esc_textarea($videos); ?></textarea>
    <p class="description">These videos will be displayed in the gallery grid mixed with images.</p>
    <?php
}

function cnc_render_gallery_shortcode_info($post) {
    ?>
    <p><strong>Single Gallery:</strong><br>
    <code>[cnc_gallery id="<?php echo $post->ID; ?>"]</code></p>
    <p><strong>All Galleries Grid:</strong><br>
    <code>[cnc_gallery_grid]</code></p>
    <?php
}

function cnc_save_gallery_meta_v2($post_id) {
    if (!isset($_POST['cnc_gallery_nonce']) || !wp_verify_nonce($_POST['cnc_gallery_nonce'], 'cnc_save_gallery_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['cnc_gallery_image_ids'])) {
        update_post_meta($post_id, 'cnc_gallery_image_ids', sanitize_text_field($_POST['cnc_gallery_image_ids']));
    }
    
    if (isset($_POST['cnc_gallery_videos'])) {
        update_post_meta($post_id, 'cnc_gallery_videos', sanitize_textarea_field($_POST['cnc_gallery_videos']));
    }
}
add_action('save_post', 'cnc_save_gallery_meta_v2');

/**
 * Admin Columns
 */
function cnc_gallery_columns($columns) {
    $new = array();
    foreach($columns as $key => $title) {
        if ($key == 'date') {
            $new['shortcode'] = 'Shortcode';
        }
        $new[$key] = $title;
    }
    return $new;
}
add_filter('manage_cnc_gallery_posts_columns', 'cnc_gallery_columns');

function cnc_gallery_custom_column($column, $post_id) {
    if ($column == 'shortcode') {
        echo '<code>[cnc_gallery id="' . $post_id . '"]</code>';
    }
}
add_action('manage_cnc_gallery_posts_custom_column', 'cnc_gallery_custom_column', 10, 2);
