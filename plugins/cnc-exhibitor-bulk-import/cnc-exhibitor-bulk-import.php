<?php
/**
 * Plugin Name: CNC Exhibitor Bulk Importer
 * Description: Bulk import CNC exhibitors and logos from /uploads/cnc-exhibitors via WP-CLI and admin tools.
 * Version: 1.0.0
 * Author: CNC Expo
 */

if (!defined('ABSPATH')) {
    exit;
}

class CNC_Exhibitor_Bulk_Importer {
    const POST_TYPE = 'cnc_exhibitor';
    const LOGO_SUBDIR = 'cnc-exhibitors';
    const META_IMPORT_SOURCE = '_exhibitor_import_source';
    const META_LOGO_ATTACHED = '_exhibitor_logo_attached';

    /**
     * @var string[]
     */
    private $allowed_extensions = array('png', 'jpg', 'jpeg', 'webp');

    /**
     * @return array
     */
    public function run_import() {
        $result = array(
            'imported' => 0,
            'skipped' => 0,
            'errors' => 0,
            'items' => array(),
            'error_message' => '',
            'files_count' => 0,
            'no_files' => false,
        );

        $source_dir = $this->get_source_dir();
        if (is_wp_error($source_dir)) {
            $result['error_message'] = $source_dir->get_error_message();
            return $result;
        }

        $files = $this->scan_logo_files($source_dir);
        $result['files_count'] = count($files);

        if (empty($files)) {
            $result['no_files'] = true;
            return $result;
        }

        foreach ($files as $file_path) {
            $slug = $this->slug_from_filename($file_path);
            $title = $this->title_from_slug($slug);

            $existing = $this->find_existing_exhibitor($slug);
            if ($existing) {
                $result['items'][] = array(
                    'type' => 'skipped',
                    'message' => 'Skipped: ' . $title . ' (already exists)',
                );
                $result['skipped']++;
                continue;
            }

            $post_id = $this->create_exhibitor_post($title, $slug);
            if (is_wp_error($post_id)) {
                $result['items'][] = array(
                    'type' => 'error',
                    'message' => 'Error creating exhibitor for ' . $title . ': ' . $post_id->get_error_message(),
                );
                $result['errors']++;
                continue;
            }

            $attachment_id = $this->get_or_create_attachment($file_path, $post_id, $title);
            if (is_wp_error($attachment_id)) {
                $result['items'][] = array(
                    'type' => 'error',
                    'message' => 'Error attaching logo for ' . $title . ': ' . $attachment_id->get_error_message(),
                );
                $result['errors']++;
                continue;
            }

            set_post_thumbnail($post_id, $attachment_id);
            update_post_meta($post_id, self::META_IMPORT_SOURCE, 'bulk_script');
            update_post_meta($post_id, self::META_LOGO_ATTACHED, 'yes');

            $result['items'][] = array(
                'type' => 'imported',
                'message' => 'Imported: ' . $title,
            );
            $result['imported']++;
        }

        return $result;
    }

    /**
     * @return string|WP_Error
     */
    public function get_source_dir() {
        $upload_dir = wp_get_upload_dir();
        if (empty($upload_dir['basedir'])) {
            return new WP_Error('cnc_upload_dir_missing', 'Uploads base directory is not available.');
        }

        $source_dir = trailingslashit($upload_dir['basedir']) . self::LOGO_SUBDIR;
        if (!is_dir($source_dir)) {
            return new WP_Error('cnc_source_dir_missing', 'Source directory not found: ' . $source_dir);
        }

        return $source_dir;
    }

    /**
     * @param string $source_dir
     * @return string[]
     */
    public function scan_logo_files($source_dir) {
        $files = array();

        $iterator = new DirectoryIterator($source_dir);
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isFile()) {
                continue;
            }

            $extension = strtolower($fileinfo->getExtension());
            if (!in_array($extension, $this->allowed_extensions, true)) {
                continue;
            }

            $files[] = $fileinfo->getPathname();
        }

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        return $files;
    }

    /**
     * @param string $file_path
     * @return string
     */
    public function slug_from_filename($file_path) {
        $slug = pathinfo($file_path, PATHINFO_FILENAME);
        return sanitize_title($slug);
    }

    /**
     * @param string $slug
     * @return string
     */
    public function title_from_slug($slug) {
        $title = str_replace('-', ' ', $slug);
        return ucwords($title);
    }

    /**
     * @param string $slug
     * @return WP_Post|null
     */
    public function find_existing_exhibitor($slug) {
        return get_page_by_path($slug, OBJECT, self::POST_TYPE);
    }

    /**
     * @param string $title
     * @param string $slug
     * @return int|WP_Error
     */
    public function create_exhibitor_post($title, $slug) {
        $post_id = wp_insert_post(
            array(
                'post_type' => self::POST_TYPE,
                'post_title' => $title,
                'post_name' => $slug,
                'post_status' => 'publish',
            ),
            true
        );

        return $post_id;
    }

    /**
     * @param string $file_path
     * @param int $post_id
     * @param string $title
     * @return int|WP_Error
     */
    public function get_or_create_attachment($file_path, $post_id, $title) {
        $relative_path = $this->build_relative_path($file_path);
        $existing_id = $this->find_attachment_by_relative_path($relative_path);

        if ($existing_id) {
            $current_parent = (int) get_post_field('post_parent', $existing_id);
            if ($current_parent !== (int) $post_id) {
                wp_update_post(
                    array(
                        'ID' => $existing_id,
                        'post_parent' => $post_id,
                    )
                );
            }

            return $existing_id;
        }

        if (!is_readable($file_path)) {
            return new WP_Error('cnc_file_unreadable', 'File is not readable: ' . $file_path);
        }

        $filetype = wp_check_filetype($file_path);
        if (empty($filetype['type'])) {
            return new WP_Error('cnc_invalid_file_type', 'Invalid file type: ' . $file_path);
        }

        $upload_dir = wp_get_upload_dir();
        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => $title,
            'post_status' => 'inherit',
            'post_parent' => $post_id,
            'guid' => trailingslashit($upload_dir['url']) . $relative_path,
        );

        $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id, true);
        if (is_wp_error($attachment_id)) {
            return $attachment_id;
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
        if (!empty($metadata)) {
            wp_update_attachment_metadata($attachment_id, $metadata);
        }

        return $attachment_id;
    }

    /**
     * @param string $relative_path
     * @return int
     */
    private function find_attachment_by_relative_path($relative_path) {
        $attachments = get_posts(
            array(
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => 1,
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => '_wp_attached_file',
                        'value' => $relative_path,
                        'compare' => '=',
                    ),
                ),
            )
        );

        if (empty($attachments)) {
            return 0;
        }

        return (int) $attachments[0];
    }

    /**
     * @param string $file_path
     * @return string
     */
    private function build_relative_path($file_path) {
        return self::LOGO_SUBDIR . '/' . basename($file_path);
    }
}

