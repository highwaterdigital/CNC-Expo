<?php
/**
 * Footer Component - CNC Expo Hyderabad 2026 * Design System v4.0 - Modern Color Palette
 * Magenta (#C6308C) | Purple (#5E3A8E) | Blue (#0090D9)
 *
 * @package cnc_child
 * @version 4.0.0
 * @author CNC Expo Development Team
 */

if (!defined('ABSPATH')) exit;

?>
    </main><!-- /.site-main -->

<style>
/* ============================================================================
   CNC EXPO FOOTER - DESIGN SYSTEM v4.0
   Modern Color Palette Based on Logo
   ============================================================================ */

:root {
    /* === MODERN COLOR PALETTE === */
    --cnc-magenta: #C6308C;
    --cnc-magenta-dark: #A02773;
    --cnc-magenta-light: #E94FAF;
    
    --cnc-purple: #5E3A8E;
    --cnc-purple-dark: #4A2E6F;
    
    --cnc-blue: #0090D9;
    --cnc-blue-dark: #007AB8;
    
    --cnc-teal: #00B4B4;
    --cnc-teal-dark: #009999;
    
    /* === NEUTRALS === */
    --cnc-black: #0D0D0D;
    --cnc-white: #FFFFFF;
    --cnc-gray-light: #F8F9FA;
    --cnc-gray: #E9ECEF;
    --cnc-gray-dark: #6C757D;
    --cnc-dark: #212529;
    
    /* === ACCENTS === */
    --cnc-gold: #FFC107;
    --cnc-orange: #FF6B35;
    
    /* === GRADIENTS === */
    --gradient-primary: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    --gradient-secondary: linear-gradient(135deg, #0090D9 0%, #00B4B4 100%);
    --gradient-dark: linear-gradient(135deg, #0D0D0D 0%, #212529 100%);
    
    /* === DESIGN TOKENS === */
    --cnc-radius: 5px;
    --cnc-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    --cnc-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* === TYPOGRAPHY === */
    --cnc-font-primary: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --cnc-font-secondary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ============================================================================
   FOOTER MAIN CONTAINER
   ============================================================================ */

.cnc-footer {
    background: var(--gradient-dark);
    color: rgba(255, 255, 255, 0.85);
    font-family: var(--cnc-font-secondary);
    border-top: 4px solid var(--cnc-magenta);
    position: relative;
    overflow: hidden;
}

/* Animated background pattern */
.cnc-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 20px,
        rgba(198, 48, 140, 0.02) 20px,
        rgba(198, 48, 140, 0.02) 40px
    );
    pointer-events: none;
}

/* ============================================================================
   FOOTER MAIN CONTENT GRID
   ============================================================================ */

.cnc-footer__main {
    max-width: 1400px;
    margin: 0 auto;
    padding: 5rem 2rem 3rem;
    display: grid;
    grid-template-columns: 2.5fr 1fr 1fr 1.5fr;
    gap: 3rem;
    position: relative;
    z-index: 2;
}

/* ============================================================================
   BRAND COLUMN
   ============================================================================ */

.cnc-footer__brand {
    padding-right: 2rem;
}

.cnc-footer__logo {
    max-width: 220px;
    height: auto;
    margin-bottom: 1.5rem;
    transition: var(--cnc-transition);
    filter: brightness(1.1);
}

.cnc-footer__logo:hover {
    filter: brightness(1.3);
    transform: scale(1.05);
}

.cnc-footer__brand-title {
    font-family: var(--cnc-font-primary);
    font-size: 2rem;
    font-weight: 800;
    color: var(--cnc-white);
    margin-bottom: 0.5rem;
    letter-spacing: -0.5px;
}

.cnc-footer__brand-highlight {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cnc-footer__tagline {
    font-size: 1rem;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 2rem;
    font-weight: 400;
}

.cnc-footer__event-info {
    background: rgba(255, 255, 255, 0.05);
    border-left: 3px solid var(--cnc-gold);
    padding: 1rem 1.25rem;
    border-radius: var(--cnc-radius);
    margin-bottom: 2rem;
}

.cnc-footer__event-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--cnc-gold);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.cnc-footer__event-date {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--cnc-white);
}

