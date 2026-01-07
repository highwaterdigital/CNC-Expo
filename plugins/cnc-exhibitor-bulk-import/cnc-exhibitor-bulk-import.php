<?php
/**
 * Plugin Name: CNC Exhibitor Bulk Importer
 * Description: Bulk import CNC exhibitors and logos from /uploads/cnc-exhibitors via WP-CLI.
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
            $source_dir = $importer->get_source_dir();

            if (is_wp_error($source_dir)) {
                WP_CLI::error($source_dir->get_error_message());
            }

            $files = $importer->scan_logo_files($source_dir);
            if (empty($files)) {
                WP_CLI::warning('No logo files found to import.');
                return;
            }

            $imported = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($files as $file_path) {
                $slug = $importer->slug_from_filename($file_path);
                $title = $importer->title_from_slug($slug);

                $existing = $importer->find_existing_exhibitor($slug);
                if ($existing) {
                    WP_CLI::log('Skipped: ' . $title . ' (already exists)');
                    $skipped++;
                    continue;
                }

                $post_id = $importer->create_exhibitor_post($title, $slug);
                if (is_wp_error($post_id)) {
                    WP_CLI::warning('Error creating exhibitor for ' . $title . ': ' . $post_id->get_error_message());
                    $errors++;
                    continue;
                }

                $attachment_id = $importer->get_or_create_attachment($file_path, $post_id, $title);
                if (is_wp_error($attachment_id)) {
                    WP_CLI::warning('Error attaching logo for ' . $title . ': ' . $attachment_id->get_error_message());
                    $errors++;
                    continue;
                }

                set_post_thumbnail($post_id, $attachment_id);
                update_post_meta($post_id, CNC_Exhibitor_Bulk_Importer::META_IMPORT_SOURCE, 'bulk_script');
                update_post_meta($post_id, CNC_Exhibitor_Bulk_Importer::META_LOGO_ATTACHED, 'yes');

                WP_CLI::log('Imported: ' . $title);
                $imported++;
            }

            if ($errors > 0) {
                WP_CLI::warning('Bulk import completed with errors: ' . $imported . ' new, ' . $skipped . ' skipped, ' . $errors . ' errors');
                return;
            }

            WP_CLI::success('Bulk import completed: ' . $imported . ' new, ' . $skipped . ' skipped');
        }
    }

    WP_CLI::add_command('cnc', 'CNC_Exhibitor_Import_Command');
}
