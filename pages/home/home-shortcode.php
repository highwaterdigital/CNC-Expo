<?php
/**
 * CNC Home Page Shortcode v7.0 - MODULAR ARCHITECTURE
 * Refactored: Split into separate section files for easy editing
 * @package cnc_child
 * @version 7.0.0
 */

if (!defined('ABSPATH')) exit;

ob_start();
?>

<style>
/* CNC Home Page Complete Styles - v7.0 */
:root {
    --cnc-magenta: #C6308C;     /* PRIMARY brand color */
    --cnc-purple: #5E3A8E;      /* Secondary */
    --cnc-blue: #0090D9;        /* CTAs */
    --cnc-teal: #00B4B4;        /* Accents */
    --cnc-black: #0D0D0D;       /* Text */
    --cnc-gold: #FFC107;        /* Highlights */
    --cnc-white: #FFFFFF;
    --gradient-primary: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    --cnc-radius: 5px;
    --cnc-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    --cnc-transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

<?php
// Include all CSS files
$styles_dir = get_stylesheet_directory() . '/pages/home/styles/';
$css_files = ['global.css', 'hero.css', 'sections.css', 'responsive.css'];
foreach ($css_files as $css_file) {
    $file_path = $styles_dir . $css_file;
    if (file_exists($file_path)) {
        include $file_path;
    }
}
?>
</style>

<!-- Main Content Wrapper -->
<div class="cnc-home-wrapper">
    
    <?php 
    // Load all home page sections
    $sections_dir = get_stylesheet_directory() . '/pages/home/sections/';
    $participants_file = get_stylesheet_directory() . '/components/participants/participants-slider.php';
    
    // Section order
    $sections = [
        'hero',
        'brochure',
        'highlights',
        'metrics',
        'about',
        'exhibitors',
        'schedule',
        'venue',
        'testimonials',
        'cta'
    ];
    
    foreach ($sections as $section) {
        $section_file = $sections_dir . $section . '.php';
        if (file_exists($section_file)) {
            include $section_file;
        } elseif (current_user_can('administrator')) {
            echo "<!-- Missing section: {$section}.php -->";
        }

        // Insert participants slider immediately after hero
        if ($section === 'hero' && file_exists($participants_file)) {
            include $participants_file;
        }
    }
    ?>

</div>

<script>
<?php
// Include JavaScript file
$js_file = get_stylesheet_directory() . '/pages/home/scripts/animations.js';
if (file_exists($js_file)) {
    include $js_file;
}
?>
</script>

<?php
$content = ob_get_clean();
echo $content;
