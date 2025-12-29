<?php
/**
 * Customer Dashboard Template
 * Shows booking details, company info form, addons form, and OTP login fallback.
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();
$fallback_booking = [];
$booking_id = 0;
if ($is_logged_in && $current_user->ID && function_exists('cnc_expo_find_booking_for_user')) {
    $booking_id = cnc_expo_find_booking_for_user($current_user->ID);
    if ($booking_id) {
        $meta = get_post_meta($booking_id);
        $fallback_booking = [
            'company'   => $meta['cnc_company_name'][0] ?? '',
            'contact'   => $meta['cnc_contact_person'][0] ?? '',
            'phone'     => $meta['cnc_phone'][0] ?? '',
            'email'     => $meta['cnc_email'][0] ?? '',
            'status'    => ucfirst($meta['cnc_booking_status'][0] ?? 'pending'),
            'stall_ids' => array_values(array_filter((array)($meta['cnc_stall_id'] ?? []))),
            'profile'   => [
                'address'  => $meta['cnc_address'][0] ?? '',
                'gst'      => $meta['cnc_gst'][0] ?? '',
                'pan'      => $meta['cnc_pan'][0] ?? '',
                'logo_url' => $meta['cnc_logo_url'][0] ?? '',
            ],
            'addons'    => $meta['cnc_addons'][0] ?? [],
        ];
    }
}
?>

<style>
.expo-dashboard-shell {
    max-width: 1240px;
    margin: 0 auto;
    padding: 140px 18px 90px;
    font-family: 'Inter', sans-serif;
    color: #0f1320;
    background: linear-gradient(180deg, #0a0a0f 0%, #0f0f14 55%, #0b0b0f 100%);
}
.expo-dashboard-hero {
    position: relative;
    background: linear-gradient(120deg, #14182a 0%, #0f0f14 50%, #10122b 100%);
    color: #fff;
    padding: 32px 32px 80px;
    border-radius: 18px;
    display: flex;
    justify-content: space-between;
    gap: 20px;
    box-shadow: 0 18px 50px rgba(0,0,0,0.35);
    overflow: hidden;
}
.expo-dashboard-hero::after {
    content: '';
    position: absolute;
    right: -120px;
    top: -80px;
    width: 320px;
    height: 320px;
    background: radial-gradient(ellipse at center, rgba(198,48,140,0.35) 0%, rgba(198,48,140,0) 60%);
    transform: rotate(-8deg);
}
.expo-dashboard-hero h2 { margin: 8px 0; font-size: 30px; }
.expo-dashboard-hero p { margin: 0; color: #dce0ee; max-width: 760px; }
.expo-eyebrow { text-transform: uppercase; letter-spacing: 0.08em; font-size: 12px; color: #a7b4ff; margin: 0; }
.expo-user-pill {
    display: flex;
    gap: 12px;
    align-items: center;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(4px);
    z-index: 1;
}
.expo-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #cdff00;
    color: #0d0d0d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 18px;
}
.expo-dashboard-body { margin-top: -50px; display: flex; flex-direction: column; gap: 16px; }
.expo-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    background: #111420;
    padding: 6px;
    border-radius: 12px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.28);
    width: 100%;
    justify-content: flex-start;
    align-self: flex-start;
}
.expo-tab-btn {
    border: none;
    cursor: pointer;
    padding: 12px 18px;
    border-radius: 10px;
    background: #1b1f2e;
    color: #dfe4f5;
    font-weight: 700;
    transition: all .2s ease;
    min-width: 140px;
}
.expo-tab-btn.active {
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #fff;
    box-shadow: 0 14px 32px rgba(94,58,142,0.45);
}
.expo-tab-btn:hover { color: #fff; }
.expo-panels {
    margin-top: 4px;
    display: grid;
    gap: 18px;
    scroll-margin-top: 120px;
}
.expo-tab-panel {
    display: none;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.14);
    padding: 24px;
    opacity: 0;
    transform: translateY(8px);
    transition: opacity .2s ease, transform .2s ease;
}
.expo-tab-panel.active { display: block; opacity: 1; transform: translateY(0); }
.expo-card {
    border: 1px solid #ebedf3;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 16px;
    background: #fff;
    box-shadow: 0 10px 24px rgba(15,18,35,0.06);
}
.expo-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}
.expo-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
}
.expo-grid label {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 13px;
    font-weight: 700;
    color: #1b1e2a;
}
.expo-grid input,
.expo-grid textarea {
    border: 1px solid #dfe3ec;
    border-radius: 9px;
    padding: 12px 14px;
    font-size: 14px;
    background: #fafbff;
    transition: border-color .15s ease, box-shadow .15s ease;
}
.expo-grid input:focus,
.expo-grid textarea:focus {
    outline: none;
    border-color: #c6308c;
    box-shadow: 0 0 0 3px rgba(198,48,140,0.12);
}
.expo-grid textarea { min-height: 120px; resize: vertical; }
.expo-grid .wide { grid-column: 1 / -1; }
.expo-btn {
    cursor: pointer;
    border: none;
    border-radius: 10px;
    padding: 14px 16px;
    font-weight: 800;
    font-size: 15px;
}
.expo-btn--primary { background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%); color: #fff; }
.expo-btn--secondary { background: #0f0f14; color: #fff; }
.expo-badge {
    padding: 8px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}
.expo-badge--approved { background: #e6f7ed; color: #0b6d3c; }
.expo-badge--pending { background: #fff7e6; color: #946200; }
.expo-badge--rejected { background: #fdecea; color: #b42318; }
.expo-stall-list { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
.expo-stall-pill {
    padding: 10px 14px;
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    background: #f7f8fb;
    cursor: pointer;
    transition: all .2s ease;
    font-weight: 700;
}
.expo-stall-pill.active { background: #e9ddff; border-color: #5E3A8E; color: #3c2366; }
.expo-login-gate {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 1px solid #edf0f7;
}
.expo-small { font-size: 13px; color: #5c6070; margin-top: 8px; }
.expo-link { color: #5E3A8E; font-weight: 700; text-decoration: none; }
.expo-link:hover { text-decoration: underline; }
.expo-lead { margin: 0; }
.expo-muted { color: #5c6070; }
.expo-toast {
    position: fixed;
    bottom: 22px;
    right: 22px;
    background: #0f0f14;
    color: #fff;
    padding: 12px 16px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    z-index: 9999;
    display: none;
    font-weight: 700;
}
.expo-toast--success { background: #0b6d3c; }
.expo-toast--error { background: #b42318; }
@media (max-width: 960px) {
    .expo-dashboard-hero { padding: 28px 24px 90px; }
    .expo-dashboard-body { margin-top: -40px; }
    .expo-tabs { width: 100%; justify-content: center; flex-wrap: wrap; }
    .expo-tab-btn { flex: 1 1 40%; text-align: center; }
}
</style>

<style>
/* Layout overrides: tabs left, content right */
.expo-dashboard-body {
    margin-top: -30px;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 18px;
    align-items: start;
}
.expo-tabs {
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #111420;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.28);
    width: 100%;
    position: sticky;
    top: 110px;
}
.expo-tab-btn {
    width: 100%;
    text-align: left;
    padding: 12px 14px;
}
@media (max-width: 960px) {
    .expo-dashboard-body {
        margin-top: -20px;
        grid-template-columns: 1fr;
    }
    .expo-tabs {
        position: relative;
        top: 0;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
    }
    .expo-tab-btn { flex: 1 1 45%; text-align: center; }
}
</style>

