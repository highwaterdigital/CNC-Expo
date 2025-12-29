<?php
/**
 * CNC Home Page Shortcode - Complete Rewrite v5.0
 * Based on Convergence India Design Reference
 * Modern Color Palette: Magenta (#C6308C), Purple (#5E3A8E), Blue (#0090D9)
 * 
 * @package cnc_child
 * @version 5.0.0
 * @author CNC Expo Development Team
 * @since 2025-01-11
 */

if (!defined('ABSPATH')) exit;

error_log('🏠 CNC v5.0: Complete home page rewrite loading...');

ob_start();
?>

<style>
/* ============================================================================
   CNC EXPO HYDERABAD 2025 - DESIGN SYSTEM v5.0
   Complete Rewrite Based on Convergence India Reference
   ============================================================================ */

:root {
    /* === BRAND COLORS === */
    --cnc-magenta: #C6308C;
    --cnc-magenta-dark: #A02773;
    --cnc-magenta-light: #E94FAF;
    --cnc-purple: #5E3A8E;
    --cnc-purple-dark: #4A2E6F;
    --cnc-blue: #0090D9;
    --cnc-blue-dark: #007AB8;
    --cnc-teal: #00B4B4;
    --cnc-gold: #FFC107;
    --cnc-lime: #CDFF00;
    
    /* === NEUTRALS === */
    --cnc-black: #0D0D0D;
    --cnc-white: #FFFFFF;
    --cnc-gray-light: #F8F9FA;
    --cnc-gray: #E9ECEF;
    --cnc-gray-dark: #6C757D;
    --cnc-dark: #1A0B2E;
    
    /* === GRADIENTS === */
    --gradient-primary: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    --gradient-secondary: linear-gradient(135deg, #0090D9 0%, #00B4B4 100%);
    --gradient-dark: linear-gradient(135deg, #1A0B2E 0%, #16213E 100%);
    --gradient-lime: linear-gradient(135deg, #CDFF00 0%, #A8E600 100%);
    
    /* === DESIGN TOKENS === */
    --cnc-radius: 5px;
    --cnc-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    --cnc-shadow-lg: 0 20px 60px rgba(198, 48, 140, 0.3);
    --cnc-transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* === TYPOGRAPHY === */
    --font-primary: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-secondary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* === GLOBAL RESET === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.cnc-home {
    width: 100vw;
    max-width: 100vw;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    font-family: var(--font-secondary);
    line-height: 1.6;
    color: var(--cnc-black);
    background: var(--cnc-white);
}

/* ============================================================================
   HERO SECTION - FULL VIEWPORT WITH VIDEO
   ============================================================================ */

.cnc-hero {
    min-height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: var(--gradient-dark);
    padding: 2rem 1rem;
}

/* Video Background */
.cnc-hero__video-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.cnc-hero__video-bg iframe {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100vw;
    height: 56.25vw;
    min-height: 100vh;
    min-width: 177.77vh;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

/* Gradient Overlay */
.cnc-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, 
        rgba(26, 11, 46, 0.85) 0%, 
        rgba(94, 58, 142, 0.75) 50%,
        rgba(198, 48, 140, 0.65) 100%
    );
    z-index: 1;
}

/* Animated Grid Pattern */
.cnc-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: gridMove 20s linear infinite;
    z-index: 1;
}

@keyframes gridMove {
    0% { transform: translate(0, 0); }
    100% { transform: translate(50px, 50px); }
}

.cnc-hero__content {
    max-width: 1200px;
    text-align: center;
    position: relative;
    z-index: 2;
    animation: heroFadeIn 1.2s ease-out;
    padding: 2rem;
}

@keyframes heroFadeIn {
    from {
        opacity: 0;
        transform: translateY(60px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.cnc-hero__badge {
    display: inline-block;
    background: var(--gradient-lime);
    color: var(--cnc-black);
    padding: 10px 30px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 2rem;
    box-shadow: 0 6px 20px rgba(205, 255, 0, 0.4);
}

.cnc-hero h1 {
    font-family: var(--font-primary);
    font-size: clamp(2.5rem, 7vw, 5.5rem);
    font-weight: 900;
    color: var(--cnc-white);
    margin-bottom: 1.5rem;
    line-height: 1.1;
    letter-spacing: -2px;
    text-shadow: 4px 4px 20px rgba(0, 0, 0, 0.8);
}

.cnc-hero h1 span {
    background: var(--gradient-lime);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cnc-hero__subtitle {
    font-size: clamp(1.3rem, 3vw, 2.2rem);
    color: var(--cnc-white);
    margin-bottom: 1rem;
    font-weight: 600;
    opacity: 0.95;
    text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.6);
}

.cnc-hero__tagline {
    font-size: clamp(1rem, 2vw, 1.5rem);
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 3rem;
    line-height: 1.6;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

/* Event Details Grid */
.cnc-hero__details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin: 3rem 0;
    padding: 2.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: var(--cnc-radius);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.cnc-hero__detail {
    text-align: center;
}

.cnc-hero__detail-icon {
    font-size: 3rem;
    display: block;
    margin-bottom: 1rem;
    filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.5));
}

.cnc-hero__detail h3 {
    font-size: 0.9rem;
    color: var(--cnc-lime);
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
}

.cnc-hero__detail p {
    font-size: 1.4rem;
    color: var(--cnc-white);
    font-weight: 800;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
}

/* CTA Buttons */
.cnc-hero__actions {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 2rem;
}

.cnc-btn {
    display: inline-block;
    padding: 18px 40px;
    font-family: var(--font-primary);
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-decoration: none;
    border-radius: 5px;
    transition: var(--cnc-transition);
    border: 3px solid transparent;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.cnc-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.cnc-btn:hover::before {
    width: 400px;
    height: 400px;
}

.cnc-btn--primary {
    background: var(--gradient-lime);
    color: var(--cnc-black);
    box-shadow: 0 8px 30px rgba(205, 255, 0, 0.4);
}

.cnc-btn--primary:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(205, 255, 0, 0.6);
}

.cnc-btn--secondary {
    background: transparent;
    color: var(--cnc-white);
    border-color: var(--cnc-white);
}

.cnc-btn--secondary:hover {
    background: var(--cnc-white);
    color: var(--cnc-magenta);
    transform: translateY(-5px);
}

/* ============================================================================
   INTRO SECTION - ABOUT THE EXPO
   ============================================================================ */

.cnc-intro {
    padding: 8rem 2rem;
    background: var(--cnc-white);
    position: relative;
}

.cnc-intro__wrap {
    max-width: 1400px;
    margin: 0 auto;
}

.cnc-intro__content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.cnc-intro__label {
    display: inline-block;
    background: var(--gradient-primary);
    color: var(--cnc-white);
    padding: 8px 24px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 2rem;
}

.cnc-intro h2 {
    font-family: var(--font-primary);
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    color: var(--cnc-black);
    margin-bottom: 2rem;
    line-height: 1.2;
    letter-spacing: -1px;
}

.cnc-intro h2 span {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cnc-intro__text {
    font-size: 1.25rem;
    line-height: 1.9;
    color: var(--cnc-gray-dark);
    margin-bottom: 3rem;
}

.cnc-intro--icons {
    padding-bottom: 5rem;
}
.cnc-intro__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    justify-items: center;
    margin-top: 1.5rem;
}
.cnc-intro__pill {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    background: #ffffff;
    border: 1px solid rgba(10, 55, 46, 0.1);
    border-radius: 999px;
    padding: 0.65rem 1.3rem;
    font-weight: 600;
    color: #0A372E;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}
.cnc-intro__icon {
    font-family: 'Roboto Mono', monospace;
    font-size: 1rem;
    color: #0A372E;
}

/* ============================================================================
   BROCHURE SECTION - DOWNLOAD CTA
   ============================================================================ */

.cnc-brochure {
    padding: 8rem 2rem;
    background: linear-gradient(180deg, var(--cnc-white) 0%, #f9f7fb 100%);
}

.cnc-brochure__wrap {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    align-items: center;
    gap: 3rem;
}

.cnc-brochure__content {
    max-width: 680px;
}

.cnc-brochure__eyebrow {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(198, 48, 140, 0.1);
    color: var(--cnc-magenta);
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.cnc-brochure h2 {
    font-family: var(--font-primary);
    font-size: clamp(2.2rem, 4vw, 3.4rem);
    line-height: 1.15;
    margin-bottom: 1.5rem;
    color: var(--cnc-black);
}

.cnc-brochure__lead {
    font-size: 1.15rem;
    color: var(--cnc-gray-dark);
    margin-bottom: 1.5rem;
}

.cnc-brochure__points {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
    display: grid;
    gap: 0.9rem;
}

.cnc-brochure__points li {
    position: relative;
    padding-left: 1.5rem;
    color: var(--cnc-black);
    font-weight: 600;
}

.cnc-brochure__points li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65rem;
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: var(--gradient-primary);
    transform: translateY(-50%);
}

.cnc-brochure__cta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.cnc-brochure__cta .cnc-btn {
    border-color: var(--cnc-magenta);
    color: var(--cnc-magenta);
    background: transparent;
}

.cnc-brochure__cta .cnc-btn:hover {
    background: var(--gradient-primary);
    color: var(--cnc-white);
    box-shadow: 0 12px 30px rgba(198, 48, 140, 0.35);
}

.cnc-brochure__media img {
    width: 100%;
    display: block;
    border-radius: 18px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
    object-fit: cover;
}

@media (max-width: 768px) {
    .cnc-brochure__wrap {
        grid-template-columns: 1fr;
    }

    .cnc-brochure__content {
        order: 2;
    }

    .cnc-brochure__media {
        order: 1;
    }
}

/* ============================================================================
   HIGHLIGHTS SECTION - KEY FEATURES GRID
   ============================================================================ */

.cnc-highlights {
    padding: 8rem 2rem;
    background: linear-gradient(180deg, var(--cnc-white) 0%, var(--cnc-gray-light) 100%);
}

.cnc-highlights__wrap {
    max-width: 1400px;
    margin: 0 auto;
}

.cnc-section-header {
    text-align: center;
    margin-bottom: 5rem;
}

.cnc-section-header__label {
    display: inline-block;
    background: var(--gradient-secondary);
    color: var(--cnc-white);
    padding: 8px 24px;
    border-radius: 5px;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 1.5rem;
}

.cnc-section-header h2 {
    font-family: var(--font-primary);
    font-size: clamp(2.5rem, 5vw, 3.5rem);
    font-weight: 800;
    color: var(--cnc-black);
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.cnc-section-header p {
    font-size: 1.2rem;
    color: var(--cnc-gray-dark);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.8;
}

.cnc-highlights__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 3rem;
}

.cnc-highlight-card {
    background: var(--cnc-white);
    padding: 3rem 2.5rem;
    border-radius: var(--cnc-radius);
    text-align: center;
    box-shadow: var(--cnc-shadow);
    transition: var(--cnc-transition);
    border: 3px solid transparent;
    position: relative;
    overflow: hidden;
}

.cnc-highlight-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: var(--gradient-primary);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.cnc-highlight-card:hover::before {
    transform: scaleX(1);
}

.cnc-highlight-card:hover {
    transform: translateY(-15px);
    box-shadow: var(--cnc-shadow-lg);
    border-color: var(--cnc-magenta);
}

.cnc-highlight-card__icon {
    width: 100px;
    height: 100px;
    background: var(--gradient-primary);
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    font-size: 3rem;
    color: var(--cnc-white);
    box-shadow: 0 10px 30px rgba(198, 48, 140, 0.4);
    transition: var(--cnc-transition);
}

.cnc-highlight-card:hover .cnc-highlight-card__icon {
    transform: rotateY(360deg);
}

.cnc-highlight-card h3 {
    font-family: var(--font-primary);
    font-size: 1.75rem;
    color: var(--cnc-black);
    margin-bottom: 1.25rem;
    font-weight: 700;
}

.cnc-highlight-card p {
    color: var(--cnc-gray-dark);
    line-height: 1.8;
    font-size: 1.05rem;
}

/* ============================================================================
   STATS SECTION - IMPRESSIVE NUMBERS
   ============================================================================ */

.cnc-stats {
    padding: 8rem 2rem;
    background: var(--gradient-dark);
    color: var(--cnc-white);
    position: relative;
    overflow: hidden;
}

.cnc-stats::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--gradient-lime);
}

.cnc-stats__wrap {
    max-width: 1400px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.cnc-stats__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 4rem;
}

.cnc-stat {
    text-align: center;
    padding: 2.5rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--cnc-radius);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.1);
    transition: var(--cnc-transition);
}

.cnc-stat:hover {
    background: rgba(198, 48, 140, 0.2);
    border-color: var(--cnc-lime);
    transform: translateY(-10px);
}

.cnc-stat__icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    display: block;
    filter: drop-shadow(0 4px 15px rgba(205, 255, 0, 0.4));
}

.cnc-stat__number {
    font-family: var(--font-primary);
    font-size: 4.5rem;
    font-weight: 900;
    color: var(--cnc-lime);
    margin-bottom: 1rem;
    display: block;
    line-height: 1;
}

.cnc-stat__label {
    font-size: 1.2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.95;
}

/* ============================================================================
   CO-LOCATED EXPOS SECTION
   ============================================================================ */

.cnc-colocated {
    padding: 8rem 2rem;
    background: var(--cnc-white);
}

.cnc-colocated__wrap {
    max-width: 1400px;
    margin: 0 auto;
}

.cnc-colocated__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
    margin-top: 4rem;
}

