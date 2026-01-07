<style>
/* Hero Section Styles - Modern & Robust */
.cnc-hero {
    position: relative !important;
    min-height: 95vh !important;
    display: flex !important;
    align-items: flex-end !important; 
    justify-content: center !important;
    overflow: hidden !important;
    color: #FFFFFF !important;
    font-family: 'Poppins', sans-serif !important;
    padding: calc(140px + 4vw) 0 80px 0 !important;
}

/* Video Background */
.cnc-hero__video,
.cnc-hero__video::before {
    position: absolute !important;
    inset: 0 !important;
}

.cnc-hero__video {
    z-index: 1 !important;
}

.cnc-video-container {
    position: absolute !important;
    inset: -10% -15% -10% -15%;
    width: 130vw !important;
    height: 110vh !important;
    min-width: 110vw !important;
    min-height: 70vw !important;
    transform: translate(-5%, -10%) !important;
    overflow: hidden !important;
}

.cnc-video-container iframe {
    width: 100% !important;
    height: 100% !important;
    border: none !important;
    pointer-events: none !important;
}

/* Overlay - Gradient with brand colors */
.cnc-video-overlay {
    position: absolute !important;
    inset: 0 !important;
    background: linear-gradient(
        to bottom,
        rgba(94, 58, 142, 0.2) 0%,
        rgba(13, 13, 13, 0.4) 50%,
        rgba(13, 13, 13, 0.9) 100%
    ) !important;
    z-index: 2 !important;
    backdrop-filter: blur(2px);
}

.cnc-hero__black-overlay {
    position: absolute !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.1) !important;
    z-index: 3 !important;
    pointer-events: none;
}

/* Content Layout - Bottom aligned */
.cnc-hero__content {
    position: relative !important;
    z-index: 11 !important;
    width: 100% !important;
    max-width: 1400px !important;
    margin: 0 auto !important;
    padding: 4rem 2rem 5rem !important;
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 2.5rem !important;
}

/* Headlines */
.cnc-hero__headline {
    font-size: clamp(3rem, 6vw, 5.5rem) !important;
    line-height: 1.1 !important;
    margin: 0 !important;
    text-transform: uppercase !important;
    letter-spacing: -0.02em !important;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.6) !important;
    font-weight: 800 !important;
    animation: fadeUp 1s ease-out !important;
}

.cnc-hero__headline span {
    display: block !important;
    color: #ffffff !important;
}

.cnc-hero__highlight {
    background: linear-gradient(135deg, #C6308C 0%, #FFC107 100%);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: none !important;
    filter: drop-shadow(0 4px 10px rgba(198, 48, 140, 0.3));
}

/* Info Bar (Date/Stats) */
.cnc-hero__info-bar {
    display: flex !important;
    gap: 3rem !important;
    align-items: center !important;
    justify-content: center !important;
    flex-wrap: wrap !important;
    margin-bottom: 1rem !important;
    animation: fadeUp 1s ease-out 0.3s backwards !important;
    background: rgba(255, 255, 255, 0.1);
    padding: 5rem 8rem;
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.1);
}

.cnc-info-item {
    text-align: center !important;
    border-right: 1px solid rgba(255,255,255,0.3) !important;
    padding-right: 3rem !important;
}

.cnc-info-item:last-child {
    border-right: none !important;
    padding-right: 0 !important;
}

.cnc-info-value {
    display: block !important;
    font-size: 1.2rem !important;
    font-weight: 700 !important;
    color: #FFFFFF !important;
    text-transform: uppercase !important;
}

.cnc-info-label {
    display: block !important;
    font-size: 0.85rem !important;
    color: rgba(255, 255, 255, 0.8) !important;
    margin-top: 0.2rem !important;
}

/* Button - Gradient & Animated */
.cnc-hero__btn-wrapper {
    animation: fadeUp 1s ease-out 0.6s backwards !important;
}

.cnc-hero__btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1.2rem 3rem !important;
    border-radius: 50px !important;
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%) !important;
    color: #FFFFFF !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    text-decoration: none !important;
    border: none !important;
    box-shadow: 0 10px 25px rgba(94, 58, 142, 0.4) !important;
    transition: all 0.3s ease !important;
    position: relative;
    overflow: hidden;
}

.cnc-hero__btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #C6308C 0%, #5E3A8E 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cnc-hero__btn span {
    position: relative;
    z-index: 1;
}

.cnc-hero__btn:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 15px 35px rgba(198, 48, 140, 0.5) !important;
}

.cnc-hero__btn:hover::before {
    opacity: 1;
}

/* Animations */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .cnc-hero {
        align-items: center !important; /* Center vertically on mobile */
        padding-bottom: 80px !important;
    }
    
    .cnc-hero__headline {
        font-size: clamp(2.2rem, 8vw, 3.5rem) !important;
        margin-bottom: 1rem !important;
    }
    
    .cnc-hero__info-bar {
        gap: 1.5rem !important;
        margin-bottom: 2rem !important;
        padding: 1rem 1.5rem;
        width: 90%;
    }
    
    .cnc-info-item {
        padding-right: 1.5rem !important;
        border-right: 1px solid rgba(255,255,255,0.2) !important;
    }
    
    .cnc-info-value { font-size: 1rem !important; }
    .cnc-info-label { font-size: 0.75rem !important; }
    
    .cnc-hero__btn {
        width: 100% !important;
        max-width: 280px !important;
        font-size: 1rem !important;
        padding: 1rem 2rem !important;
    }
    .cnc-video-container {
        inset: -30% -10% -20% -10% !important;
        width: 140vw !important;
        height: 95vh !important;
    }
}
</style>

<!-- Hero Section with Video -->
<section class="cnc-hero" data-animate="fade-up">
    <!-- Video Background -->
    <div class="cnc-hero__video">
        <div class="cnc-video-container">
            <iframe 
                id="cnc-hero-video"
                src="https://www.youtube.com/embed/XmDWHvrt9uw?start=31&autoplay=1&mute=1&loop=1&playlist=XmDWHvrt9uw&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
        <div class="cnc-video-overlay"></div>
        <div class="cnc-hero__black-overlay"></div>
    </div>
    
    <!-- Hero Content -->
    <div class="cnc-hero__content">
        
        <h1 class="cnc-hero__headline">
            <span>Cable Net Convergence</span>
            <span class="cnc-hero__highlight">India's Biggest Expo</span>
        </h1>

        <!-- Additional Info Bar -->
        <div class="cnc-hero__info-bar">
            <div class="cnc-info-item">
                <span class="cnc-info-value">13-15 Aug 2026</span>
                <span class="cnc-info-label">Date</span>
            </div>
            <div class="cnc-info-item">
                <span class="cnc-info-value">HITEX, Hyd</span>
                <span class="cnc-info-label">Venue</span>
            </div>
        </div>

        <div class="cnc-hero__btn-wrapper">
            <a href="https://cablenetconvergence.com/book-space" class="cnc-hero__btn">
                <span>Book Your Stall</span>
            </a>
        </div>
    </div>
</section>