<section class="expo-dashboard-shell">
    <div class="expo-dashboard-hero">
        <div>
            <p class="expo-eyebrow">CNC Expo 2026</p>
            <h2><?php echo $is_logged_in ? 'My Dashboard' : 'Manage Your Booking'; ?></h2>
            <p class="expo-lead">
                View your stall, update company details, and add services. Everything syncs instantly to our admin desk.
            </p>
        </div>
        <?php if ($is_logged_in): ?>
            <div class="expo-user-pill">
                <span class="expo-avatar"><?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?></span>
                <div>
                    <strong><?php echo esc_html($current_user->display_name); ?></strong><br>
                    <small><?php echo esc_html($current_user->user_email); ?></small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="expo-dashboard-body">
        <div class="expo-tabs">
            <button class="expo-tab-btn active" data-tab="booking">Personal Details</button>
            <button class="expo-tab-btn" data-tab="company">Company Profile</button>
            <button class="expo-tab-btn" data-tab="addons">Bookings & Add-ons</button>
        </div>

        <div class="expo-panels expo-customer-dashboard" data-logged-in="<?php echo $is_logged_in ? '1' : '0'; ?>">
            <?php if (!$is_logged_in): ?>
                <div class="expo-login-gate">
                    <h3>Quick OTP Login</h3>
                    <p class="expo-muted">Use your booking phone and email to get an OTP and access your dashboard.</p>
                    <button class="expo-btn expo-btn--primary expo-login-open" type="button">Login with OTP</button>
                    <p class="expo-small">New exhibitor? <a href="<?php echo esc_url(home_url('/book-space')); ?>">Book a Stall</a></p>
                </div>
            <?php endif; ?>

            <div class="expo-tab-panel active" data-tab="booking">
                <?php if (!empty($fallback_booking['stall_ids'])): ?>
                    <div class="expo-card">
                        <div class="expo-card-header">
                            <h3>Your Booking</h3>
                            <span class="expo-badge expo-badge--<?php echo esc_attr(strtolower($fallback_booking['status'] ?? 'pending')); ?>">
                                <?php echo esc_html($fallback_booking['status'] ?? 'Pending'); ?>
                            </span>
                        </div>
                        <div class="expo-grid">
                            <div><strong>Stall(s)</strong><span><?php echo esc_html(implode(', ', $fallback_booking['stall_ids'])); ?></span></div>
                            <div><strong>Company</strong><span><?php echo esc_html($fallback_booking['company']); ?></span></div>
                            <div><strong>Contact</strong><span><?php echo esc_html($fallback_booking['contact']); ?></span></div>
                            <div><strong>Phone</strong><span><?php echo esc_html($fallback_booking['phone']); ?></span></div>
                            <div><strong>Email</strong><span><?php echo esc_html($fallback_booking['email']); ?></span></div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="expo-card">
                        <p>No booking found yet. Use the control panel to request a stall or <a class="expo-link" href="<?php echo esc_url(home_url('/book-space')); ?>">book a stall</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="expo-tab-panel" data-tab="company">
                <div class="expo-card">
                    <div class="expo-card-header"><h3>Company Profile</h3></div>
                    <div class="expo-grid">
                        <label>Company Name<input value="<?php echo esc_attr($fallback_booking['company'] ?? ''); ?>" disabled /></label>
                        <label>Contact Person<input value="<?php echo esc_attr($fallback_booking['contact'] ?? ''); ?>" disabled /></label>
                        <label>Phone<input value="<?php echo esc_attr($fallback_booking['phone'] ?? ''); ?>" disabled /></label>
                        <label>Email<input value="<?php echo esc_attr($fallback_booking['email'] ?? ''); ?>" disabled /></label>
                        <label>Address<textarea disabled><?php echo esc_textarea($fallback_booking['profile']['address'] ?? ''); ?></textarea></label>
                    </div>
                </div>
            </div>
            <div class="expo-tab-panel" data-tab="addons">
                <?php if (!empty($fallback_booking['addons'])): ?>
                    <div class="expo-card">
                        <div class="expo-card-header"><h3>Add-ons Summary</h3></div>
                        <div class="expo-grid">
                            <?php foreach ((array)$fallback_booking['addons'] as $stall => $values): ?>
                                <div style="grid-column: span 1;">
                                    <strong>Stall <?php echo esc_html($stall); ?></strong>
                                    <p class="expo-muted" style="margin:0;"><?php echo esc_html(json_encode($values)); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="expo-card">
                        <p>Add-ons will appear here once your booking is approved.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
