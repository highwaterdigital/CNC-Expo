<style>
/* Sponsors Section Styles - Modern & Robust */
.cnc-sponsors {
    padding: 100px 20px;
    background: #FFFFFF;
    position: relative;
    overflow: hidden;
}

/* Subtle background decoration */
.cnc-sponsors::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #5E3A8E, #C6308C, #0090D9);
}

.cnc-sponsors__content {
    max-width: 1400px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 2;
}

.cnc-sponsor-tier {
    margin-bottom: 5rem;
}

.cnc-sponsor-tier h3 {
    font-size: 1.25rem;
    color: #5E3A8E;
    margin-bottom: 2.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    position: relative;
    display: inline-block;
}

.cnc-sponsor-tier h3::after {
    content: '';
    display: block;
    width: 40px;
    height: 3px;
    background: #C6308C;
    margin: 10px auto 0;
    border-radius: 2px;
}

.cnc-sponsor-logos {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.cnc-sponsor-logo {
    background: #FFFFFF;
    padding: 2rem;
    border-radius: 5px;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    border: 1px solid #F0F0F0;
    min-width: 180px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.cnc-sponsor-logo::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(94, 58, 142, 0.05) 0%, rgba(198, 48, 140, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
}

.cnc-sponsor-logo:hover {
    border-color: transparent;
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.cnc-sponsor-logo:hover::before {
    opacity: 1;
}

.cnc-sponsor-logo--large {
    padding: 3rem;
    min-width: 280px;
    height: 140px;
    background: #FFFFFF;
    border: 2px solid transparent;
    background-image: linear-gradient(#fff, #fff), linear-gradient(135deg, #5E3A8E, #C6308C);
    background-origin: border-box;
    background-clip: content-box, border-box;
    box-shadow: 0 10px 30px rgba(94, 58, 142, 0.1);
}

.cnc-sponsor-logo--large:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 50px rgba(198, 48, 140, 0.2);
}

.cnc-placeholder-logo {
    font-weight: 700;
    color: #888;
    font-size: 1rem;
    z-index: 1;
    transition: color 0.3s ease;
}

.cnc-sponsor-logo:hover .cnc-placeholder-logo {
    color: #5E3A8E;
}

.cnc-sponsors-cta {
    margin-top: 5rem;
    text-align: center;
}

@media (max-width: 768px) {
    .cnc-sponsors {
        padding: 80px 20px;
    }
    .cnc-sponsor-logos {
        gap: 1.5rem;
    }
    .cnc-sponsor-logo {
        min-width: 140px;
        padding: 1.5rem;
    }
}
</style>

<!-- Sponsors Section -->
<section class="cnc-sponsors" data-animate="fade-up">
    <div class="cnc-sponsors__content">
        <h2 class="cnc-section-title">Our Sponsors</h2>
        <div class="cnc-sponsors-tiers">
            <div class="cnc-sponsor-tier">
                <h3>Title Sponsor</h3>
                <div class="cnc-sponsor-logos cnc-sponsor-logos--title">
                    <div class="cnc-sponsor-logo cnc-sponsor-logo--large">
                        <div class="cnc-placeholder-logo">TITLE SPONSOR</div>
                    </div>
                </div>
            </div>
            <div class="cnc-sponsor-tier">
                <h3>Gold Sponsors</h3>
                <div class="cnc-sponsor-logos cnc-sponsor-logos--gold">
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">GOLD 1</div>
                    </div>
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">GOLD 2</div>
                    </div>
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">GOLD 3</div>
                    </div>
                </div>
            </div>
            <div class="cnc-sponsor-tier">
                <h3>Silver Sponsors</h3>
                <div class="cnc-sponsor-logos cnc-sponsor-logos--silver">
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">SILVER 1</div>
                    </div>
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">SILVER 2</div>
                    </div>
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">SILVER 3</div>
                    </div>
                    <div class="cnc-sponsor-logo">
                        <div class="cnc-placeholder-logo">SILVER 4</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cnc-sponsors-cta">
            <a href="<?php echo esc_url(home_url('/sponsors')); ?>" class="cnc-btn cnc-btn--outline">
                Become a Sponsor
            </a>
        </div>
    </div>
</section>
