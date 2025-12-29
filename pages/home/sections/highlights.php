<style>
/* Highlights Section Styles - Modern & Robust */
.cnc-highlights {
    padding: 100px 20px;
    background: radial-gradient(circle at 20% 20%, rgba(198, 48, 140, 0.08), transparent 40%), #F7F8FD;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.cnc-highlights::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(94, 58, 142, 0.06) 0%, rgba(198, 48, 140, 0.04) 100%);
    pointer-events: none;
}

.cnc-highlights__content {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 1;
    display: grid;
    gap: 1.5rem;
    justify-items: center;
}

.cnc-section-title {
    font-size: clamp(2.4rem, 3vw, 3rem);
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: #0D0D0D;
    letter-spacing: -0.02em;
    position: relative;
    display: inline-block;
}

.cnc-section-title::after {
    content: '';
    display: block;
    width: 72px;
    height: 5px;
    background: linear-gradient(90deg, #C6308C, #5E3A8E);
    margin: 1rem auto 0;
    border-radius: 999px;
    box-shadow: 0 6px 22px rgba(198, 48, 140, 0.4);
}

.cnc-section-desc {
    font-size: 1.1rem;
    color: #4a4a4a;
    max-width: 760px;
    margin: 0 auto 2.5rem;
    line-height: 1.8;
}

.cnc-highlights__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.75rem;
    width: 100%;
    max-width: 1080px;
    justify-items: center;
}

.cnc-highlight-card {
    background: linear-gradient(145deg, #FFFFFF 0%, #F2F4FF 100%);
    padding: 2.5rem 2rem;
    border-radius: 5px;
    transition: all 0.45s cubic-bezier(0.165, 0.84, 0.44, 1);
    text-align: center;
    border: 1px solid rgba(94, 58, 142, 0.08);
    position: relative;
    overflow: hidden;
    z-index: 1;
    box-shadow: 0 20px 50px rgba(13, 13, 13, 0.06);
    opacity: 0;
    transform: translateY(18px) scale(0.98);
    animation: cnc-card-fade 0.9s ease forwards;
    animation-delay: var(--card-delay, 0s);
}

.cnc-highlight-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 20%, rgba(198, 48, 140, 0.08), transparent 40%);
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: -1;
}

.cnc-highlight-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #C6308C, #5E3A8E);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.cnc-highlight-card:hover {
    transform: translateY(-10px) scale(1.01);
    box-shadow: 0 28px 60px rgba(94, 58, 142, 0.14);
}

.cnc-highlight-card:hover::after {
    transform: scaleX(1);
}

.cnc-highlight-card:hover::before {
    opacity: 1;
}

.cnc-highlight-icon {
    width: 72px;
    height: 72px;
    border-radius: 5px;
    display: grid;
    place-items: center;
    margin: 0 auto 1.25rem;
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #FFFFFF;
    font-size: 1.9rem;
    box-shadow: 0 14px 30px rgba(198, 48, 140, 0.25);
}

.cnc-highlight-card h3 {
    font-size: 1.45rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    color: #0D0D0D;
}

.cnc-highlight-card p {
    color: #555;
    line-height: 1.7;
    font-size: 1.02rem;
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
    .cnc-highlights {
        padding: 70px 16px;
    }
    .cnc-section-title {
        font-size: 2rem;
    }
    .cnc-section-desc {
        margin-bottom: 2rem;
    }
}
</style>

<!-- Highlights Section -->
<section class="cnc-highlights" data-animate="fade-up">
    <div class="cnc-highlights__content">
        <h2 class="cnc-section-title">Why Attend Cable Net Convergence Expo 2026?</h2>
        <p class="cnc-section-desc">Join the premier gathering of cable and internet professionals. Discover why industry leaders choose Cable Net Convergence Expo as their platform for growth.</p>

        <div class="cnc-highlights__grid">
            <div class="cnc-highlight-card" style="--card-delay: 0s;">
                <span class="cnc-highlight-icon" aria-hidden="true">&#128640;</span>
                <h3>Latest Technology</h3>
                <p>Explore cutting-edge innovations in cable, internet, and broadband technology from leading global manufacturers.</p>
            </div>
            <div class="cnc-highlight-card" style="--card-delay: 0.1s;">
                <span class="cnc-highlight-icon" aria-hidden="true">&#129309;</span>
                <h3>Networking</h3>
                <p>Connect with industry leaders, potential partners, and technology experts from across India and international markets.</p>
            </div>
            <div class="cnc-highlight-card" style="--card-delay: 0.2s;">
                <span class="cnc-highlight-icon" aria-hidden="true">&#128200;</span>
                <h3>Business Growth</h3>
                <p>Discover new business opportunities, partnerships, and market insights that will drive your company's growth.</p>
            </div>
        </div>
    </div>
</section>
