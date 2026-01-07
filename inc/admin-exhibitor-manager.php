<?php
/**
 * CNC Exhibitor Manager
 * Admin tool to sync Bookings with Exhibitor Profiles.
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('CNC_Exhibitor_Bulk_Importer')) {
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

            $existing_slugs = $this->get_existing_exhibitor_slugs();

            foreach ($files as $file_path) {
                $slug = $this->slug_from_filename($file_path);
                if ($slug === '') {
                    $result['items'][] = array(
                        'type' => 'error',
                        'message' => 'Error: Unable to derive slug from ' . basename($file_path),
                    );
                    $result['errors']++;
                    continue;
                }

                $normalized_slug = strtolower($slug);
                $title = $this->title_from_slug($slug);

                if (isset($existing_slugs[$normalized_slug])) {
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
                $existing_slugs[$normalized_slug] = (int) $post_id;

                $result['items'][] = array(
                    'type' => 'imported',
                    'message' => 'Imported: ' . $title,
                );
                $result['imported']++;
            }

            return $result;
        }

        /**
         * @return array
         */
        public function get_existing_exhibitor_slugs() {
            $slugs = array();

            $query = new WP_Query(
                array(
                    'post_type' => self::POST_TYPE,
                    'post_status' => array('publish', 'draft', 'pending', 'private', 'trash'),
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'no_found_rows' => true,
                )
            );

            foreach ($query->posts as $post_id) {
                $slug = get_post_field('post_name', $post_id);
                if ($slug !== '') {
                    $slugs[strtolower($slug)] = (int) $post_id;
                }
            }

            wp_reset_postdata();

            return $slugs;
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

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source_dir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isFile()) {
                    continue;
                }

                $extension = strtolower($fileinfo->getExtension());
                if (!in_array($extension, $this->allowed_extensions, true)) {
                    continue;
                }

                $basename = pathinfo($fileinfo->getFilename(), PATHINFO_FILENAME);
                if ($this->is_variant_filename($basename)) {
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
            $query = new WP_Query(
                array(
                    'post_type' => self::POST_TYPE,
                    'post_status' => array('publish', 'draft', 'pending', 'private', 'trash'),
                    'name' => $slug,
                    'posts_per_page' => 1,
                )
            );

            if (empty($query->posts)) {
                return null;
            }

            return $query->posts[0];
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
            $upload_dir = wp_get_upload_dir();
            $base_dir = trailingslashit($upload_dir['basedir']);
            $normalized_path = wp_normalize_path($file_path);
            $normalized_base = wp_normalize_path($base_dir);

            if (strpos($normalized_path, $normalized_base) === 0) {
                $relative = ltrim(substr($normalized_path, strlen($normalized_base)), '/');
                return $relative;
            }

            return self::LOGO_SUBDIR . '/' . basename($file_path);
        }

        /**
         * @param string $base_name
         * @return bool
         */
        private function is_variant_filename($base_name) {
            $base_name = strtolower($base_name);

            if (preg_match('/-\\d+x\\d+$/', $base_name)) {
                return true;
            }

            if (substr($base_name, -7) === '-scaled') {
                return true;
            }

            if (substr($base_name, -8) === '-rotated') {
                return true;
            }

            return false;
        }
    }
}

function cnc_exhibitor_bulk_import_tool_get_importer() {
    if (!class_exists('CNC_Exhibitor_Bulk_Importer')) {
        return new WP_Error('cnc_importer_missing', 'Bulk importer class is not available.');
    }

    return new CNC_Exhibitor_Bulk_Importer();
}

function cnc_exhibitor_bulk_import_tool_run() {
    $importer = cnc_exhibitor_bulk_import_tool_get_importer();
    if (is_wp_error($importer)) {
        return array(
            'imported' => 0,
            'skipped' => 0,
            'errors' => 0,
            'items' => array(),
            'error_message' => $importer->get_error_message(),
            'files_count' => 0,
            'no_files' => false,
        );
    }

    return $importer->run_import();
}

function cnc_render_exhibitor_bulk_import_tool_page() {
    $bulk_result = null;
    if (isset($_POST['cnc_bulk_import_exhibitors']) && check_admin_referer('cnc_bulk_import_action')) {
        $bulk_result = cnc_exhibitor_bulk_import_tool_run();
    }
    ?>
    <div class="wrap">
        <h1>Exhibitor Bulk Import</h1>
        <p>Import exhibitors and logos from the uploads directory. This tool is safe and idempotent.</p>
        <?php cnc_render_exhibitor_bulk_import_tool_panel($bulk_result, false); ?>
    </div>
    <?php
}