.cnc-colocated-card {
    position: relative;
    border-radius: var(--cnc-radius);
    overflow: hidden;
    box-shadow: var(--cnc-shadow);
    transition: var(--cnc-transition);
    height: 400px;
}

.cnc-colocated-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--cnc-shadow-lg);
}

.cnc-colocated-card__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--cnc-transition);
}

.cnc-colocated-card:hover .cnc-colocated-card__image {
    transform: scale(1.1);
}

.cnc-colocated-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(26, 11, 46, 0.95) 0%, transparent 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 2.5rem;
    transition: var(--cnc-transition);
}

.cnc-colocated-card:hover .cnc-colocated-card__overlay {
    background: linear-gradient(to top, rgba(198, 48, 140, 0.95) 0%, rgba(94, 58, 142, 0.8) 100%);
}

.cnc-colocated-card__logo {
    width: 120px;
    height: 120px;
    background: var(--cnc-white);
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cnc-colocated-card h3 {
    font-family: var(--font-primary);
    font-size: 1.8rem;
    color: var(--cnc-white);
    margin-bottom: 1rem;
    font-weight: 700;
}

.cnc-colocated-card p {
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.6;
}

/* ============================================================================
   EXHIBITORS SECTION
   ============================================================================ */

.cnc-exhibitors {
    padding: 8rem 2rem;
    background: var(--gradient-dark);
    color: var(--cnc-white);
}

.cnc-exhibitors__wrap {
    max-width: 1400px;
    margin: 0 auto;
}

.cnc-exhibitors__content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6rem;
    align-items: center;
    margin-top: 4rem;
}

