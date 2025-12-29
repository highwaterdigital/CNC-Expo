<style>
/* Exhibitors Preview Section Styles - Modern & Robust */
.cnc-exhibitors-preview {
    padding: 100px 20px;
    background: radial-gradient(circle at 15% 10%, rgba(198, 48, 140, 0.08), transparent 35%), #FFFFFF;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.cnc-section__content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    display: grid;
    gap: 1.2rem;
    justify-items: center;
}

.cnc-section-subtitle {
    font-size: 1.1rem;
    color: #525252;
    margin-bottom: 2.5rem;
    max-width: 680px;
    line-height: 1.7;
}

.cnc-exhibitors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.75rem;
    margin-bottom: 3rem;
    width: 100%;
}

.cnc-exhibitor-card {
    background: linear-gradient(145deg, #FFFFFF 0%, #F6F8FF 100%);
    padding: 2.25rem 1.75rem;
    border-radius: 5px;
    border: 1px solid rgba(94, 58, 142, 0.1);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 16px 38px rgba(13, 13, 13, 0.06);
    opacity: 0;
    transform: translateY(18px) scale(0.98);
    animation: cnc-card-fade 0.9s ease forwards;
    animation-delay: var(--exhibitor-delay, 0s);
}

.cnc-exhibitor-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #C6308C, #5E3A8E);
    transform: scaleX(0);
    transform-origin: center;
    transition: transform 0.4s ease;
}

.cnc-exhibitor-card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 24px 52px rgba(94, 58, 142, 0.14);
    border-color: rgba(94, 58, 142, 0.2);
}

.cnc-exhibitor-card:hover::after {
    transform: scaleX(1);
}

.cnc-exhibitor-logo {
    margin-bottom: 1.5rem;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cnc-placeholder-logo {
    background: #F5F5F5;
    color: #555;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
    font-weight: 700;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
    border: 1px dashed #DDD;
}

.cnc-exhibitor-card h4 {
    font-size: 1.25rem;
    color: #0D0D0D;
    margin-bottom: 0.75rem;
    font-weight: 700;
}

.cnc-exhibitor-card p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
}

.cnc-exhibitors-cta {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cnc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 2rem;
    border-radius: 5px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.cnc-btn--primary {
    background: linear-gradient(135deg, #C6308C 0%, #5E3A8E 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 15px rgba(198, 48, 140, 0.3);
}

.cnc-btn--primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(198, 48, 140, 0.4);
    color: white;
}

.cnc-btn--outline {
    background: transparent;
    color: #0D0D0D;
    border: 2px solid #E0E0E0;
}

.cnc-btn--outline:hover {
    border-color: #0D0D0D;
    background: transparent;
    transform: translateY(-2px);
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
    .cnc-exhibitors-preview {
        padding: 80px 20px;
    }
    .cnc-exhibitors-grid {
        gap: 1.5rem;
    }
}
</style>

<!-- Exhibitors Preview Section -->
<section class="cnc-exhibitors-preview" data-animate="fade-up">
    <div class="cnc-section__content">
        <h2 class="cnc-section-title">2025 Exhibitors</h2>
        <p class="cnc-section-subtitle">Connect with leading companies shaping the future of connectivity</p>
        <div class="cnc-exhibitors-grid">
            <div class="cnc-exhibitor-card" style="--exhibitor-delay: 0s;">
                <div class="cnc-exhibitor-logo">
                    <div class="cnc-placeholder-logo">TECH CO</div>
                </div>
                <h4>Leading Cable Manufacturers</h4>
                <p>Fiber optic cables, networking equipment, and connectivity solutions</p>
            </div>
            <div class="cnc-exhibitor-card" style="--exhibitor-delay: 0.08s;">
                <div class="cnc-exhibitor-logo">
                    <div class="cnc-placeholder-logo">BROAD NET</div>
                </div>
                <h4>Broadband Service Providers</h4>
                <p>High-speed internet solutions and last-mile connectivity</p>
            </div>
            <div class="cnc-exhibitor-card" style="--exhibitor-delay: 0.16s;">
                <div class="cnc-exhibitor-logo">
                    <div class="cnc-placeholder-logo">SAT COM</div>
                </div>
                <h4>Satellite Technology</h4>
                <p>Satellite communication systems and broadcasting equipment</p>
            </div>
            <div class="cnc-exhibitor-card" style="--exhibitor-delay: 0.24s;">
                <div class="cnc-exhibitor-logo">
                    <div class="cnc-placeholder-logo">SMART TV</div>
                </div>
                <h4>Digital Entertainment</h4>
                <p>Set-top boxes, streaming devices, and digital content platforms</p>
            </div>
        </div>
        <div class="cnc-exhibitors-cta">
            <a href="<?php echo esc_url(home_url('/exhibitors')); ?>" class="cnc-btn cnc-btn--primary">
                View All Exhibitors
            </a>
            <a href="<?php echo esc_url(home_url('/book-space')); ?>" class="cnc-btn cnc-btn--outline">
                Book Exhibition Space
            </a>
        </div>
    </div>
</section>
