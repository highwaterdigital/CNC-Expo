<?php
/**
 * CNC Expo Header v11.0 - Redesigned
 * Features: Smart User Icon, Grouped Menu, Solid Visibility
 */
if (!defined('ABSPATH')) exit;
?>

<style>
    /* --- CSS VARIABLES --- */
    :root {
        --cnc-magenta: #C6308C;
        --cnc-purple: #5E3A8E;
        --cnc-black: #080714;
        --cnc-night: #05040a;
        --cnc-white: #FFFFFF;
        --cnc-lime: #C8FF33;
        --cnc-gray: #333333;
        --cnc-transition: all 0.3s ease;
        --header-height: 90px;
    }

    /* --- 1. GLOBAL LAYOUT FIXES --- */
    body { padding-top: var(--header-height) !important; }
    
    /* Admin Bar Fix */
    body.admin-bar .cnc-header { top: 32px; }
    @media (max-width: 782px) { body.admin-bar .cnc-header { top: 46px; } }

    /* --- 2. HEADER CONTAINER --- */
    .cnc-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999999;
        padding: 15px 0;
        width: 100%;
        background-color: var(--cnc-night); /* Always Solid */
        border-bottom: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        font-family: 'Poppins', sans-serif;
    }

    .cnc-header__container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* --- LEFT: LOGO & DATE --- */
    .cnc-header__left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .cnc-header__logo img {
        height: 50px;
        width: auto;
        display: block;
    }

    .cnc-header__divider {
        height: 30px;
        width: 1px;
        background-color: rgba(255,255,255,0.2);
        display: none; /* Hidden on mobile */
    }
    @media (min-width: 992px) { .cnc-header__divider { display: block; } }

    .cnc-header__date {
        display: none; /* Hidden on mobile */
        flex-direction: column;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        line-height: 1.3;
        color: var(--cnc-white);
    }
    @media (min-width: 992px) { .cnc-header__date { display: flex; } }
    
    .cnc-header__date span {
        color: var(--cnc-magenta);
        font-weight: 800;
        font-size: 0.85rem;
    }

    /* --- RIGHT: ACTIONS --- */
    .cnc-header__right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* Action Buttons (Desktop) */
    .cnc-btn-action {
        background: transparent;
        color: var(--cnc-white);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        text-decoration: none;
        transition: var(--cnc-transition);
        white-space: nowrap;
        display: none; /* Hidden on mobile */
    }
    @media (min-width: 992px) { .cnc-btn-action { display: inline-flex; } }

    .cnc-btn-action:hover {
        border-color: var(--cnc-magenta);
        color: var(--cnc-magenta);
    }

    /* Primary Button (Book Space) */
    .cnc-btn-primary {
        background: linear-gradient(135deg, var(--cnc-purple) 0%, var(--cnc-magenta) 100%);
        border: none;
        color: #fff;
    }
    .cnc-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(198, 48, 140, 0.4);
        color: #fff;
    }

    /* --- LOGIN / ACCOUNT STYLES --- */
    
    /* Guest: Login Button with Key */
    .cnc-btn-login {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--cnc-lime);
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.2s;
        border: none;
        background: rgba(255,255,255,0.05);
    }
    .cnc-btn-login:hover { background: rgba(255,255,255,0.1); }

    /* Logged In: User Icon Only */
    .cnc-user-icon-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        color: var(--cnc-white);
        font-size: 1.2rem;
        text-decoration: none;
        transition: 0.3s;
        border: 1px solid transparent;
    }
    .cnc-user-icon-link:hover {
        background: var(--cnc-purple);
        border-color: var(--cnc-magenta);
    }

    /* --- HAMBURGER MENU --- */
    .cnc-menu-toggle {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-end;
        width: 44px;
        height: 44px;
        background: transparent;
        border: none;
        padding: 0 5px;
        gap: 6px;
        cursor: pointer;
    }
    .cnc-menu-toggle span {
        display: block;
        height: 2px;
        background: var(--cnc-white);
        border-radius: 2px;
        transition: 0.3s;
    }
    .cnc-menu-toggle span:nth-child(1) { width: 28px; }
    .cnc-menu-toggle span:nth-child(2) { width: 20px; }
    .cnc-menu-toggle span:nth-child(3) { width: 24px; }
    
    .cnc-menu-toggle:hover span { width: 28px; background: var(--cnc-lime); }

    /* --- SIDE DRAWER --- */
    .cnc-side-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.8);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s;
        z-index: 1000000;
        backdrop-filter: blur(5px);
    }
    .cnc-side-overlay.active { opacity: 1; visibility: visible; }

    .cnc-side-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 320px;
        max-width: 85vw;
        height: 100vh;
        background: #121212; /* Dark Theme Menu */
        z-index: 1000001;
        transition: right 0.4s cubic-bezier(0.7,0,0.3,1);
        box-shadow: -10px 0 30px rgba(0,0,0,0.6);
        display: flex;
        flex-direction: column;
    }
    .cnc-side-menu.active { right: 0; }

    .cnc-drawer-header {
        padding: 20px;
        display: flex;
        justify-content: flex-end;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .cnc-close-menu {
        background: none; border: none; color: #FFF; font-size: 2rem; cursor: pointer;
        transition: color 0.2s;
    }
    .cnc-close-menu:hover { color: var(--cnc-magenta); }

    /* Menu List */
    .cnc-drawer-scroll {
        overflow-y: auto;
        padding: 20px 30px 40px;
        flex: 1;
    }

    .cnc-menu-list { list-style: none; padding: 0; margin: 0; }
    
    .cnc-menu-item a {
        display: block;
        font-size: 1rem;
        color: #DDD;
        text-decoration: none;
        padding: 10px 0;
        transition: 0.2s;
        border-bottom: 1px solid rgba(255,255,255,0.03);
    }
    .cnc-menu-item a:hover {
        color: var(--cnc-magenta);
        padding-left: 8px;
    }

    /* Menu Divider */
    .cnc-menu-divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 15px 0;
    }

    /* Account Link in Menu */
    .cnc-menu-account a {
        color: var(--cnc-lime);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Footer Links in Menu */
    .cnc-menu-footer {
        display: flex;
        gap: 15px;
        margin-top: 10px;
    }
    .cnc-menu-footer a {
        font-size: 0.8rem;
        color: #888;
        text-decoration: none;
    }
    .cnc-menu-footer a:hover { color: #FFF; }

</style>

<div class="cnc-header-wrapper">
    
    <header id="cnc-main-header" class="cnc-header">
        <div class="cnc-header__container">
            
            <div class="cnc-header__left">
                <a href="<?php echo home_url('/'); ?>" class="cnc-header__logo">
                    <img src="https://cablenetconvergence.com/wp-content/uploads/2025/12/cnc-logo.png" alt="CNC Expo">
                </a>

                <div class="cnc-header__divider"></div>

                <div class="cnc-header__date">
                    <span>13-15 AUG 2026</span>
                    HITEX, Hyderabad
                </div>
            </div>

            <div class="cnc-header__right">
                
                <a href="/visitors" class="cnc-btn-action">For Visitors</a>
                <a href="/book-space" class="cnc-btn-action cnc-btn-primary">For Booking</a>

                <?php if (is_user_logged_in()) : ?>
                    <a href="<?php echo home_url('/my-account'); ?>" class="cnc-user-icon-link" title="My Account">
                        👤
                    </a>
                <?php else : ?>
                    <button class="cnc-btn-login expo-login-open" type="button">
                        Login 🔑
                    </button>
                <?php endif; ?>

                <button id="cnc-toggle-btn" class="cnc-menu-toggle" aria-label="Open Menu">
                    <span></span><span></span><span></span>
                </button>

            </div>
        </div>
    </header>

    <div id="cnc-drawer-overlay" class="cnc-side-overlay"></div>
    
    <div id="cnc-drawer-menu" class="cnc-side-menu">
        <div class="cnc-drawer-header">
            <button id="cnc-close-btn" class="cnc-close-menu">&times;</button>
        </div>
        
        <div class="cnc-drawer-scroll">
            <ul class="cnc-menu-list">
                
                <li class="cnc-menu-item cnc-menu-account">
                    <a href="/my-account">My Account</a>
                </li>
                <li class="cnc-menu-divider"></li>

                <li class="cnc-menu-item"><a href="/book-space">Book Space</a></li>
                <li class="cnc-menu-item"><a href="/visitors">Visitors Registration</a></li>
                <li class="cnc-menu-divider"></li>

                <li class="cnc-menu-item"><a href="/floorplan">Floor Plan</a></li>
                <li class="cnc-menu-item"><a href="/exhibitors">Exhibitors</a></li>
                <li class="cnc-menu-item"><a href="/sponsors-partners">Sponsors & Partners</a></li>
                <li class="cnc-menu-item"><a href="/participants">Participants</a></li>
                <li class="cnc-menu-item"><a href="/gallery">Gallery</a></li>
                <li class="cnc-menu-item"><a href="/important-dates">Imp Dates</a></li>
                <li class="cnc-menu-divider"></li>

                <li class="cnc-menu-item"><a href="/about">About Us</a></li>
                <li class="cnc-menu-item"><a href="/contact">Contact</a></li>
                <li class="cnc-menu-divider"></li>

                <li class="cnc-menu-item"><a href="/nearby-stays">Nearby Stays</a></li>
                <li class="cnc-menu-divider"></li>

                <div class="cnc-menu-footer">
                    <a href="/privacy-policy">Privacy Policy</a>
                    <span>|</span>
                    <a href="/terms-conditions">Terms</a>
                </div>

            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Menu Toggle Logic
    const toggleBtn = document.getElementById("cnc-toggle-btn");
    const closeBtn = document.getElementById("cnc-close-btn");
    const drawer = document.getElementById("cnc-drawer-menu");
    const overlay = document.getElementById("cnc-drawer-overlay");

    function toggleMenu() {
        if (!drawer || !overlay) return;
        
        const isActive = drawer.classList.contains("active");
        if (isActive) {
            drawer.classList.remove("active");
            overlay.classList.remove("active");
            document.body.style.overflow = ""; // Enable scroll
        } else {
            drawer.classList.add("active");
            overlay.classList.add("active");
            document.body.style.overflow = "hidden"; // Disable scroll
        }
    }

    if(toggleBtn) toggleBtn.addEventListener("click", toggleMenu);
    if(closeBtn) closeBtn.addEventListener("click", toggleMenu);
    if(overlay) overlay.addEventListener("click", toggleMenu);

    // 2. Login Popup Trigger (Connects to your existing JS)
    // The class 'expo-login-open' is handled by your assets/js/expo-login.js
    // This listener just ensures the mobile drawer closes if they click login inside it
    const loginBtns = document.querySelectorAll('.expo-login-open');
    loginBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if(drawer.classList.contains("active")) {
                toggleMenu(); // Close drawer so popup is visible
            }
        });
    });
});
</script>
