<?php
/**
 * Component: Gallery Grid
 * Displays a filterable gallery with lightbox.
 * 
 * Attributes:
 * - year: Filter by specific year (slug)
 * - limit: Number of items to show (-1 for all)
 */

$year_filter = isset($atts['year']) ? $atts['year'] : '';
$limit = isset($atts['limit']) ? intval($atts['limit']) : -1;

// Get all years
$years = get_terms([
    'taxonomy' => 'cnc_gallery_year',
    'hide_empty' => true,
    'orderby' => 'name',
    'order' => 'DESC'
]);

// Build Query
$args = [
    'post_type' => 'cnc_gallery_item',
    'posts_per_page' => $limit,
    'orderby' => 'date',
    'order' => 'DESC'
];

if ($year_filter) {
    $args['tax_query'] = [[
        'taxonomy' => 'cnc_gallery_year',
        'field' => 'slug',
        'terms' => $year_filter
    ]];
}

$query = new WP_Query($args);
?>
<style>
.cnc-gallery-wrapper {
    padding: 40px 0;
}

/* Filters */
.cnc-gallery-filters {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.cnc-filter-btn {
    background: transparent;
    border: 2px solid #eee;
    padding: 8px 24px;
    border-radius: 30px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.cnc-filter-btn:hover,
.cnc-filter-btn.active {
    background: var(--cnc-magenta, #C6308C);
    border-color: var(--cnc-magenta, #C6308C);
    color: #fff;
}

/* Grid */
.cnc-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.cnc-gallery-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 4/3;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.cnc-gallery-item:hover {
    transform: translateY(-5px);
}

.cnc-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.cnc-gallery-item:hover img {
    transform: scale(1.1);
}

.cnc-gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cnc-gallery-item:hover .cnc-gallery-overlay {
    opacity: 1;
}

.cnc-play-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--cnc-magenta, #C6308C);
}

/* Lightbox */
.cnc-lightbox {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.95);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cnc-lightbox.open {
    display: flex;
    opacity: 1;
}

.cnc-lightbox-content {
    max-width: 90vw;
    max-height: 90vh;
    position: relative;
}

.cnc-lightbox-content img {
    max-width: 100%;
    max-height: 90vh;
    border-radius: 4px;
    box-shadow: 0 0 50px rgba(0,0,0,0.5);
}

.cnc-lightbox-content iframe {
    width: 80vw;
    height: 45vw;
    max-width: 1200px;
    max-height: 675px;
    border-radius: 4px;
}

.cnc-lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #fff;
    font-size: 40px;
    cursor: pointer;
    z-index: 100000;
    line-height: 1;
}

.cnc-lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    color: #fff;
    font-size: 40px;
    cursor: pointer;
    padding: 20px;
    background: rgba(0,0,0,0.3);
    border-radius: 50%;
    transition: background 0.3s;
    user-select: none;
}

.cnc-lightbox-nav:hover {
    background: rgba(255,255,255,0.2);
}

.cnc-prev { left: 20px; }
.cnc-next { right: 20px; }

@media (max-width: 768px) {
    .cnc-gallery-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .cnc-lightbox-nav {
        font-size: 24px;
        padding: 10px;
    }
}
</style>

<div class="cnc-gallery-wrapper">
    <?php if (!$year_filter && !empty($years)) : ?>
        <div class="cnc-gallery-filters">
            <button class="cnc-filter-btn active" data-filter="all">All</button>
            <?php foreach ($years as $year) : ?>
                <button class="cnc-filter-btn" data-filter="<?php echo esc_attr($year->slug); ?>">
                    <?php echo esc_html($year->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="cnc-gallery-grid">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); 
            $video_url = get_post_meta(get_the_ID(), 'cnc_gallery_video_url', true);
            $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $terms = get_the_terms(get_the_ID(), 'cnc_gallery_year');
            $term_slugs = $terms ? implode(' ', array_map(function($t) { return $t->slug; }, $terms)) : '';
            
            // Extract YouTube ID if video
            $video_id = '';
            if ($video_url) {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches);
                $video_id = isset($matches[1]) ? $matches[1] : '';
            }
        ?>
            <div class="cnc-gallery-item" 
                 data-year="<?php echo esc_attr($term_slugs); ?>"
                 data-src="<?php echo esc_url($image_url); ?>"
                 data-video="<?php echo esc_attr($video_id); ?>"
                 data-type="<?php echo $video_id ? 'video' : 'image'; ?>">
                
                <?php the_post_thumbnail('medium_large'); ?>
                
                <div class="cnc-gallery-overlay">
                    <?php if ($video_id) : ?>
                        <div class="cnc-play-icon">▶</div>
                    <?php else : ?>
                        <div class="cnc-play-icon" style="font-size: 24px;">🔍</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; endif; wp_reset_postdata(); ?>
    </div>
</div>

<!-- Lightbox Markup -->
<div class="cnc-lightbox" id="cncLightbox">
    <div class="cnc-lightbox-close">&times;</div>
    <div class="cnc-lightbox-nav cnc-prev">&#10094;</div>
    <div class="cnc-lightbox-nav cnc-next">&#10095;</div>
    <div class="cnc-lightbox-content">
        <!-- Content injected via JS -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('.cnc-filter-btn');
    const items = document.querySelectorAll('.cnc-gallery-item');
    const lightbox = document.getElementById('cncLightbox');
    const content = lightbox.querySelector('.cnc-lightbox-content');
    const closeBtn = lightbox.querySelector('.cnc-lightbox-close');
    const prevBtn = lightbox.querySelector('.cnc-prev');
    const nextBtn = lightbox.querySelector('.cnc-next');
    
    let currentIndex = 0;
    let visibleItems = Array.from(items);

    // Filtering
    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update Active State
            filters.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            
            visibleItems = [];
            items.forEach(item => {
                if (filter === 'all' || item.dataset.year.includes(filter)) {
                    item.style.display = 'block';
                    visibleItems.push(item);
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Lightbox Logic
    function openLightbox(index) {
        currentIndex = index;
        const item = visibleItems[index];
        const type = item.dataset.type;
        
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
        
        if (type === 'video') {
            const videoId = item.dataset.video;
            content.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
        } else {
            const src = item.dataset.src;
            content.innerHTML = `<img src="${src}" alt="Gallery Image">`;
        }
    }

    function closeLightbox() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        content.innerHTML = ''; // Stop video
    }

    function showNext() {
        let nextIndex = currentIndex + 1;
        if (nextIndex >= visibleItems.length) nextIndex = 0;
        openLightbox(nextIndex);
    }

    function showPrev() {
        let prevIndex = currentIndex - 1;
        if (prevIndex < 0) prevIndex = visibleItems.length - 1;
        openLightbox(prevIndex);
    }

    // Event Listeners
    items.forEach(item => {
        item.addEventListener('click', function() {
            const index = visibleItems.indexOf(this);
            if (index !== -1) openLightbox(index);
        });
    });

    closeBtn.addEventListener('click', closeLightbox);
    nextBtn.addEventListener('click', (e) => { e.stopPropagation(); showNext(); });
    prevBtn.addEventListener('click', (e) => { e.stopPropagation(); showPrev(); });
    
    // Close on background click
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
    });

    // Keyboard Nav
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') showNext();
        if (e.key === 'ArrowLeft') showPrev();
    });
});
</script>
<?php return ob_get_clean(); ?>