.cnc-exhibitors__image {
    position: relative;
    border-radius: var(--cnc-radius);
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.cnc-exhibitors__image img {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.cnc-exhibitors__info h3 {
    font-family: var(--font-primary);
    font-size: 2.5rem;
    margin-bottom: 2rem;
    font-weight: 800;
    line-height: 1.3;
}

.cnc-exhibitors__info h3 span {
    color: var(--cnc-lime);
}

.cnc-exhibitors__features {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    margin-bottom: 3rem;
}

.cnc-exhibitors__feature {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.cnc-exhibitors__feature-icon {
    width: 50px;
    height: 50px;
    background: var(--gradient-lime);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.cnc-exhibitors__feature-text h4 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.cnc-exhibitors__feature-text p {
    font-size: 0.95rem;
    opacity: 0.9;
    line-height: 1.6;
}

/* ============================================================================
   CTA SECTION - CALL TO ACTION
   ============================================================================ */

.cnc-cta {
    padding: 8rem 2rem;
    background: var(--gradient-primary);
    color: var(--cnc-white);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cnc-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 30px,
        rgba(255, 255, 255, 0.05) 30px,
        rgba(255, 255, 255, 0.05) 60px
    );
    animation: patternMove 30s linear infinite;
}

@keyframes patternMove {
    0% { transform: translate(0, 0); }
    100% { transform: translate(60px, 60px); }
}

.cnc-cta__wrap {
    max-width: 900px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.cnc-cta h2 {
    font-family: var(--font-primary);
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: 900;
    margin-bottom: 2rem;
    line-height: 1.2;
}

.cnc-cta p {
    font-size: 1.5rem;
    margin-bottom: 3rem;
    opacity: 0.95;
    line-height: 1.6;
}

.cnc-cta__actions {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* ============================================================================
   FOOTER CTA STRIP
   ============================================================================ */

.cnc-footer-cta {
    background: var(--cnc-lime);
    color: var(--cnc-black);
    padding: 3rem 2rem;
    text-align: center;
}

.cnc-footer-cta__wrap {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.cnc-footer-cta h3 {
    font-family: var(--font-primary);
    font-size: 2rem;
    font-weight: 800;
}

.cnc-footer-cta__buttons {
    display: flex;
    gap: 1rem;
}

/* ============================================================================
   RESPONSIVE DESIGN
   ============================================================================ */

@media (max-width: 1024px) {
    .cnc-exhibitors__content {
        grid-template-columns: 1fr;
        gap: 4rem;
    }
    
    .cnc-exhibitors__features {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .cnc-hero__details {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 2rem;
    }
    
    .cnc-hero__actions,
    .cnc-cta__actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .cnc-btn {
        width: 100%;
        max-width: 400px;
    }
    
    .cnc-highlights__grid,
    .cnc-colocated__grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .cnc-stats__grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
    
    .cnc-footer-cta__wrap {
        flex-direction: column;
        text-align: center;
    }
    
    .cnc-footer-cta__buttons {
        width: 100%;
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .cnc-stats__grid {
        grid-template-columns: 1fr;
    }
    
    .cnc-stat__number {
        font-size: 3rem;
    }
    
    .cnc-intro,
    .cnc-highlights,
    .cnc-stats,
    .cnc-colocated,
    .cnc-exhibitors,
    .cnc-cta {
        padding: 5rem 1.5rem;
    }
}
</style>

<!-- ============================================================================
     HTML CONTENT STARTS HERE
     ============================================================================ -->

<div class="cnc-home">
    
    <!-- HERO SECTION -->
    <section class="cnc-hero">
        <div class="cnc-hero__video-bg">
            <iframe 
                src="https://www.youtube.com/embed/Oi3k5PFOq90?autoplay=1&mute=1&loop=1&playlist=Oi3k5PFOq90&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&enablejsapi=1" 
                frameborder="0" 
                allow="autoplay; encrypted-media" 
                allowfullscreen>
            </iframe>
        </div>
        
        <div class="cnc-hero__content">
            <span class="cnc-hero__badge">August 14-16, 2026</span>
            
            <h1>
                INDIA'S<br>
                <span>BIGGEST</span><br>
                TECHNOLOGY EXPO
            </h1>
            
            <p class="cnc-hero__subtitle">Cable & Internet Expo Hyderabad 2025</p>
            <p class="cnc-hero__tagline">
                The Most Comprehensive Technology Showcase of the Year - Where Innovation Meets Opportunity
            </p>
            
            <div class="cnc-hero__details">
                <div class="cnc-hero__detail">
                    <span class="cnc-hero__detail-icon">📅</span>
                    <h3>Event Dates</h3>
                    <p>August 14-16, 2026</p>
                </div>
                <div class="cnc-hero__detail">
                    <span class="cnc-hero__detail-icon">📍</span>
                    <h3>Venue</h3>
                    <p>HITEX, Hyderabad</p>
                </div>
                <div class="cnc-hero__detail">
                    <span class="cnc-hero__detail-icon">🏢</span>
                    <h3>Exhibitors</h3>
                    <p>200+ Brands</p>
                </div>
                <div class="cnc-hero__detail">
                    <span class="cnc-hero__detail-icon">👥</span>
                    <h3>Expected Visitors</h3>
                    <p>25,000+</p>
                </div>
            </div>
            
            <div class="cnc-hero__actions">
                <a href="https://cnc.tst-india.com" target="_blank" rel="noopener" class="cnc-btn cnc-btn--primary">
                    Register Your Visit
                </a>
                <a href="<?php echo esc_url(home_url('/space-booking')); ?>" class="cnc-btn cnc-btn--secondary">
                    Book Exhibition Space
                </a>
            </div>
        </div>
    </section>

    <!-- INTRO SECTION -->
    <section class="cnc-intro cnc-intro--icons">
        <div class="cnc-intro__wrap">
            <div class="cnc-intro__content">
                <span class="cnc-intro__label">Connected Technologies</span>
                <h2>The Building Blocks of Cable Net Convergence 2025</h2>
                <p class="cnc-intro__text">
                    Discover the ecosystems powering modern connectivity, media, and automation. These pillars form the heartbeat of the Cable Net Convergence experience.
                </p>
            </div>
            <div class="cnc-intro__grid">
                <?php
                $showcase = [
                    ['icon' => '📡', 'label' => 'Broadband'],
                    ['icon' => '📺', 'label' => 'Cable TV'],
                    ['icon' => '☁️', 'label' => 'OTT'],
                    ['icon' => '🌐', 'label' => 'FTTH'],
                    ['icon' => '🔊', 'label' => 'Broadcast'],
                    ['icon' => '📱', 'label' => 'Mobiles'],
                    ['icon' => '🛰️', 'label' => 'IOT'],
                    ['icon' => '📺', 'label' => 'IPTV'],
                    ['icon' => '🎯', 'label' => 'CC Camera'],
                    ['icon' => '🛡️', 'label' => 'Security Solutions'],
                    ['icon' => '🏠', 'label' => 'Home Automation'],
                    ['icon' => '💻', 'label' => 'ICT'],
                ];
                foreach ($showcase as $item) :
                    ?>
                    <div class="cnc-intro__pill">
                        <span class="cnc-intro__icon"><?php echo esc_html($item['icon']); ?></span>
                        <span><?php echo esc_html($item['label']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BROCHURE DOWNLOAD -->
    <section class="cnc-brochure" id="cnc-brochure">
        <div class="cnc-brochure__wrap">
            <div class="cnc-brochure__content">
                <span class="cnc-brochure__eyebrow">Download the 2026 brochure</span>
                <h2>Cable, Broadband, OTT & Security in one definitive guide</h2>
                <p class="cnc-brochure__lead">
                    Get the latest floor plan, audience breakdown, and sponsorship inventory for the 2026 Cable & Internet Expo Hyderabad.
                </p>
                <ul class="cnc-brochure__points">
                    <li>Converged coverage: Cable TV, broadband, OTT, FTTH, IPTV, mobile networks, security, broadcast, and content workflows.</li>
                    <li>What’s inside: product showcases, feature zones, conference themes, hosted buyer programs, and branding opportunities.</li>
                    <li>Who attends: MSOs, LCOs, ISPs, broadcasters, telcos, content owners, and enterprise network leaders.</li>
                </ul>
                <div class="cnc-brochure__cta">
                    <a href="https://cablenetconvergence.com/wp-content/uploads/2025/10/CNC-Brochure-v2.pdf" target="_blank" rel="noopener" class="cnc-btn">
                        Download Brochure
                    </a>
                </div>
            </div>
            <div class="cnc-brochure__media">
                <img src="https://images.unsplash.com/photo-1551836022-4c4c79ecde51?auto=format&fit=crop&w=1200&q=80" alt="Attendees exploring solutions at CNC Expo" loading="lazy">
            </div>
        </div>
    </section>

    <!-- HIGHLIGHTS SECTION -->
    <section class="cnc-highlights">
        <div class="cnc-highlights__wrap">
            <div class="cnc-section-header">
                <span class="cnc-section-header__label">Why Attend</span>
                <h2>Key Highlights</h2>
                <p>Discover what makes CNC Expo India's most influential technology exhibition</p>
            </div>
            
            <div class="cnc-highlights__grid">
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">🚀</div>
                    <h3>Latest Technology</h3>
                    <p>Explore cutting-edge innovations from leading global manufacturers and technology providers</p>
                </div>
                
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">🤝</div>
                    <h3>Networking</h3>
                    <p>Connect with industry leaders, potential partners, and technology experts from across India</p>
                </div>
                
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">📈</div>
                    <h3>Business Growth</h3>
                    <p>Discover new business opportunities, partnerships, and market insights that drive growth</p>
                </div>
                
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">🎓</div>
                    <h3>Knowledge Sessions</h3>
                    <p>Attend seminars, workshops, and panel discussions led by industry thought leaders</p>
                </div>
                
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">🎯</div>
                    <h3>Live Demonstrations</h3>
                    <p>Experience hands-on product demos and witness the latest technology in action</p>
                </div>
                
                <div class="cnc-highlight-card">
                    <div class="cnc-highlight-card__icon">🌐</div>
                    <h3>Global Brands</h3>
                    <p>Meet with international and domestic brands showcasing innovative solutions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section class="cnc-stats">
        <div class="cnc-stats__wrap">
            <div class="cnc-section-header">
                <span class="cnc-section-header__label" style="background: var(--gradient-lime); color: var(--cnc-black);">By The Numbers</span>
                <h2 style="color: var(--cnc-white);">CNC Expo in Numbers</h2>
            </div>
            
            <div class="cnc-stats__grid">
                <div class="cnc-stat">
                    <span class="cnc-stat__icon">🏢</span>
                    <span class="cnc-stat__number" data-target="200">0</span>
                    <span class="cnc-stat__label">Exhibitors</span>
                </div>
                
                <div class="cnc-stat">
                    <span class="cnc-stat__icon">👥</span>
                    <span class="cnc-stat__number" data-target="25000">0</span>
                    <span class="cnc-stat__label">Visitors</span>
                </div>
                
                <div class="cnc-stat">
                    <span class="cnc-stat__icon">🌐</span>
                    <span class="cnc-stat__number" data-target="15">0</span>
                    <span class="cnc-stat__label">Countries</span>
                </div>
                
                <div class="cnc-stat">
                    <span class="cnc-stat__icon">📏</span>
                    <span class="cnc-stat__number" data-target="50">0</span>
                    <span class="cnc-stat__label">Sqft (1000s)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CO-LOCATED EXPOS -->
    <section class="cnc-colocated">
        <div class="cnc-colocated__wrap">
            <div class="cnc-section-header">
                <span class="cnc-section-header__label">Co-Located Events</span>
                <h2>Multiple Expos, One Destination</h2>
                <p>Experience various technology sectors converging at one premier venue</p>
            </div>
            
            <div class="cnc-colocated__grid">
                <div class="cnc-colocated-card">
                    <img src="https://via.placeholder.com/400x400?text=Cable+%26+Satellite" alt="Cable & Satellite" class="cnc-colocated-card__image">
                    <div class="cnc-colocated-card__overlay">
                        <div class="cnc-colocated-card__logo">
                            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/cnc-logo.png'); ?>" alt="CNC">
                        </div>
                        <h3>Cable & Satellite</h3>
                        <p>Broadcast & distribution technologies</p>
                    </div>
                </div>
                
                <div class="cnc-colocated-card">
                    <img src="https://via.placeholder.com/400x400?text=Internet+%26+Broadband" alt="Internet" class="cnc-colocated-card__image">
                    <div class="cnc-colocated-card__overlay">
                        <h3>Internet & Broadband</h3>
                        <p>Next-gen connectivity solutions</p>
                    </div>
                </div>
                
                <div class="cnc-colocated-card">
                    <img src="https://via.placeholder.com/400x400?text=Digital+Infrastructure" alt="Digital" class="cnc-colocated-card__image">
                    <div class="cnc-colocated-card__overlay">
                        <h3>Digital Infrastructure</h3>
                        <p>Smart city & IoT technologies</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- EXHIBITORS SECTION -->
    <section class="cnc-exhibitors">
        <div class="cnc-exhibitors__wrap">
            <div class="cnc-section-header">
                <span class="cnc-section-header__label" style="background: var(--gradient-lime); color: var(--cnc-black);">For Exhibitors</span>
                <h2 style="color: var(--cnc-white);">Position Your Brand Across<br>Dedicated Product Sectors</h2>
            </div>
            
            <div class="cnc-exhibitors__content">
                <div class="cnc-exhibitors__image">
                    <img src="https://via.placeholder.com/600x500?text=Exhibition+Floor" alt="Exhibition">
                </div>
                
                <div class="cnc-exhibitors__info">
                    <h3>
                        Showcase Your<br>
                        <span>Innovations</span> to<br>
                        India's Tech Leaders
                    </h3>
                    
                    <div class="cnc-exhibitors__features">
                        <div class="cnc-exhibitors__feature">
                            <div class="cnc-exhibitors__feature-icon">🎯</div>
                            <div class="cnc-exhibitors__feature-text">
                                <h4>Targeted Audience</h4>
                                <p>Reach decision-makers from telecom, MSOs, and ISPs</p>
                            </div>
                        </div>
                        
                        <div class="cnc-exhibitors__feature">
                            <div class="cnc-exhibitors__feature-icon">📊</div>
                            <div class="cnc-exhibitors__feature-text">
                                <h4>Market Insights</h4>
                                <p>Gain valuable industry intelligence and trends</p>
                            </div>
                        </div>
                        
                        <div class="cnc-exhibitors__feature">
                            <div class="cnc-exhibitors__feature-icon">🤝</div>
                            <div class="cnc-exhibitors__feature-text">
                                <h4>Partnership Opportunities</h4>
                                <p>Network with potential partners and distributors</p>
                            </div>
                        </div>
                        
                        <div class="cnc-exhibitors__feature">
                            <div class="cnc-exhibitors__feature-icon">🚀</div>
                            <div class="cnc-exhibitors__feature-text">
                                <h4>Product Launches</h4>
                                <p>Perfect platform for new product introductions</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="<?php echo esc_url(home_url('/space-booking')); ?>" class="cnc-btn cnc-btn--primary">
                        Book Your Space Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cnc-cta">
        <div class="cnc-cta__wrap">
            <h2>Ready to Join India's<br>Biggest Tech Expo?</h2>
            <p>Don't miss this opportunity to connect with 25,000+ industry professionals</p>
            
            <div class="cnc-cta__actions">
                <a href="https://cnc.tst-india.com" target="_blank" rel="noopener" class="cnc-btn cnc-btn--primary">
                    Register as Visitor
                </a>
                <a href="<?php echo esc_url(home_url('/space-booking')); ?>" class="cnc-btn cnc-btn--secondary">
                    Book Exhibition Space
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER CTA STRIP -->
    <div class="cnc-footer-cta">
        <div class="cnc-footer-cta__wrap">
            <h3>🎯 Early Bird Registration Now Open!</h3>
            <div class="cnc-footer-cta__buttons">
                <a href="https://cnc.tst-india.com" target="_blank" rel="noopener" class="cnc-btn cnc-btn--primary">
                    Register Free
                </a>
            </div>
        </div>
    </div>

</div>

<script>
/**
 * CNC Home Page JavaScript - v5.0
 * Handles animations and interactive elements
 */
(function() {
    'use strict';
    
    console.log('🎯 CNC v5.0: JavaScript initialized');
    
    // Counter Animation
    const animateCounters = () => {
        const counters = document.querySelectorAll('.cnc-stat__number');
        
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };
        
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    const duration = 2000;
                    const increment = target / (duration / 16);
                    let current = 0;
                    
                    entry.target.classList.add('counted');
                    
                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            entry.target.textContent = Math.floor(current).toLocaleString();
                            requestAnimationFrame(updateCounter);
                        } else {
                            entry.target.textContent = target.toLocaleString() + '+';
                        }
                    };
                    
                    updateCounter();
                }
            });
        }, observerOptions);
        
        counters.forEach(counter => counterObserver.observe(counter));
    };
    
    // Smooth Scroll for Anchor Links
    const smoothScrollLinks = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '#0') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        const headerOffset = 100;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        
                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    };
    
    // Initialize on DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        animateCounters();
        smoothScrollLinks();
        console.log('✅ CNC v5.0: All features initialized');
    });
})();
</script>

<?php
error_log('✅ CNC v5.0: Home page shortcode completed');
return ob_get_clean();
?>
