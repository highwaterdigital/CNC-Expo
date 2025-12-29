<?php
/**
 * Component: Gallery Archive (Grid of Albums)
 * Displays a grid of gallery albums.
 */

$args = [
    'post_type' => 'cnc_gallery',
    'posts_per_page' => -1,
    'post_status' => 'publish'
];
$query = new WP_Query($args);
?>
<style>
.cnc-album-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    padding: 40px 0;
}
.cnc-album-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: block;
}
.cnc-album-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}
.cnc-album-thumb {
    height: 200px;
    background: #eee;
    position: relative;
}
.cnc-album-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.cnc-album-info {
    padding: 20px;
}
.cnc-album-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 5px 0;
    color: #333;
}
.cnc-album-meta {
    font-size: 14px;
    color: #777;
}
</style>

<div class="cnc-album-grid">
    <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); 
        $count = 0;
        $ids = get_post_meta(get_the_ID(), 'cnc_gallery_image_ids', true);
        if ($ids) $count += count(explode(',', $ids));
        
        $vids = get_post_meta(get_the_ID(), 'cnc_gallery_videos', true);
        if ($vids) $count += count(array_filter(explode("\n", $vids)));
    ?>
        <a href="<?php the_permalink(); ?>" class="cnc-album-card">
            <div class="cnc-album-thumb">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium_large'); ?>
                <?php else : ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:40px;">📷</div>
                <?php endif; ?>
            </div>
            <div class="cnc-album-info">
                <h3 class="cnc-album-title"><?php the_title(); ?></h3>
                <div class="cnc-album-meta"><?php echo $count; ?> Items</div>
            </div>
        </a>
    <?php endwhile; endif; wp_reset_postdata(); ?>
</div>

