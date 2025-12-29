<?php
/**
 * Contact Team Page
 */
if (!defined('ABSPATH')) {
    exit;
}

ob_start();
?>
<section style="padding:120px 0 80px;background:#0a0616;color:#fff;">
    <div style="max-width:900px;margin:0 auto;padding:0 24px;">
        <p style="color:#C8FF33;letter-spacing:0.12em;font-weight:700;text-transform:uppercase;margin-bottom:10px;">Contact</p>
        <h1 style="font-family:'Poppins',sans-serif;font-size:38px;margin:0 0 16px;">Talk to the CNC Team</h1>
        <p style="line-height:1.8;color:rgba(255,255,255,0.78);margin:0 0 28px;">For bookings, partnerships, speaking, and visitor assistance.</p>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 8px;font-size:18px;">General</h3>
                <p style="margin:0 0 8px;line-height:1.7;color:rgba(255,255,255,0.8);">info@cncexpo.com</p>
                <a href="tel:+919505050007" style="color:#C8FF33;text-decoration:none;font-weight:600;">+91 9505 050 007</a>
            </div>
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 8px;font-size:18px;">Exhibitor & Bookings</h3>
                <p style="margin:0;line-height:1.7;color:rgba(255,255,255,0.8);">For stalls, branding, and sponsorships.</p>
                <a href="<?php echo esc_url(home_url('/book-space')); ?>" style="color:#C8FF33;text-decoration:none;font-weight:600;">Book Your Stall</a>
            </div>
            <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);padding:18px;border-radius:12px;">
                <h3 style="margin:0 0 8px;font-size:18px;">Visitors</h3>
                <p style="margin:0;line-height:1.7;color:rgba(255,255,255,0.8);">Assistance with registration and entry.</p>
                <a href="<?php echo esc_url(home_url('/visitors')); ?>" style="color:#C8FF33;text-decoration:none;font-weight:600;">Visitor Registration</a>
            </div>
        </div>
    </div>
</section>
<?php
return ob_get_clean();
