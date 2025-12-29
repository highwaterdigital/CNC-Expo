<?php
/**
 * Component: Exhibitor Grid
 * Displays a grid of exhibitors with search and filter.
 */

ob_start();

$args = array(
    'post_type' => 'cnc_exhibitor',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'post_status' => 'publish'
);

$query = new WP_Query($args);
?>
<style>
.cnc-exhibitor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
    padding: 40px 0;
}

.cnc-exhibitor-card {
    background: #fff;
    border-radius: var(--cnc-radius, 20px);
    box-shadow: var(--cnc-shadow, 0 10px 40px rgba(0,0,0,0.1));
    overflow: hidden;
    transition: var(--cnc-transition, all 0.4s ease);
    display: flex;
    flex-direction: column;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
}

.cnc-exhibitor-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.cnc-exhibitor-logo {
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.cnc-exhibitor-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.cnc-exhibitor-card:hover .cnc-exhibitor-logo img {
    transform: scale(1.05);
}

.cnc-exhibitor-info {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.cnc-exhibitor-stall {
    display: inline-block;
    background: var(--cnc-magenta, #C6308C);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 15px;
    align-self: flex-start;
}

.cnc-exhibitor-title {
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    color: var(--cnc-black, #0D0D0D);
    margin: 0 0 10px 0;
    line-height: 1.4;
}

.cnc-exhibitor-meta {
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 15px;
}

.cnc-exhibitor-link {
    color: var(--cnc-purple, #5E3A8E);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.cnc-exhibitor-link:hover {
    color: var(--cnc-magenta, #C6308C);
}

/* Empty State */
.cnc-no-exhibitors {
    text-align: center;
    padding: 60px;
    background: #f8f9fa;
    border-radius: 20px;
    grid-column: 1 / -1;
}
</style>

<div class="cnc-container">
    <?php if ($query->have_posts()) : ?>
        <div class="cnc-exhibitor-grid">
            <?php while ($query->have_posts()) : $query->the_post(); 
                $stall = get_post_meta(get_the_ID(), 'cnc_stall_number', true);
                $website = get_post_meta(get_the_ID(), 'cnc_website', true);
            ?>
                <article class="cnc-exhibitor-card">
                    <a href="<?php the_permalink(); ?>" class="cnc-exhibitor-card-link" style="text-decoration: none; color: inherit; display: contents;">
                        <div class="cnc-exhibitor-logo">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium'); ?>
                            <?php else : ?>
                                <span style="color: #ccc; font-size: 40px;">🏢</span>
                            <?php endif; ?>
                        </div>
                        <div class="cnc-exhibitor-info">
                            <?php if ($stall) : ?>
                                <span class="cnc-exhibitor-stall">Stall <?php echo esc_html($stall); ?></span>
                            <?php endif; ?>
                            
                            <h3 class="cnc-exhibitor-title"><?php the_title(); ?></h3>
                            
                            <div class="cnc-exhibitor-meta">
                                <span class="cnc-exhibitor-link">View Profile →</span>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="cnc-no-exhibitors">
            <h3>No Exhibitors Found</h3>
            <p>Check back soon for our updated exhibitor list.</p>
        </div>
    <?php endif; wp_reset_postdata(); ?>
</div>
<?php return ob_get_clean(); // Ensure this file can be included in a shortcode function ?>
