<?php
/**
 * Template Name: Single Exhibitor
 * Description: Custom template for displaying a single exhibitor profile.
 */

get_header();

$stall = get_post_meta(get_the_ID(), 'cnc_stall_number', true);
$website = get_post_meta(get_the_ID(), 'cnc_website', true);
$email = get_post_meta(get_the_ID(), 'cnc_contact_email', true);
$phone = get_post_meta(get_the_ID(), 'cnc_contact_phone', true);
?>

<style>
    /* Inline CSS for Portability */
    .cnc-single-exhibitor {
        padding: 60px 0;
        background: #f8f9fa;
        min-height: 80vh;
        font-family: 'Inter', sans-serif;
    }

    .cnc-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .cnc-profile-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        overflow: hidden;
        display: grid;
        grid-template-columns: 350px 1fr;
    }

    @media (max-width: 900px) {
        .cnc-profile-card {
            grid-template-columns: 1fr;
        }
    }

    /* Sidebar / Logo Area */
    .cnc-profile-sidebar {
        background: #fff;
        border-right: 1px solid #eee;
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .cnc-profile-logo {
        width: 100%;
        max-width: 250px;
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 20px;
    }

    .cnc-profile-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .cnc-stall-badge {
        background: #C6308C;
        color: #fff;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 20px;
        display: inline-block;
    }

    .cnc-contact-list {
        width: 100%;
        text-align: left;
        margin-top: 20px;
    }

    .cnc-contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        color: #555;
        font-size: 14px;
    }

    .cnc-contact-item svg {
        width: 18px;
        height: 18px;
        color: #C6308C;
        flex-shrink: 0;
    }

    .cnc-contact-item a {
        color: #555;
        text-decoration: none;
        transition: color 0.3s;
        word-break: break-all;
    }

    .cnc-contact-item a:hover {
        color: #C6308C;
    }

    /* Main Content Area */
    .cnc-profile-content {
        padding: 50px;
    }

    .cnc-profile-title {
        font-family: 'Poppins', sans-serif;
        font-size: 32px;
        font-weight: 700;
        color: #0D0D0D;
        margin: 0 0 20px 0;
        line-height: 1.2;
    }

    .cnc-profile-description {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
        margin-bottom: 40px;
    }

    .cnc-action-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .cnc-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .cnc-btn-primary {
        background: #C6308C;
        color: #fff;
    }

    .cnc-btn-primary:hover {
        background: #a52673;
        transform: translateY(-2px);
    }

    .cnc-btn-outline {
        border: 2px solid #C6308C;
        color: #C6308C;
    }

    .cnc-btn-outline:hover {
        background: #C6308C;
        color: #fff;
        transform: translateY(-2px);
    }

    .cnc-back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #666;
        text-decoration: none;
        font-weight: 500;
    }

    .cnc-back-link:hover {
        color: #C6308C;
    }
</style>

<div class="cnc-single-exhibitor">
    <div class="cnc-container">
        <a href="<?php echo esc_url(home_url('/exhibitors')); ?>" class="cnc-back-link">← Back to Exhibitors</a>

        <div class="cnc-profile-card">
            <!-- Sidebar -->
            <div class="cnc-profile-sidebar">
                <div class="cnc-profile-logo">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium'); ?>
                    <?php else : ?>
                        <span style="font-size: 60px;">🏢</span>
                    <?php endif; ?>
                </div>

                <?php if ($stall) : ?>
                    <div class="cnc-stall-badge">Stall <?php echo esc_html($stall); ?></div>
                <?php endif; ?>

                <div class="cnc-contact-list">
                    <?php if ($website) : ?>
                        <div class="cnc-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            <a href="<?php echo esc_url($website); ?>" target="_blank">Visit Website</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($email) : ?>
                        <div class="cnc-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ($phone) : ?>
                        <div class="cnc-contact-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content -->
            <div class="cnc-profile-content">
                <h1 class="cnc-profile-title"><?php the_title(); ?></h1>
                
                <div class="cnc-profile-description">
                    <?php if (get_the_content()) : ?>
                        <?php the_content(); ?>
                    <?php else : ?>
                        <p><em>No description available for this exhibitor.</em></p>
                    <?php endif; ?>
                </div>

                <div class="cnc-action-buttons">
                    <?php if ($website) : ?>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" class="cnc-btn cnc-btn-primary">
                            Visit Website
                        </a>
                    <?php endif; ?>
                    
                    <!-- Placeholder for future features -->
                    <!-- <a href="#" class="cnc-btn cnc-btn-outline">Book a Meeting</a> -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
