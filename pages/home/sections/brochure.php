<style>
/* Brochure Section */
.cnc-brochure {
    padding: 110px 20px;
    background: linear-gradient(180deg, #FFFFFF 0%, #F7F3FB 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
}

.cnc-brochure::before {
    content: '';
    position: absolute;
    width: 460px;
    height: 460px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(198, 48, 140, 0.08) 0%, transparent 70%);
    top: -120px;
    left: -120px;
    z-index: 1;
}

.cnc-brochure::after {
    content: '';
    position: absolute;
    width: 520px;
    height: 520px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(94, 58, 142, 0.08) 0%, transparent 70%);
    bottom: -140px;
    right: -160px;
    z-index: 1;
}

.cnc-brochure__content {
    max-width: 520px;
    display: grid;
    gap: 1.25rem;
    position: relative;
    z-index: 2;
    justify-items: center;
    text-align: center;
}

.cnc-brochure__eyebrow {
    display: inline-block;
    padding: 10px 18px;
    background: rgba(198, 48, 140, 0.12);
    color: #C6308C;
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
}

.cnc-brochure__title {
    font-size: clamp(2.1rem, 4vw, 3rem);
    line-height: 1.15;
    font-weight: 800;
    color: #0D0D0D;
    letter-spacing: -0.02em;
}

.cnc-brochure__lead {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #3f3f3f;
    max-width: 520px;
}

.cnc-brochure__points {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 0.9rem;
    width: 100%;
}

.cnc-brochure__points li {
    position: relative;
    padding-left: 1.6rem;
    text-align: left;
    font-weight: 600;
    color: #1e1e1e;
}

.cnc-brochure__points li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.6rem;
    width: 10px;
    height: 10px;
    background: linear-gradient(135deg, #C6308C, #5E3A8E);
    border-radius: 50%;
    box-shadow: 0 0 0 6px rgba(198, 48, 140, 0.12);
    transform: translateY(-50%);
}

.cnc-brochure__cta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
}

.cnc-brochure__cta .cnc-btn {
    background: transparent;
    color: #C6308C;
    border-color: #C6308C;
    box-shadow: 0 12px 30px rgba(198, 48, 140, 0.2);
}

.cnc-brochure__cta .cnc-btn:hover {
    background: linear-gradient(135deg, #C6308C, #5E3A8E);
    color: #FFFFFF;
    border-color: transparent;
    box-shadow: 0 14px 36px rgba(94, 58, 142, 0.3);
}

.cnc-brochure__media {
    position: relative;
    z-index: 2;
}

.cnc-brochure__image {
    width: 100%;
    max-width: 620px;
    border-radius: 20px;
    box-shadow: 0 28px 60px rgba(0, 0, 0, 0.2);
    object-fit: cover;
    display: block;
}

.cnc-brochure__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 3rem;
    align-items: center;
    max-width: 1300px;
    width: 100%;
}

@media (max-width: 768px) {
    .cnc-brochure {
        padding: 80px 20px;
    }
    .cnc-brochure__content {
        justify-items: start;
        text-align: left;
    }
    .cnc-brochure__points li {
        text-align: left;
    }
}
</style>

<section class="cnc-brochure" data-animate="fade-up">
    <div class="cnc-brochure__grid cnc-container">
        <div class="cnc-brochure__content">
            <span class="cnc-brochure__eyebrow">Download the 2026 brochure</span>
            <h2 class="cnc-brochure__title">Cable, Broadband, OTT & Security in one definitive guide</h2>
            <p class="cnc-brochure__lead">
                Get the latest floor plan, audience breakdown, and sponsorship inventory for the 2026 Cable & Internet Expo Hyderabad.
            </p>
            <ul class="cnc-brochure__points">
                <li>Converged coverage: Cable TV, broadband, OTT, FTTH, IPTV, mobile networks, security, broadcast, and content workflows.</li>
                <li>What’s inside: product showcases, feature zones, conference themes, hosted buyer programs, and branding opportunities.</li>
                <li>Who attends: MSOs, LCOs, ISPs, broadcasters, telcos, content owners, and enterprise network leaders.</li>
            </ul>
            <div class="cnc-brochure__cta">
                <a href="https://cablenetconvergence.com/wp-content/uploads/2025/10/CNC-Brochure-v2.pdf" target="_blank" rel="noopener" class="cnc-btn cnc-btn--primary">
                    Download Brochure
                </a>
            </div>
        </div>
        <div class="cnc-brochure__media">
            <img src="https://images.unsplash.com/photo-1551836022-4c4c79ecde51?auto=format&fit=crop&w=1200&q=80" alt="Attendees exploring solutions at CNC Expo" class="cnc-brochure__image" loading="lazy">
        </div>
    </div>
</section>
