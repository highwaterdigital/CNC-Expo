<style>
/* About Section Styles - Modern & Robust */
.cnc-about {
    padding: 120px 20px;
    background: linear-gradient(135deg, #FFFFFF 0%, #F8F3FF 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

/* Decorative Background Elements */
.cnc-about::before {
    content: '';
    position: absolute;
    top: -10%;
    right: -5%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(94, 58, 142, 0.05) 0%, transparent 70%);
    border-radius: 5px;
    z-index: 1;
}

.cnc-about::after {
    content: '';
    position: absolute;
    bottom: -10%;
    left: -5%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(198, 48, 140, 0.05) 0%, transparent 70%);
    border-radius: 5px;
    z-index: 1;
}

.cnc-about__content {
    max-width: 960px;
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 2;
    display: grid;
    gap: 1.25rem;
    justify-items: center;
}

.cnc-about-text {
    font-size: 1.2rem;
    line-height: 1.8;
    color: #3f3f3f;
    margin: 0;
    font-weight: 400;
    opacity: 0;
    transform: translateY(16px);
    animation: cnc-card-fade 0.9s ease forwards;
}

.cnc-about-text strong {
    color: #5E3A8E;
    font-weight: 600;
}

.cnc-about-highlight {
    font-size: 1.35rem;
    line-height: 1.6;
    color: #0D0D0D;
    font-weight: 600;
    margin-top: 1.75rem;
    padding: 1.75rem;
    background: #FFFFFF;
    border-radius: 5px;
    box-shadow: 0 16px 40px rgba(94, 58, 142, 0.12);
    border: 1px solid rgba(198, 48, 140, 0.12);
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    animation: cnc-card-fade 0.95s ease forwards;
    animation-delay: 0.2s;
}

.cnc-about-highlight::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(94, 58, 142, 0.08), rgba(198, 48, 140, 0.12));
    opacity: 0.35;
    z-index: -1;
}

@keyframes cnc-card-fade {
    from {
        opacity: 0;
        transform: translateY(24px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 768px) {
    .cnc-about {
        padding: 80px 20px;
    }
    .cnc-about-text {
        font-size: 1.1rem;
    }
    .cnc-about-highlight {
        font-size: 1.2rem;
        padding: 1.5rem;
    }
}
</style>

<!-- About Section -->
<section class="cnc-about" data-animate="fade-up">
    <div class="cnc-about__content">
        <h2 class="cnc-section-title">About Cable Net Convergence Expo</h2>
        <p class="cnc-about-text" style="animation-delay: 0s;">
            <strong>Cable Net Convergence (CNC) Expo</strong> is India's most comprehensive platform for cable, internet, and broadband technology professionals. Now in its <strong>14th edition</strong>, Cable Net Convergence Expo brings together the entire ecosystem of connectivity solutions.
        </p>
        <p class="cnc-about-text" style="animation-delay: 0.12s;">
            From MSOs and LCOs to technology vendors and service providers, Cable Net Convergence Expo is where the future of India's digital connectivity is shaped. Join us at <strong>HITEX, Hyderabad</strong> for three days of innovation, networking, and business growth.
        </p>
        <div class="cnc-about-highlight">
            "Experience live demonstrations, attend technical sessions, and discover the technologies that are transforming how India connects to the digital world."
        </div>
    </div>
</section>