.cnc-footer__social {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.cnc-footer__social-link {
    width: 45px;
    height: 45px;
    border-radius: 0%;
    background: rgba(255, 255, 255, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: var(--cnc-white);
    text-decoration: none;
    font-size: 1.1rem;
    transition: var(--cnc-transition);
    border: 2px solid transparent;
}

.cnc-footer__social-link:hover {
    background: var(--gradient-primary);
    border-color: var(--cnc-magenta-light);
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(198, 48, 140, 0.4);
}

/* ============================================================================
   LINKS COLUMNS
   ============================================================================ */

.cnc-footer__section-title {
    font-family: var(--cnc-font-primary);
    font-weight: 700;
    font-size: 1.25rem;
    color: var(--cnc-white);
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.75rem;
}

.cnc-footer__section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: var(--gradient-primary);
    border-radius: 2px;
}

.cnc-footer__links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.cnc-footer__links li {
    margin-bottom: 0.75rem;
}

.cnc-footer__link {
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    font-size: 0.95rem;
    transition: var(--cnc-transition);
    display: inline-block;
    position: relative;
}

.cnc-footer__link::before {
    content: '→';
    position: absolute;
    left: -20px;
    opacity: 0;
    transition: var(--cnc-transition);
    color: var(--cnc-magenta);
}

.cnc-footer__link:hover {
    color: var(--cnc-magenta-light);
    padding-left: 20px;
}

.cnc-footer__link:hover::before {
    opacity: 1;
    left: 0;
}

/* ============================================================================
   CONTACT COLUMN
   ============================================================================ */

.cnc-footer__contact-item {
    margin-bottom: 1.5rem;
}

.cnc-footer__contact-label {
    font-weight: 700;
    color: var(--cnc-gold);
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.cnc-footer__contact-value {
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.6;
}

.cnc-footer__contact-link {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    transition: var(--cnc-transition);
}

.cnc-footer__contact-link:hover {
    color: var(--cnc-teal);
}

/* ============================================================================
   FOOTER BOTTOM BAR
   ============================================================================ */

.cnc-footer__bottom {
    background: rgba(0, 0, 0, 0.6);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    position: relative;
    z-index: 2;
}

.cnc-footer__bottom-wrap {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.cnc-footer__copyright {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
}

.cnc-footer__legal {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.cnc-footer__legal-link {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    font-size: 0.9rem;
    transition: var(--cnc-transition);
}

.cnc-footer__legal-link:hover {
    color: var(--cnc-magenta);
}

.cnc-footer__credits {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.85rem;
}

.cnc-footer__credits-link {
    color: var(--cnc-gold);
    text-decoration: none;
    font-weight: 600;
    transition: var(--cnc-transition);
}

.cnc-footer__credits-link:hover {
    color: var(--cnc-white);
}

/* ============================================================================
   BACK TO TOP BUTTON
   ============================================================================ */

.cnc-back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    border-radius: 0px;
    display: none;
    align-items: center;
    justify-content: center;
    color: var(--cnc-white);
    text-decoration: none;
    font-size: 1.5rem;
    box-shadow: 0 4px 20px rgba(198, 48, 140, 0.4);
    transition: var(--cnc-transition);
    z-index: 9998;
    border: 2px solid transparent;
}

.cnc-back-to-top.visible {
    display: flex;
}

.cnc-back-to-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 30px rgba(198, 48, 140, 0.6);
    border-color: var(--cnc-magenta-light);
}

/* ============================================================================
   RESPONSIVE DESIGN
   ============================================================================ */

@media (max-width: 1024px) {
    .cnc-footer__main {
        grid-template-columns: repeat(2, 1fr);
        gap: 2.5rem;
        padding: 4rem 1.5rem 2.5rem;
    }
    
    .cnc-footer__brand {
        padding-right: 0;
    }
}

@media (max-width: 768px) {
    .cnc-footer__main {
        grid-template-columns: 1fr;
        gap: 2rem;
        padding: 3rem 1.5rem 2rem;
    }
    
    .cnc-footer__logo {
        max-width: 180px;
    }
    
    .cnc-footer__social-link {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .cnc-footer__bottom-wrap {
        flex-direction: column;
        text-align: center;
        padding: 1.25rem 1.5rem;
    }
    
    .cnc-footer__legal {
        justify-content: center;
    }
    
    .cnc-back-to-top {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }
}

@media (max-width: 480px) {
    .cnc-footer__brand-title {
        font-size: 1.5rem;
    }
    
    .cnc-footer__tagline {
        font-size: 0.9rem;
    }
    
    .cnc-footer__legal {
        flex-direction: column;
        gap: 0.75rem;
    }
}

/* ============================================================================
   LOADING ANIMATION
   ============================================================================ */

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cnc-footer {
    animation: fadeInUp 0.8s ease-out;
}
</style>

<!-- Footer Section -->
<footer class="cnc-footer" role="contentinfo">
    
    <!-- Main Footer Content -->
    <div class="cnc-footer__main">
        
        <!-- Brand & About Column -->
        <div class="cnc-footer__brand">
            <?php 
            // Try to load logo with fallback
            $logo_paths = array(
                '/assets/cnc-logov3.png',
                '/assets/img/cnc-logo-white.png',
                '/assets/img/cnc-logo.png'
            );
            
            $logo_found = false;
            foreach ($logo_paths as $logo_path) {
                if (file_exists(get_stylesheet_directory() . $logo_path)) {
                    echo '<img src="' . esc_url(get_stylesheet_directory_uri() . $logo_path) . '" alt="Cable Net Convergence Expo Hyderabad" class="cnc-footer__logo">';
                    $logo_found = true;
                    break;
                }
            }
            
            // Fallback text logo
            if (!$logo_found):
            ?>
                <h2 class="cnc-footer__brand-title">
                    Cable Net Convergence <span class="cnc-footer__brand-highlight">EXPO</span>
                </h2>
            <?php endif; ?>
            
            <p class="cnc-footer__tagline">
                India's Premier Cable, Internet & Broadband Technology Exhibition. 
                Connecting innovation, technology, and the future of connectivity.
            </p>
            
            <div class="cnc-footer__event-info">
                <div class="cnc-footer__event-label">Next Event</div>
                <div class="cnc-footer__event-date">August 13-15, 2026 | HITEX, Hyderabad</div>
            </div>
            
            <div class="cnc-footer__social">
                <a href="https://www.facebook.com/CNCExpo/" target="_blank" rel="noopener" class="cnc-footer__social-link" aria-label="Facebook">
                    <span>f</span>
                </a>
                <a href="https://twitter.com/CNCExpo" target="_blank" rel="noopener" class="cnc-footer__social-link" aria-label="Twitter">
                    <span>𝕏</span>
                </a>
                <a href="https://www.linkedin.com/company/CNCExpo/" target="_blank" rel="noopener" class="cnc-footer__social-link" aria-label="LinkedIn">
                    <span>in</span>
                </a>
                <a href="https://www.instagram.com/CNCExpo/" target="_blank" rel="noopener" class="cnc-footer__social-link" aria-label="Instagram">
                    <span>📷</span>
                </a>
                <a href="https://www.youtube.com/@cncexpo" target="_blank" rel="noopener" class="cnc-footer__social-link" aria-label="YouTube">
                    <span>▶</span>
                </a>
            </div>
        </div>
        
        <!-- Quick Links Column -->
        <div class="cnc-footer__column">
            <h3 class="cnc-footer__section-title">Quick Links</h3>
            <ul class="cnc-footer__links">
                <li><a href="<?php echo esc_url(home_url('/')); ?>" class="cnc-footer__link">Home</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="cnc-footer__link">About Us</a></li>
                <li><a href="<?php echo esc_url(home_url('/exhibitors')); ?>" class="cnc-footer__link">Exhibitors</a></li>
                <li><a href="<?php echo esc_url(home_url('/visitors')); ?>" class="cnc-footer__link">Visitors</a></li>
                <li><a href="<?php echo esc_url(home_url('/floorplan')); ?>" class="cnc-footer__link">Floor Plan</a></li>
                <li><a href="<?php echo esc_url(home_url('/sponsors')); ?>" class="cnc-footer__link">Sponsors</a></li>
            </ul>
        </div>
        
        <!-- Resources Column -->
        <div class="cnc-footer__column">
            <h3 class="cnc-footer__section-title">Resources</h3>
            <ul class="cnc-footer__links">
                <li><a href="https://cnc.tst-india.com" target="_blank" rel="noopener" class="cnc-footer__link">Register</a></li>
                <li><a href="<?php echo esc_url(home_url('/space-booking')); ?>" class="cnc-footer__link">Book Space</a></li>
                <li><a href="<?php echo esc_url(home_url('/gallery')); ?>" class="cnc-footer__link">Gallery</a></li>
                <li><a href="<?php echo esc_url(home_url('/media')); ?>" class="cnc-footer__link">Media Center</a></li>
                <li><a href="<?php echo esc_url(home_url('/testimonials')); ?>" class="cnc-footer__link">Testimonials</a></li>
                <li><a href="<?php echo esc_url(home_url('/faq')); ?>" class="cnc-footer__link">FAQ</a></li>
            </ul>
        </div>
        
        <!-- Contact Column -->
        <div class="cnc-footer__column">
            <h3 class="cnc-footer__section-title">Contact Us</h3>
            
            <div class="cnc-footer__contact-item">
                <div class="cnc-footer__contact-label">📍 Venue</div>
                <div class="cnc-footer__contact-value">
                    HITEX Exhibition Center<br>
                    Hyderabad, Telangana<br>
                    India
                </div>
            </div>
            
            <div class="cnc-footer__contact-item">
                <div class="cnc-footer__contact-label">📞 Phone</div>
                <div class="cnc-footer__contact-value">
                    <a href="tel:+919505050007" class="cnc-footer__contact-link">+91 9505 050 007</a>
                </div>
            </div>
            
            <div class="cnc-footer__contact-item">
                <div class="cnc-footer__contact-label">✉️ Email</div>
                <div class="cnc-footer__contact-value">
                    <a href="mailto:info@cncexpo.com" class="cnc-footer__contact-link">info@cncexpo.com</a>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Footer Bottom Bar -->
    <div class="cnc-footer__bottom">
        <div class="cnc-footer__bottom-wrap">
            <div class="cnc-footer__copyright">
                &copy; <?php echo date('Y'); ?> Cable Net Convergence Expo Hyderabad. All rights reserved.
            </div>
            <nav class="cnc-footer__legal" aria-label="Legal">
                <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" class="cnc-footer__legal-link">Privacy Policy</a>
                <a href="<?php echo esc_url(home_url('/terms-conditions')); ?>" class="cnc-footer__legal-link">Terms & Conditions</a>
                <a href="<?php echo esc_url(home_url('/cookie-policy')); ?>" class="cnc-footer__legal-link">Cookie Policy</a>
            </nav>
            <div class="cnc-footer__credits">
                Developed by <a href="https://highwaterdigital.com" target="_blank" rel="noopener" class="cnc-footer__credits-link">HighWater Digital</a>
            </div>
        </div>
    </div>
    
</footer>

<!-- Back to Top Button -->
<a href="#" class="cnc-back-to-top" id="cnc-back-to-top" aria-label="Back to Top">
    ↑
</a>

<script>
/**
 * Footer JavaScript - Back to Top & Animations
 * CNC Expo Hyderabad 2026 */
(function() {
    'use strict';
    
    console.log('👣 CNC Footer: JavaScript initialized');
    
    // Back to Top Button
    const backToTop = document.getElementById('cnc-back-to-top');
    
    if (backToTop) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        console.log('✅ Back to top button initialized');
    }
    
    // Animate footer elements on scroll into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const footerObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                footerObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe footer columns
    const footerColumns = document.querySelectorAll('.cnc-footer__column, .cnc-footer__brand');
    footerColumns.forEach(column => {
        footerObserver.observe(column);
    });
    
    console.log('✅ CNC Footer: All features loaded successfully');
})();
</script>

<?php 
if (function_exists('wp_footer')) {
    wp_footer(); 
}
?>
</body>
</html>
