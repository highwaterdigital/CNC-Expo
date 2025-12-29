<?php
$cnc_testimonials = get_posts([
    'post_type' => 'cnc_testimonial',
    'posts_per_page' => 6,
    'post_status' => 'publish'
]);
?>

<style>
/* Testimonials Section Styles - Modern & Robust */
.cnc-testimonials {
    padding: 100px 20px;
    background: linear-gradient(135deg, #F8F9FC 0%, #F2EDFF 100%);
    position: relative;
    display: flex;
    justify-content: center;
}

.cnc-testimonials__content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    display: grid;
    gap: 1.5rem;
    justify-items: center;
}

.cnc-testimonials-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    margin-top: 4rem;
}

.cnc-testimonial-card {
    background: linear-gradient(145deg, #FFFFFF 0%, #F6F8FF 100%);
    padding: 2.5rem 2.25rem;
    border-radius: 5px;
    border: 1px solid rgba(94, 58, 142, 0.1);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-align: center;
    position: relative;
    box-shadow: 0 16px 38px rgba(13, 13, 13, 0.06);
    opacity: 0;
    transform: translateY(18px) scale(0.98);
    animation: cnc-card-fade 0.95s ease forwards;
    animation-delay: var(--testimonial-delay, 0s);
}

.cnc-testimonial-card::before {
    content: '"';
    position: absolute;
    top: 1.25rem;
    left: 1.75rem;
    font-size: 5rem;
    color: rgba(198, 48, 140, 0.1);
    font-family: serif;
    line-height: 1;
}

.cnc-testimonial-card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 22px 50px rgba(94, 58, 142, 0.14);
    border-color: rgba(94, 58, 142, 0.18);
}

.cnc-testimonial-content {
    margin-bottom: 2rem;
    position: relative;
    z-index: 2;
}

.cnc-testimonial-content p {
    font-size: 1.125rem;
    line-height: 1.7;
    color: #444;
    font-style: normal;
    font-weight: 400;
}

.cnc-testimonial-author {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    border-top: 1px solid #F0F0F0;
    padding-top: 1.5rem;
}

.cnc-testimonial-author strong {
    display: block;
    color: #5E3A8E;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.cnc-testimonial-author span {
    color: #888;
    font-size: 0.875rem;
    display: block;
}

@keyframes cnc-card-fade {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 768px) {
    .cnc-testimonials {
        padding: 80px 20px;
    }
    .cnc-testimonials-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
</style>

<!-- Testimonials Section -->
<section class="cnc-testimonials" data-animate="fade-up">
    <div class="cnc-testimonials__content">
        <h2 class="cnc-section-title">What Visitors Say</h2>
        <div class="cnc-testimonials-grid">
            <?php if (!empty($cnc_testimonials)) : ?>
                <?php $delay = 0; ?>
                <?php foreach ($cnc_testimonials as $testimonial) : ?>
                    <?php
                    $designation = get_post_meta($testimonial->ID, '_cnc_testimonial_designation', true);
                    $photo = get_the_post_thumbnail($testimonial->ID, 'thumbnail', ['style' => 'border-radius:50%; width:56px; height:56px; object-fit:cover;']);
                    ?>
                    <div class="cnc-testimonial-card" style="--testimonial-delay: <?php echo esc_attr($delay); ?>s;">
                        <div class="cnc-testimonial-content">
                            <p><?php echo esc_html(wp_strip_all_tags($testimonial->post_content)); ?></p>
                        </div>
                        <div class="cnc-testimonial-author">
                            <?php if ($photo) : ?>
                                <?php echo $photo; ?>
                            <?php endif; ?>
                            <div>
                                <strong><?php echo esc_html(get_the_title($testimonial)); ?></strong>
                                <?php if (!empty($designation)) : ?>
                                    <span><?php echo esc_html($designation); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php $delay += 0.12; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="cnc-testimonial-card" style="--testimonial-delay: 0s;">
                    <div class="cnc-testimonial-content">
                        <p>"Cable Net Convergence Expo provided excellent networking opportunities and showcased the latest innovations in our industry. Highly recommended!"</p>
                    </div>
                    <div class="cnc-testimonial-author">
                        <div>
                            <strong>Rajesh Kumar</strong>
                            <span>CEO, BroadNet Solutions</span>
                        </div>
                    </div>
                </div>
                <div class="cnc-testimonial-card" style="--testimonial-delay: 0.12s;">
                    <div class="cnc-testimonial-content">
                        <p>"The exhibition helped us connect with key suppliers and understand market trends. Great organization and quality exhibitors."</p>
                    </div>
                    <div class="cnc-testimonial-author">
                        <div>
                            <strong>Priya Sharma</strong>
                            <span>CTO, TechConnect India</span>
                        </div>
                    </div>
                </div>
                <div class="cnc-testimonial-card" style="--testimonial-delay: 0.24s;">
                    <div class="cnc-testimonial-content">
                        <p>"Cable Net Convergence Expo is the must-attend event for anyone in the cable and broadband industry. Valuable insights and business opportunities."</p>
                    </div>
                    <div class="cnc-testimonial-author">
                        <div>
                            <strong>Mohammed Ali</strong>
                            <span>Director, Cable Vision</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
