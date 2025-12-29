<style>
/* Venue Section Styles - Modern & Robust */
.cnc-venue {
    padding: 100px 20px;
    background: #FFFFFF;
    position: relative;
}

.cnc-venue-container {
    max-width: 1000px;
    margin: 0 auto;
    text-align: center;
}

.cnc-venue-info h2 {
    text-align: center;
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #0D0D0D;
}

.cnc-venue-details h3 {
    font-size: 2rem;
    color: #5E3A8E;
    margin-bottom: 1rem;
    font-weight: 700;
}

.cnc-venue-address {
    font-size: 1.25rem;
    line-height: 1.6;
    color: #666;
    margin-bottom: 4rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.cnc-venue-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 4rem;
}

.cnc-venue-feature {
    text-align: center;
    padding: 2rem;
    background: #F9FAFB;
    border-radius: 5px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.cnc-venue-feature:hover {
    transform: translateY(-5px);
    background: #FFFFFF;
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    border-color: #EAEAEA;
}

.cnc-venue-feature strong {
    display: block;
    color: #C6308C;
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.cnc-venue-feature span {
    color: #555;
    font-weight: 500;
}

.cnc-map-placeholder {
    background: #F0F0F0;
    padding: 4rem 2rem;
    border-radius: 5px;
    text-align: center;
    margin-top: 4rem;
    position: relative;
    overflow: hidden;
}

.cnc-map-placeholder::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiNjY2MiIG9wYWNpdHk9IjAuMiIvPjwvc3ZnPg==');
    opacity: 0.5;
}

.cnc-map-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    display: block;
    position: relative;
    z-index: 2;
}

.cnc-btn--small {
    padding: 0.75rem 2rem;
    font-size: 0.95rem;
    min-width: auto;
    border-radius: 5px;
    background: #0D0D0D;
    color: white;
    transition: all 0.3s ease;
}

.cnc-btn--small:hover {
    background: #333;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
    .cnc-venue {
        padding: 80px 20px;
    }
    .cnc-venue-info h2 {
        font-size: 2.25rem;
    }
}
</style>

<!-- Venue Information -->
<section class="cnc-venue" data-animate="fade-up">
    <div class="cnc-venue-container">
        <div class="cnc-venue-info">
            <h2 class="cnc-section-title">Venue</h2>
            <div class="cnc-venue-details">
                <h3>HITEX Exhibition Center</h3>
                <p class="cnc-venue-address">
                    Trade Fair Office Building, Izzat Nagar,<br>
                    Hyderabad, Telangana 500084
                </p>
                
                <div class="cnc-venue-features">
                    <div class="cnc-venue-feature">
                        <strong>Hall 1 & 2</strong>
                        <span>Exhibition Area</span>
                    </div>
                    <div class="cnc-venue-feature">
                        <strong>20,000+ sqm</strong>
                        <span>Floor Space</span>
                    </div>
                    <div class="cnc-venue-feature">
                        <strong>Parking</strong>
                        <span>5,000+ Cars</span>
                    </div>
                    <div class="cnc-venue-feature">
                        <strong>Airport</strong>
                        <span>35 Mins Drive</span>
                    </div>
                </div>

                <a href="https://goo.gl/maps/..." target="_blank" class="cnc-btn cnc-btn--small">
                    Get Directions
                </a>
            </div>
        </div>
    </div>
</section>
