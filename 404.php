<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package cnc_child
 */

get_header();
?>

<main class="site-main" aria-label="404 page">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e('Page not found', 'cnc_child'); ?></h1>
        </header>
        <div class="page-content">
            <p><?php esc_html_e('It looks like nothing was found at this location. Try using the menu to navigate.', 'cnc_child'); ?></p>
        </div>
    </section>
</main>

<?php
get_footer();
?>
