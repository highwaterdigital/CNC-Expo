<?php
/**
 * Privacy & Cookies Policy Page
 * Covers registration, stall allotment, WhatsApp notifications, and personal data use.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
.cnc-privacy {
    font-family: 'Poppins', sans-serif;
    color: #0D0D0D;
    background: #FFFFFF;
}

.cnc-privacy__hero {
    padding: 110px 20px 70px;
    text-align: center;
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #FFFFFF;
}

.cnc-privacy__hero h1 {
    margin: 0 0 1rem;
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.cnc-privacy__hero p {
    margin: 0 auto;
    max-width: 900px;
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.9);
}

.cnc-privacy__content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 20px 90px;
    display: grid;
    gap: 2rem;
}

.cnc-privacy__section h2 {
    font-size: 1.5rem;
    margin: 0 0 0.75rem;
    font-weight: 700;
    color: #0D0D0D;
}

.cnc-privacy__section p,
.cnc-privacy__section ul {
    margin: 0 0 1rem;
    line-height: 1.7;
    color: #3f3f3f;
}

.cnc-privacy__section ul {
    padding-left: 1.1rem;
}

.cnc-privacy__section ul li {
    margin-bottom: 0.5rem;
}

.cnc-privacy__tag {
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

.cnc-privacy__contact {
    background: #F8F8FB;
    border: 1px solid #E6E6F2;
    border-radius: 12px;
    padding: 1.5rem;
}

@media (max-width: 768px) {
    .cnc-privacy__hero {
        padding: 90px 20px 60px;
    }
    .cnc-privacy__content {
        padding: 50px 20px 70px;
    }
}
</style>

<div class="cnc-privacy">
    <section class="cnc-privacy__hero">
        <h1>Privacy, Cookies & Communication Policy</h1>
        <p>How Cable Net Convergence Expo collects, uses, shares, and safeguards your data across registration, stall allotment, notifications, and attendee services.</p>
    </section>

    <section class="cnc-privacy__content">
        <div class="cnc-privacy__section">
            <span class="cnc-privacy__tag">Overview</span>
            <p>We collect personal data to manage event participation, allocate exhibition stalls, communicate updates (including WhatsApp), and improve onsite and digital experiences. We follow applicable data protection norms and minimize the data we store.</p>
        </div>

        <div class="cnc-privacy__section">
            <h2>Data We Collect</h2>
            <ul>
                <li>Registration details: name, email, phone/WhatsApp number, company, role, and preferences for sessions or meetings.</li>
                <li>Stall allotment inputs: company profile, product categories, space needs, and logistics contacts.</li>
                <li>Onsite/visit data: badge scans, session check-ins, and meeting bookings when applicable.</li>
                <li>Technical data: cookies, device/browser info, and analytics events to keep the site functional and secure.</li>
            </ul>
        </div>

        <div class="cnc-privacy__section">
            <h2>How We Use Your Data</h2>
            <ul>
                <li>Process registrations and confirmations for visitors, exhibitors, and delegates.</li>
                <li>Allocate stalls, share floorplan updates, invoicing details, and operational communications.</li>
                <li>Send WhatsApp/SMS/email notifications about event updates, reminders, or service alerts (you can opt out where offered).</li>
                <li>Issue badges, support lead retrieval, and manage on-site access control.</li>
                <li>Analyze aggregated engagement to improve content, exhibitor placement, and attendee experience.</li>
            </ul>
        </div>

        <div class="cnc-privacy__section">
            <h2>Cookies & Tracking</h2>
            <ul>
                <li>Essential cookies: required for site security, session handling, and form submissions.</li>
                <li>Preference cookies: remember language or prior inputs to speed up registration.</li>
                <li>Analytics: anonymized usage to improve the site; disable via browser settings or available opt-outs.</li>
                <li>Third-party embeds: videos, maps, or payment gateways may set their own cookies; refer to their policies.</li>
            </ul>
            <p>Blocking cookies may affect form completion or registration performance.</p>
        </div>

        <div class="cnc-privacy__section">
            <h2>Sharing & Retention</h2>
            <ul>
                <li>We share relevant registration and stall data with authorized internal teams, venue operators, and contracted vendors needed to deliver the event.</li>
                <li>Lead retrieval: when your badge is scanned, your shared details go to that exhibitor under event terms.</li>
                <li>We retain data only as long as necessary for event operations, legal, accounting, and safety requirements.</li>
            </ul>
        </div>

        <div class="cnc-privacy__section">
            <h2>Your Choices</h2>
            <ul>
                <li>Opt out of marketing/WhatsApp where provided; operational messages may still be required for attendance.</li>
                <li>Request access, correction, or deletion of your data where applicable by emailing us.</li>
                <li>Disable non-essential cookies in your browser or available site controls.</li>
            </ul>
        </div>

        <div class="cnc-privacy__section">
            <h2>Security</h2>
            <p>We apply reasonable technical and organizational measures (access controls, encryption in transit where supported, role-based admin permissions). No system is 100% secure; report any suspected issues to our team.</p>
        </div>

        <div class="cnc-privacy__section">
            <h2>Contact</h2>
            <div class="cnc-privacy__contact">
                <p><strong>Data & Privacy Queries</strong></p>
                <p>Email: <a href="mailto:info@cncexpo.com">info@cncexpo.com</a><br>
                Phone/WhatsApp: +91 9505 050 007</p>
                <p>Include your name, contact details, and the request (access/update/delete/opt-out) so we can respond promptly.</p>
            </div>
        </div>
    </section>
</div>
