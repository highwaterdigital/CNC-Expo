<?php
/**
 * Component: Participants Logo Slider
 * Description: Infinite marquee slider for participant logos (Dynamic from Exhibitors CPT)
 * Usage: [cnc_participants_slider]
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

ob_start();

// Query Registered Exhibitors with Logos
$args = array(
    'post_type'      => 'cnc_exhibitor',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
    'meta_query'     => array(
        array(
            'key'     => '_thumbnail_id',
            'compare' => 'EXISTS'
        )
    )
);

$query = new WP_Query($args);
$exhibitors = $query->posts;

// Fallback to static images if no dynamic exhibitors found (for development/demo)
if (empty($exhibitors)) {
    $img_path = get_stylesheet_directory_uri() . '/assets/CNC Participants/';
    $static_logos = [
        'aeronet.png', 'beldon.png', 'claron.png', 'ctrls.png', 
        'gtpl.png', 'kp technologies.png', 'radius.png', 
        'RVR.png', 'smartplay.png', 'tfiber.png', 'well aegis.png'
    ];
}
?>
<style>
    :root {
        --cnc-logo-width: 180px;
        --cnc-logo-height: 100px;
        --cnc-logo-gap: 60px;
        --cnc-scroll-duration: 30s;
    }

    .cnc-participants-section {
        padding: 60px 0;
        background: #fff;
        overflow: hidden;
        position: relative;
    }

    .cnc-participants-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .cnc-participants-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .cnc-participants-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--cnc-purple, #5E3A8E);
        margin-bottom: 10px;
    }

    .cnc-participants-subtitle {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        color: #666;
    }

    /* Slider Container */
    .cnc-logo-slider {
        display: flex;
        overflow: hidden;
        position: relative;
        width: 100%;
        mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }

    /* The Track */
    .cnc-logo-track {
        display: flex;
        gap: var(--cnc-logo-gap);
        width: max-content;
        animation: cncMarquee var(--cnc-scroll-duration) linear infinite;
    }

    /* Individual Logo Item */
    .cnc-logo-item {
        width: var(--cnc-logo-width);
        height: var(--cnc-logo-height);
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        transition: transform 0.3s ease;
        /* Optional: Add subtle shadow or border if needed */
        /* box-shadow: 0 4px 15px rgba(0,0,0,0.05); */
    }

    .cnc-logo-item:hover {
        transform: scale(1.05);
    }

    .cnc-logo-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .cnc-logo-item:hover img {
        filter: grayscale(0%);
        opacity: 1;
    }

    /* Animation */
    @keyframes cncMarquee {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(calc(-50% - (var(--cnc-logo-gap) / 2)));
        }
    }

    /* Pause on Hover */
    .cnc-logo-slider:hover .cnc-logo-track {
        animation-play-state: paused;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        :root {
            --cnc-logo-width: 140px;
            --cnc-logo-height: 80px;
            --cnc-logo-gap: 30px;
        }
        
        .cnc-participants-title {
            font-size: 2rem;
        }
    }
</style>

<div class="cnc-participants-section">
    <div class="cnc-participants-container">
        <div class="cnc-participants-header">
            <h2 class="cnc-participants-title">Our Participants</h2>
            <p class="cnc-participants-subtitle">Leading companies trusting our platform</p>
        </div>

        <div class="cnc-logo-slider">
            <div class="cnc-logo-track">
                <?php 
                // Loop twice to create the seamless infinite effect
                for ($i = 0; $i < 2; $i++) : 
                    
                    if (!empty($exhibitors)) {
                        // Dynamic Loop
                        foreach ($exhibitors as $exhibitor) : 
                            $logo_url = get_the_post_thumbnail_url($exhibitor->ID, 'medium');
                            $alt = get_the_title($exhibitor->ID);
                            $link = get_permalink($exhibitor->ID);
                    ?>
                        <a href="<?php echo esc_url($link); ?>" class="cnc-logo-item" title="<?php echo esc_attr($alt); ?>">
                            <img src="<?php echo esc_url($logo_url); ?>" 
                                 alt="<?php echo esc_attr($alt); ?>" 
                                 loading="lazy"
                                 width="180" 
                                 height="100">
                        </a>
                    <?php 
                        endforeach;
                    } else {
                        // Static Fallback Loop
                        foreach ($static_logos as $logo) : 
                            $alt = ucwords(str_replace(['.png', '-', '_'], ['', ' ', ' '], $logo));
                    ?>
                        <div class="cnc-logo-item">
                            <img src="<?php echo esc_url($img_path . $logo); ?>" 
                                 alt="<?php echo esc_attr($alt); ?>" 
                                 loading="lazy"
                                 width="180" 
                                 height="100">
                        </div>
                    <?php 
                        endforeach;
                    }

                endfor; 
                ?>
            </div>
        </div>
    </div>
</div>
<?php
return ob_get_clean();
?>
