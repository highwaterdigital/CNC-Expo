<?php
/**
 * About Cable Net Convergence Expo
 */
if (!defined('ABSPATH')) {
    exit;
}

ob_start();
?>
<section style="padding:120px 0 80px;background:#0a0616;color:#f5f5f5;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;">
        <p style="color:#C8FF33;letter-spacing:0.12em;font-weight:700;text-transform:uppercase;margin-bottom:8px;">About</p>
        <h1 style="font-family:'Poppins',sans-serif;font-size:42px;margin:0 0 18px;">Cable Net Convergence Expo</h1>
        <p style="max-width:860px;line-height:1.8;margin:0 0 28px;color:rgba(255,255,255,0.78);">
            CNC is India’s premier platform for cable, internet, and broadband innovation. We connect operators,
            technology providers, content partners, and infrastructure leaders to showcase new solutions, forge business
            alliances, and build the future of connectivity.
        </p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-top:30px;">
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 10px;font-size:20px;">Exhibition & Floor</h3>
                <p style="margin:0;line-height:1.7;color:rgba(255,255,255,0.76);">Comprehensive floor with networking zones, demos, and curated showcases for service providers and vendors.</p>
            </div>
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 10px;font-size:20px;">Conference & Workshops</h3>
                <p style="margin:0;line-height:1.7;color:rgba(255,255,255,0.76);">Focused sessions on deployment models, content delivery, security, and monetization.</p>
            </div>
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 10px;font-size:20px;">Allied Programs</h3>
                <p style="margin:0;line-height:1.7;color:rgba(255,255,255,0.76);">Buyer-seller meets, investor connects, and special tracks for cable, broadband, and media ecosystems.</p>
            </div>
        </div>
    </div>
</section>
<?php
return ob_get_clean();