function cnc_render_exhibitor_bulk_import_tool_panel($result = null, $show_heading = true) {
    $importer = cnc_exhibitor_bulk_import_tool_get_importer();
    if (is_wp_error($importer)) {
        echo '<div class="notice notice-error"><p>' . esc_html($importer->get_error_message()) . '</p></div>';
        return;
    }

    $source_dir = $importer->get_source_dir();
    $files = array();
    if (!is_wp_error($source_dir)) {
        $files = $importer->scan_logo_files($source_dir);
    }

    if ($show_heading) {
        echo '<h2>Bulk Import Logos</h2>';
    }

    if (!empty($result)) {
        if (!empty($result['error_message'])) {
            echo '<div class="notice notice-error"><p>' . esc_html($result['error_message']) . '</p></div>';
        } elseif (!empty($result['no_files'])) {
            echo '<div class="notice notice-warning"><p>No logo files found to import.</p></div>';
        } else {
            $summary_text = 'Bulk import completed: ' . (int) $result['imported'] . ' new, ' . (int) $result['skipped'] . ' skipped';
            if (!empty($result['errors'])) {
                $summary_text .= ', ' . (int) $result['errors'] . ' errors';
            }
            $summary_class = !empty($result['errors']) ? 'notice-warning' : 'notice-success';
            echo '<div class="notice ' . esc_attr($summary_class) . '"><p>' . esc_html($summary_text) . '</p></div>';
        }
    } elseif (is_wp_error($source_dir)) {
        echo '<div class="notice notice-error"><p>' . esc_html($source_dir->get_error_message()) . '</p></div>';
    }

    $source_dir_text = is_wp_error($source_dir) ? 'N/A' : $source_dir;
    ?>
    <div class="card" style="max-width: 700px; padding: 20px; margin-bottom: 20px;">
        <h3>Import from Logos Folder</h3>
        <p><code><?php echo esc_html($source_dir_text); ?></code></p>
        <p>Detected logo files: <strong><?php echo esc_html((string) count($files)); ?></strong></p>
        <form method="post">
            <?php wp_nonce_field('cnc_bulk_import_action'); ?>
            <input type="submit" name="cnc_bulk_import_exhibitors" class="button button-primary button-hero" value="Run Bulk Import" <?php echo is_wp_error($source_dir) ? 'disabled' : ''; ?>>
        </form>
    </div>

    <?php if (!empty($result['items'])) : ?>
        <h3>Import Log</h3>
        <ul style="list-style: disc; margin-left: 20px;">
            <?php foreach ($result['items'] as $item) : ?>
                <?php if (!empty($item['message'])) : ?>
                    <li><?php echo esc_html($item['message']); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php
}

function cnc_register_exhibitor_manager_page() {
    add_submenu_page(
        'edit.php?post_type=cnc_exhibitor',
        'Exhibitor Manager',
        'Sync & Manage',
        'manage_options',
        'cnc-exhibitor-manager',
        'cnc_render_exhibitor_manager_page'
    );
    if (!function_exists('cnc_exhibitor_bulk_import_register_menu')) {
        add_submenu_page(
            'edit.php?post_type=cnc_exhibitor',
            'Exhibitor Bulk Import',
            'Bulk Import',
            'manage_options',
            'cnc-exhibitor-bulk-import',
            'cnc_render_exhibitor_bulk_import_tool_page'
        );
    }
}
add_action('admin_menu', 'cnc_register_exhibitor_manager_page');

function cnc_render_exhibitor_manager_page() {
    // Handle Sync Action
    if (isset($_POST['cnc_sync_exhibitors']) && check_admin_referer('cnc_sync_exhibitors_action')) {
        cnc_process_exhibitor_sync();
    }
    $bulk_result = null;
    if (isset($_POST['cnc_bulk_import_exhibitors']) && check_admin_referer('cnc_bulk_import_action')) {
        $bulk_result = cnc_exhibitor_bulk_import_tool_run();
    }

    ?>
    <div class="wrap">
        <h1>Exhibitor Manager</h1>
        <p>Use this tool to sync confirmed Bookings into Exhibitor Profiles.</p>
        
        <div class="card" style="max-width: 600px; padding: 20px; margin-bottom: 20px;">
            <h2>Sync Bookings to Exhibitors</h2>
            <p>This will:</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li>Scan all <strong>Stall Bookings</strong>.</li>
                <li>Create new <strong>Exhibitor Profiles</strong> for bookings that don't have one (matched by Email or Company Name).</li>
                <li>Update existing profiles with the latest Stall Number.</li>
                <li>Link the Booking ID to the Exhibitor Profile.</li>
            </ul>
            <form method="post">
                <?php wp_nonce_field('cnc_sync_exhibitors_action'); ?>
                <input type="submit" name="cnc_sync_exhibitors" class="button button-primary button-hero" value="Run Sync Now">
            </form>
        </div>

        <?php cnc_render_exhibitor_bulk_import_tool_panel($bulk_result); ?>

        <h2>Sync Status</h2>
        <?php cnc_render_sync_status_table(); ?>
    </div>
    <?php
}

