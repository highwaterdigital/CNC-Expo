<?php
/**
 * Template Name: CNC Register Page
 * Description: Uses the default header/footer and allows content/shortcodes to render from the builder.
 *
 * @package cnc_child
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
?>
