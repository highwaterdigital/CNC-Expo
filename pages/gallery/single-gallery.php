<?php
/**
 * Template Name: Single Gallery
 */
get_header();
?>
<div class="cnc-container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="text-align:center; margin-bottom:10px;"><?php the_title(); ?></h1>
    <div style="text-align:center; margin-bottom:40px; color:#666;"><?php the_excerpt(); ?></div>
    
    <?php 
    // Render the single gallery component
    $atts = ['id' => get_the_ID()];
    include get_stylesheet_directory() . '/components/gallery/single.php'; 
    ?>
</div>
<?php
get_footer();
