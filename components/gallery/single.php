<?php
/**
 * Component: Single Gallery Display
 * Displays images and videos from a specific gallery post.
 */

$gallery_id = isset($atts['id']) ? intval($atts['id']) : 0;
if (!$gallery_id) return '<p>Gallery ID not specified.</p>';

$image_ids_str = get_post_meta($gallery_id, 'cnc_gallery_image_ids', true);
$video_urls_str = get_post_meta($gallery_id, 'cnc_gallery_videos', true);

$items = [];

// Process Images
if ($image_ids_str) {
    $ids = explode(',', $image_ids_str);
    foreach ($ids as $id) {
        $url = wp_get_attachment_image_url($id, 'large');
        $thumb = wp_get_attachment_image_url($id, 'medium_large');
        if ($url) {
            $items[] = [
                'type' => 'image',
                'src' => $url,
                'thumb' => $thumb
            ];
        }
    }
}

// Process Videos
if ($video_urls_str) {
    $lines = explode("\n", $video_urls_str);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // Extract ID
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $line, $matches);
        $vid_id = isset($matches[1]) ? $matches[1] : '';
        
        if ($vid_id) {
            $items[] = [
                'type' => 'video',
                'id' => $vid_id,
                'thumb' => "https://img.youtube.com/vi/$vid_id/hqdefault.jpg"
            ];
        }
    }
}

if (empty($items)) return '<p>No items in this gallery.</p>';

$unique_id = 'cnc_gallery_' . uniqid();
?>
<style>
.cnc-gallery-single-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
}
.cnc-gallery-item {
    position: relative;
    aspect-ratio: 16/9;
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.cnc-gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.cnc-gallery-item:hover img {
    transform: scale(1.05);
}
.cnc-gallery-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,0.6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
    pointer-events: none;
}
/* Lightbox Styles (Reused) */
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
.cnc-lightbox.open { display: flex; opacity: 1; }
.cnc-lightbox-content { max-width: 90vw; max-height: 90vh; position: relative; }
.cnc-lightbox-content img { max-width: 100%; max-height: 90vh; border-radius: 4px; }
.cnc-lightbox-content iframe { width: 80vw; height: 45vw; max-width: 1200px; max-height: 675px; border-radius: 4px; }
.cnc-lightbox-close { position: absolute; top: 20px; right: 20px; color: #fff; font-size: 40px; cursor: pointer; z-index: 100000; line-height: 1; }
.cnc-lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); color: #fff; font-size: 40px; cursor: pointer; padding: 20px; background: rgba(0,0,0,0.3); border-radius: 50%; user-select: none; }
.cnc-lightbox-nav:hover { background: rgba(255,255,255,0.2); }
.cnc-prev { left: 20px; }
.cnc-next { right: 20px; }
</style>

<div class="cnc-gallery-single-grid" id="<?php echo esc_attr($unique_id); ?>">
    <?php foreach ($items as $index => $item) : ?>
        <div class="cnc-gallery-item" onclick="cncOpenLightbox('<?php echo esc_js($unique_id); ?>', <?php echo $index; ?>)">
            <img src="<?php echo esc_url($item['thumb']); ?>" alt="Gallery Item">
            <div class="cnc-gallery-icon">
                <?php echo ($item['type'] === 'video') ? '▶' : '🔍'; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
window.cncGalleries = window.cncGalleries || {};
window.cncGalleries['<?php echo esc_js($unique_id); ?>'] = <?php echo json_encode($items); ?>;
</script>

<?php if (!defined('CNC_GALLERY_ASSETS_LOADED')) : define('CNC_GALLERY_ASSETS_LOADED', true); ?>
<!-- Lightbox Markup -->
<div class="cnc-lightbox" id="cncSingleLightbox">
    <div class="cnc-lightbox-close" onclick="cncCloseLightbox()">&times;</div>
    <div class="cnc-lightbox-nav cnc-prev" onclick="cncPrevSlide(event)">&#10094;</div>
    <div class="cnc-lightbox-nav cnc-next" onclick="cncNextSlide(event)">&#10095;</div>
    <div class="cnc-lightbox-content" id="cncLightboxContent"></div>
</div>

<script>
if (typeof cncOpenLightbox === 'undefined') {
    var cncCurrentGalleryId = null;
    var cncCurrentIndex = 0;
    var cncLightbox = document.getElementById('cncSingleLightbox');
    var cncContent = document.getElementById('cncLightboxContent');

    window.cncOpenLightbox = function(galleryId, index) {
        cncCurrentGalleryId = galleryId;
        cncCurrentIndex = index;
        cncUpdateLightbox();
        if (cncLightbox) {
            cncLightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    window.cncCloseLightbox = function() {
        if (cncLightbox) {
            cncLightbox.classList.remove('open');
            document.body.style.overflow = '';
            cncContent.innerHTML = '';
        }
    }

    window.cncUpdateLightbox = function() {
        if (!cncCurrentGalleryId || !window.cncGalleries[cncCurrentGalleryId]) return;
        
        var item = window.cncGalleries[cncCurrentGalleryId][cncCurrentIndex];
        if (item.type === 'video') {
            cncContent.innerHTML = '<iframe src="https://www.youtube.com/embed/' + item.id + '?autoplay=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
        } else {
            cncContent.innerHTML = '<img src="' + item.src + '">';
        }
    }

    window.cncNextSlide = function(e) {
        e.stopPropagation();
        if (!cncCurrentGalleryId) return;
        
        var items = window.cncGalleries[cncCurrentGalleryId];
        cncCurrentIndex++;
        if (cncCurrentIndex >= items.length) cncCurrentIndex = 0;
        cncUpdateLightbox();
    }

    window.cncPrevSlide = function(e) {
        e.stopPropagation();
        if (!cncCurrentGalleryId) return;
        
        var items = window.cncGalleries[cncCurrentGalleryId];
        cncCurrentIndex--;
        if (cncCurrentIndex < 0) cncCurrentIndex = items.length - 1;
        cncUpdateLightbox();
    }

    // Keyboard Nav
    document.addEventListener('keydown', function(e) {
        if (!cncLightbox || !cncLightbox.classList.contains('open')) return;
        if (e.key === 'Escape') cncCloseLightbox();
        if (e.key === 'ArrowRight') cncNextSlide(new Event('click'));
        if (e.key === 'ArrowLeft') cncPrevSlide(new Event('click'));
    });
}
</script>
<?php endif; ?>