if (defined('WP_CLI') && WP_CLI) {
    /**
     * WP-CLI command to bulk import CNC exhibitors.
     */
    class CNC_Exhibitor_Import_Command {
        /**
         * Bulk import exhibitors from /uploads/cnc-exhibitors.
         *
         * ## EXAMPLES
         *
         *     wp cnc import_exhibitors
         */
        public function import_exhibitors() {
            $importer = new CNC_Exhibitor_Bulk_Importer();
            $result = $importer->run_import();

            if (!empty($result['error_message'])) {
                WP_CLI::error($result['error_message']);
            }

            if (!empty($result['no_files'])) {
                WP_CLI::warning('No logo files found to import.');
                return;
            }

            foreach ($result['items'] as $item) {
                if ($item['type'] === 'error') {
                    WP_CLI::warning($item['message']);
                    continue;
                }
                WP_CLI::log($item['message']);
            }

            if ($result['errors'] > 0) {
                WP_CLI::warning('Bulk import completed with errors: ' . $result['imported'] . ' new, ' . $result['skipped'] . ' skipped, ' . $result['errors'] . ' errors');
                return;
            }

            WP_CLI::success('Bulk import completed: ' . $result['imported'] . ' new, ' . $result['skipped'] . ' skipped');
        }
    }

    WP_CLI::add_command('cnc', 'CNC_Exhibitor_Import_Command');
}

if (is_admin()) {
    /**
     * Register Exhibitors admin submenu for bulk import.
     */
    function cnc_exhibitor_bulk_import_register_menu() {
        add_submenu_page(
            'edit.php?post_type=cnc_exhibitor',
            'Exhibitor Bulk Import',
            'Bulk Import',
            'manage_options',
            'cnc-exhibitor-bulk-import',
            'cnc_render_exhibitor_bulk_import_page'
        );
    }
    add_action('admin_menu', 'cnc_exhibitor_bulk_import_register_menu');

    /**
     * Render the bulk import admin page.
     */
    function cnc_render_exhibitor_bulk_import_page() {
        $importer = new CNC_Exhibitor_Bulk_Importer();
        $result = null;

        if (isset($_POST['cnc_bulk_import'])) {
            check_admin_referer('cnc_bulk_import_action');
            $result = $importer->run_import();
        }

        $source_dir = $importer->get_source_dir();
        $files = array();
        if (!is_wp_error($source_dir)) {
            $files = $importer->scan_logo_files($source_dir);
        }
        ?>
        <div class="wrap">
            <h1>Exhibitor Bulk Import</h1>
            <p>Import exhibitors and logos from the fixed uploads directory. This tool is safe and idempotent.</p>

            <?php if (is_wp_error($source_dir)) : ?>
                <div class="notice notice-error">
                    <p><?php echo esc_html($source_dir->get_error_message()); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($result)) : ?>
                <?php if (!empty($result['error_message'])) : ?>
                    <div class="notice notice-error">
                        <p><?php echo esc_html($result['error_message']); ?></p>
                    </div>
                <?php elseif (!empty($result['no_files'])) : ?>
                    <div class="notice notice-warning">
                        <p>No logo files found to import.</p>
                    </div>
                <?php else : ?>
                    <?php
                    $summary_class = $result['errors'] > 0 ? 'notice-warning' : 'notice-success';
                    $summary_text = 'Bulk import completed: ' . $result['imported'] . ' new, ' . $result['skipped'] . ' skipped';
                    if ($result['errors'] > 0) {
                        $summary_text .= ', ' . $result['errors'] . ' errors';
                    }
                    ?>
                    <div class="notice <?php echo esc_attr($summary_class); ?>">
                        <p><?php echo esc_html($summary_text); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card" style="max-width: 700px; padding: 20px; margin-top: 20px;">
                <h2>Source Directory</h2>
                <p><code><?php echo esc_html(is_wp_error($source_dir) ? 'N/A' : $source_dir); ?></code></p>
                <p>Detected logo files: <strong><?php echo esc_html((string) count($files)); ?></strong></p>

                <form method="post">
                    <?php wp_nonce_field('cnc_bulk_import_action'); ?>
                    <input type="submit" name="cnc_bulk_import" class="button button-primary button-hero" value="Run Bulk Import" <?php echo is_wp_error($source_dir) ? 'disabled' : ''; ?>>
                </form>
            </div>

            <?php if (!empty($result['items'])) : ?>
                <h2>Import Log</h2>
                <ul style="list-style: disc; margin-left: 20px;">
                    <?php foreach ($result['items'] as $item) : ?>
                        <li><?php echo esc_html($item['message']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
    }
}
