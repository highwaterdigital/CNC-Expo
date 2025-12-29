<style>
/* Metrics Section Styles - Modern & Robust */
.cnc-metrics {
    padding: 100px 20px;
    background: linear-gradient(135deg, #0D0D0D 0%, #161424 100%);
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.cnc-metrics::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 50% 50%, rgba(94, 58, 142, 0.18) 0%, transparent 70%);
    pointer-events: none;
}

.cnc-metrics__content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 2;
    display: grid;
    gap: 1.5rem;
    justify-items: center;
}

.cnc-metrics__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(180px, 1fr));
    gap: 1.25rem;
    align-items: stretch;
    margin-top: 1rem;
    width: 100%;
}

.cnc-metric-item {
    text-align: center;
    padding: 2rem;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    transition: all 0.35s ease;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
    opacity: 0;
    transform: translateY(18px) scale(0.98);
    animation: cnc-card-fade 0.9s ease forwards;
    animation-delay: var(--metric-delay, 0s);
}

.cnc-metric-item:hover {
    transform: translateY(-6px) scale(1.01);
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(198, 48, 140, 0.35);
    box-shadow: 0 18px 45px rgba(8, 7, 20, 0.45);
}

.cnc-metric-number {
    font-size: 3.6rem;
    font-weight: 800;
    background: linear-gradient(135deg, #FFFFFF 0%, #C6308C 100%);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
    line-height: 1;
    display: block;
}

.cnc-metric-label {
    font-size: 1.05rem;
    color: #C5C5C5;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
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
    .cnc-metrics {
        padding: 80px 20px;
    }
    .cnc-metrics__grid {
        gap: 1rem;
        grid-template-columns: repeat(2, minmax(150px, 1fr));
    }
    .cnc-metric-number {
        font-size: 2.8rem;
    }
}
</style>

<!-- Metrics Section -->
<section class="cnc-metrics" data-animate="fade-up">
    <div class="cnc-metrics__content">
        <div class="cnc-metrics__grid">
            <div class="cnc-metric-item cnc-metric" style="--metric-delay: 0s;">
                <span class="cnc-metric-number" data-count="10000">10k+</span>
                <span class="cnc-metric-label">Visitors</span>
            </div>
            <div class="cnc-metric-item cnc-metric" style="--metric-delay: 0.08s;">
                <span class="cnc-metric-number" data-count="150">150+</span>
                <span class="cnc-metric-label">Exhibitors</span>
            </div>
            <div class="cnc-metric-item cnc-metric" style="--metric-delay: 0.24s;">
                <span class="cnc-metric-number" data-count="50">50+</span>
                <span class="cnc-metric-label">Speakers</span>
            </div>
        </div>
    </div>
</section>
