<style>
/* CTA Section Styles - Modern & Robust */
.cnc-cta {
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    padding: 120px 20px;
    text-align: center;
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
}

/* Pattern Overlay */
.cnc-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 20%),
                      radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 20%);
    background-size: 100% 100%;
    pointer-events: none;
}

.cnc-cta__content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.cnc-cta h2 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    letter-spacing: -0.02em;
    color: #FFFFFF;
    text-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.cnc-cta p {
    font-size: 1.25rem;
    margin-bottom: 3rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
    font-weight: 500;
}

.cnc-cta-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.cnc-btn--large {
    padding: 1.25rem 3.5rem;
    font-size: 1.125rem;
    border-radius: 5px;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.cnc-btn--white {
    background: #FFFFFF;
    color: #C6308C;
    border: 2px solid #FFFFFF;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.cnc-btn--white:hover {
    background: #F0F0F0;
    border-color: #F0F0F0;
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    color: #5E3A8E;
}

.cnc-btn--outline-white {
    background: transparent;
    color: #FFFFFF;
    border: 2px solid rgba(255,255,255,0.4);
}

.cnc-btn--outline-white:hover {
    border-color: #FFFFFF;
    background: rgba(255,255,255,0.1);
    transform: translateY(-3px);
}

@media (max-width: 768px) {
    .cnc-cta {
        padding: 80px 20px;
    }
    .cnc-cta h2 {
        font-size: 2.5rem;
    }
    .cnc-cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    .cnc-btn--large {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<!-- CTA Section -->
<section class="cnc-cta" data-animate="fade-up">
    <div class="cnc-cta__content">
        <h2>Ready to Join Cable Net Convergence Expo 2026?</h2>
        <p>Don't miss India's biggest cable and broadband technology event. Register now for early bird benefits!</p>
        <div class="cnc-cta-buttons">
            <a href="<?php echo esc_url('https://cnc.tst-india.com'); ?>" target="_blank" class="cnc-btn cnc-btn--white cnc-btn--large">
                Register as Visitor
            </a>
            <a href="<?php echo esc_url(home_url('/book-space')); ?>" class="cnc-btn cnc-btn--outline-white cnc-btn--large">
                Book a Stall
            </a>
        </div>
    </div>
</section>