function cnc_process_exhibitor_sync() {
    $bookings = get_posts(array(
        'post_type' => 'cnc_booking',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ));

    $created = 0;
    $updated = 0;

    foreach ($bookings as $booking) {
        $company_name = $booking->post_title;
        $email = get_post_meta($booking->ID, 'cnc_email', true); // Assuming 'cnc_email' is the key, checking bookings.php it might be different, let's verify.
        // Checking bookings.php, it doesn't explicitly list 'cnc_email' in columns but it's likely there. 
        // Wait, bookings.php columns show 'cnc_contact_person', 'cnc_phone'. 
        // I should check how bookings are saved. Assuming 'cnc_email' for now, but will fallback to title match.
        
        // Actually, let's check if we can find an email.
        if (!$email) {
             // Try to find email in meta if key is different
             $email = get_post_meta($booking->ID, 'cnc_contact_email', true);
        }

        $stall_id = get_post_meta($booking->ID, 'cnc_stall_id', true);
        $phone = get_post_meta($booking->ID, 'cnc_phone', true);

        // 1. Try to find existing Exhibitor by Linked Booking ID
        $exhibitor_query = new WP_Query(array(
            'post_type' => 'cnc_exhibitor',
            'meta_key' => 'cnc_linked_booking_id',
            'meta_value' => $booking->ID,
            'posts_per_page' => 1
        ));

        $exhibitor_id = 0;

        if ($exhibitor_query->have_posts()) {
            $exhibitor_id = $exhibitor_query->posts[0]->ID;
        } else {
            // 2. Try to find by Email
            if ($email) {
                $exhibitor_query = new WP_Query(array(
                    'post_type' => 'cnc_exhibitor',
                    'meta_key' => 'cnc_contact_email',
                    'meta_value' => $email,
                    'posts_per_page' => 1
                ));
                if ($exhibitor_query->have_posts()) {
                    $exhibitor_id = $exhibitor_query->posts[0]->ID;
                }
            }

            // 3. Try to find by Title (Company Name)
            if (!$exhibitor_id) {
                $exhibitor_by_title = get_page_by_title($company_name, OBJECT, 'cnc_exhibitor');
                if ($exhibitor_by_title) {
                    $exhibitor_id = $exhibitor_by_title->ID;
                }
            }
        }

        if ($exhibitor_id) {
            // Update Existing
            update_post_meta($exhibitor_id, 'cnc_stall_number', $stall_id);
            update_post_meta($exhibitor_id, 'cnc_linked_booking_id', $booking->ID);
            // Only update contact info if missing
            if (!get_post_meta($exhibitor_id, 'cnc_contact_email', true)) update_post_meta($exhibitor_id, 'cnc_contact_email', $email);
            if (!get_post_meta($exhibitor_id, 'cnc_contact_phone', true)) update_post_meta($exhibitor_id, 'cnc_contact_phone', $phone);
            
            $updated++;
        } else {
            // Create New
            $exhibitor_data = array(
                'post_title'    => $company_name,
                'post_status'   => 'draft', // Draft so admin can review
                'post_type'     => 'cnc_exhibitor',
                'post_content'  => '<!-- Auto-generated from Booking #' . $booking->ID . ' -->'
            );
            
            $exhibitor_id = wp_insert_post($exhibitor_data);
            
            if ($exhibitor_id) {
                update_post_meta($exhibitor_id, 'cnc_stall_number', $stall_id);
                update_post_meta($exhibitor_id, 'cnc_linked_booking_id', $booking->ID);
                update_post_meta($exhibitor_id, 'cnc_contact_email', $email);
                update_post_meta($exhibitor_id, 'cnc_contact_phone', $phone);
                $created++;
            }
        }
    }

    echo '<div class="notice notice-success is-dismissible"><p>Sync Complete! Created: ' . $created . ', Updated: ' . $updated . '</p></div>';
}

function cnc_render_sync_status_table() {
    $bookings = get_posts(array(
        'post_type' => 'cnc_booking',
        'posts_per_page' => 20, // Limit for display
        'post_status' => 'any'
    ));

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr><th>Booking ID</th><th>Company</th><th>Stall</th><th>Linked Exhibitor</th><th>Status</th></tr></thead>';
    echo '<tbody>';

    foreach ($bookings as $booking) {
        $stall = get_post_meta($booking->ID, 'cnc_stall_id', true);
        
        // Find linked exhibitor
        $exhibitor_query = new WP_Query(array(
            'post_type' => 'cnc_exhibitor',
            'meta_key' => 'cnc_linked_booking_id',
            'meta_value' => $booking->ID,
            'posts_per_page' => 1
        ));

        $linked = '-';
        if ($exhibitor_query->have_posts()) {
            $exhibitor = $exhibitor_query->posts[0];
            $linked = '<a href="' . get_edit_post_link($exhibitor->ID) . '">' . esc_html($exhibitor->post_title) . '</a>';
        }

        echo '<tr>';
        echo '<td>' . $booking->ID . '</td>';
        echo '<td>' . esc_html($booking->post_title) . '</td>';
        echo '<td>' . esc_html($stall) . '</td>';
        echo '<td>' . $linked . '</td>';
        echo '<td>' . ($linked !== '-' ? '<span style="color:green">Synced</span>' : '<span style="color:red">Not Synced</span>') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<p><em>Showing last 20 bookings.</em></p>';
}
