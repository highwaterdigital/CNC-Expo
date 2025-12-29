<?php
/**
 * Cookie Policy Page
 * Explains how Cable Net Convergence Expo uses cookies and how users can control them.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
.cnc-cookie {
    font-family: 'Poppins', sans-serif;
    color: #0D0D0D;
    background: #FFFFFF;
}

.cnc-cookie__hero {
    padding: 110px 20px 70px;
    text-align: center;
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #FFFFFF;
}

.cnc-cookie__hero h1 {
    margin: 0 0 1rem;
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.cnc-cookie__hero p {
    margin: 0 auto;
    max-width: 900px;
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.9);
}

.cnc-cookie__content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 20px 90px;
    display: grid;
    gap: 2rem;
}

.cnc-cookie__section h2 {
    font-size: 1.5rem;
    margin: 0 0 0.75rem;
    font-weight: 700;
    color: #0D0D0D;
}

.cnc-cookie__section p,
.cnc-cookie__section ul {
    margin: 0 0 1rem;
    line-height: 1.7;
    color: #3f3f3f;
}

.cnc-cookie__section ul {
    padding-left: 1.1rem;
}

.cnc-cookie__section ul li {
    margin-bottom: 0.5rem;
}

.cnc-cookie__tag {
    display: inline-block;
    padding: 8px 14px;
    background: rgba(94, 58, 142, 0.08);
    color: #5E3A8E;
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 0.8rem;
    margin-bottom: 0.75rem;
}

.cnc-cookie__callout {
    background: #F8F8FB;
    border: 1px solid #E6E6F2;
    border-radius: 12px;
    padding: 1.5rem;
}

@media (max-width: 768px) {
    .cnc-cookie__hero {
        padding: 90px 20px 60px;
    }
    .cnc-cookie__content {
        padding: 50px 20px 70px;
    }
}
</style>

<div class="cnc-cookie">
    <section class="cnc-cookie__hero">
        <h1>Cookie Policy</h1>
        <p>How Cable Net Convergence Expo uses cookies and similar technologies to keep the site secure, reliable, and useful.</p>
    </section>

    <section class="cnc-cookie__content">
        <div class="cnc-cookie__section">
            <span class="cnc-cookie__tag">Overview</span>
            <p>Cookies are small text files placed on your device when you visit our website. We use them to keep sessions secure, remember preferences, and improve performance. By using our site, you agree to the essential cookies described here.</p>
        </div>

        <div class="cnc-cookie__section">
            <h2>Types of Cookies We Use</h2>
            <ul>
                <li><strong>Essential</strong>: Required for security, session handling, forms, and fraud prevention.</li>
                <li><strong>Preferences</strong>: Remember language or form inputs to reduce retyping.</li>
                <li><strong>Analytics</strong>: Aggregate, anonymized insights to improve content and performance.</li>
                <li><strong>Marketing/Embeds</strong>: Only when external embeds (videos, maps, payment gateways) need them; their policies apply.</li>
            </ul>
        </div>

        <div class="cnc-cookie__section">
            <h2>How We Use Cookies</h2>
            <ul>
                <li>Maintain secure login/registration sessions and prevent duplicate submissions.</li>
                <li>Remember exhibitor/visitor preferences during registration or booking flows.</li>
                <li>Balance load, measure page performance, and detect technical issues.</li>
                <li>Support third-party widgets (media players, maps, chat) when embedded.</li>
            </ul>
        </div>

        <div class="cnc-cookie__section">
            <h2>Third-Party Cookies</h2>
            <p>Some cookies may be set by trusted vendors that provide analytics, media embeds, communications, or payment services. These providers have their own privacy and cookie notices; review them for details on their handling.</p>
        </div>

        <div class="cnc-cookie__section">
            <h2>Your Choices</h2>
            <ul>
                <li>Use your browser settings to block or delete cookies; essential cookies may be required for registration or secure access.</li>
                <li>Opt out of analytics or marketing cookies where controls are provided.</li>
                <li>Remove consent by clearing cookies; the site may prompt again for preferences.</li>
            </ul>
        </div>

        <div class="cnc-cookie__section">
            <h2>Retention</h2>
            <p>Session cookies expire when you close the browser. Preference and analytics cookies may persist for a limited period set by the issuing service but are cleared if you delete cookies from your browser.</p>
        </div>

        <div class="cnc-cookie__section">
            <h2>Contact</h2>
            <div class="cnc-cookie__callout">
                <p>Questions about this Cookie Policy?<br>
                Email: <a href="mailto:info@cncexpo.com">info@cncexpo.com</a><br>
                Phone/WhatsApp: +91 9505 050 007</p>
            </div>
        </div>

        <div class="cnc-cookie__section">
            <p>For how we collect, use, and share personal data, please read our <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a> and the <a href="<?php echo esc_url(home_url('/terms-conditions')); ?>">Terms & Conditions</a>.</p>
        </div>
    </section>
</div>
