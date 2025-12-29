<?php
/**
 * Terms & Conditions Page
 * Covers attendance, exhibitor obligations, payments, conduct, safety, media, and liability.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
.cnc-legal {
    font-family: 'Poppins', sans-serif;
    color: #0D0D0D;
    background: #FFFFFF;
}

.cnc-legal__hero {
    padding: 110px 20px 70px;
    text-align: center;
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #FFFFFF;
}

.cnc-legal__hero h1 {
    margin: 0 0 1rem;
    font-size: clamp(2.2rem, 5vw, 3.2rem);
    font-weight: 800;
    letter-spacing: -0.02em;
}

.cnc-legal__hero p {
    margin: 0 auto;
    max-width: 900px;
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.9);
}

.cnc-legal__content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 60px 20px 90px;
    display: grid;
    gap: 2rem;
}

.cnc-legal__section h2 {
    font-size: 1.5rem;
    margin: 0 0 0.75rem;
    font-weight: 700;
    color: #0D0D0D;
}

.cnc-legal__section p,
.cnc-legal__section ul {
    margin: 0 0 1rem;
    line-height: 1.7;
    color: #3f3f3f;
}

.cnc-legal__section ul {
    padding-left: 1.1rem;
}

.cnc-legal__section ul li {
    margin-bottom: 0.5rem;
}

.cnc-legal__tag {
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

.cnc-legal__callout {
    background: #F8F8FB;
    border: 1px solid #E6E6F2;
    border-radius: 12px;
    padding: 1.5rem;
}

@media (max-width: 768px) {
    .cnc-legal__hero {
        padding: 90px 20px 60px;
    }
    .cnc-legal__content {
        padding: 50px 20px 70px;
    }
}
</style>

<div class="cnc-legal">
    <section class="cnc-legal__hero">
        <h1>Terms & Conditions</h1>
        <p>Rules for participation at Cable Net Convergence Expo, including exhibitor/visitor responsibilities, payments, safety, and media usage.</p>
    </section>

    <section class="cnc-legal__content">
        <div class="cnc-legal__section">
            <span class="cnc-legal__tag">Overview</span>
            <p>By registering for, visiting, or exhibiting at Cable Net Convergence Expo, you agree to these terms and any venue-specific rules issued by organizers. Keep your registration details accurate and follow onsite staff instructions.</p>
        </div>

        <div class="cnc-legal__section">
            <h2>Eligibility & Access</h2>
            <ul>
                <li>Entry credentials (badges/passes) are personal, non-transferable, and may be revoked for misuse.</li>
                <li>Minors may require guardian consent and must comply with venue safety policies.</li>
                <li>Organizers may refuse or revoke access for security, safety, or non-compliance reasons.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Registrations, Payments & Invoicing</h2>
            <ul>
                <li>Exhibitor stall bookings and upgrades are confirmed only after required paperwork and payments.</li>
                <li>Invoices, due dates, and payment modes are communicated by the operations/finance team; late payments may delay allotment.</li>
                <li>Visitor/delegate registrations are subject to verification; fraudulent or duplicate entries can be cancelled.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Exhibitor Obligations</h2>
            <ul>
                <li>Follow assigned stall dimensions, power and safety norms, and venue build/break schedules.</li>
                <li>Use approved branding and signage; hazardous materials or high-power equipment require prior clearance.</li>
                <li>Keep the stall staffed during show hours and vacate per published timelines.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Visitor & Delegate Conduct</h2>
            <ul>
                <li>Respect fellow attendees, exhibitors, and staff. Harassment or disruptive behavior is prohibited.</li>
                <li>Follow queueing, access control, and health/safety instructions given onsite.</li>
                <li>Badge scans share your provided details with that exhibitor for follow-ups and lead retrieval.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Cancellations & Changes</h2>
            <ul>
                <li>Organizers may adjust schedules, floorplans, or programming for operational or safety reasons.</li>
                <li>Exhibitor cancellation/refund terms follow the booking agreement shared during allotment.</li>
                <li>In-force circumstances (force majeure, government orders, venue directives) may require postponement or format changes.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Safety, Compliance & Insurance</h2>
            <ul>
                <li>Comply with venue safety rules, electrical limits, and emergency procedures.</li>
                <li>Exhibitors should maintain adequate insurance for equipment, staff, and third-party liability.</li>
                <li>Organizers are not responsible for loss or damage to personal or exhibitor property.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Intellectual Property & Media</h2>
            <ul>
                <li>Logos, creatives, and marks remain the property of their owners; obtain permission for use.</li>
                <li>By entering the venue, you consent to photography, videography, and live streaming; organizers may use such media for event promotion.</li>
                <li>Do not record restricted sessions or content marked confidential.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Data & Communications</h2>
            <ul>
                <li>Registration and stall data are processed to run the event, allocate space, and send operational updates.</li>
                <li>We may send WhatsApp/SMS/email notifications about schedules, invoices, and reminders; opt out of marketing where offered.</li>
                <li>See our <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a> and <a href="<?php echo esc_url(home_url('/cookie-policy')); ?>">Cookie Policy</a> for details.</li>
            </ul>
        </div>

        <div class="cnc-legal__section">
            <h2>Liability</h2>
            <p>To the extent permitted by law, organizers and partners are not liable for indirect, incidental, or consequential damages arising from attendance or exhibition. Total direct liability, if established, is limited to fees paid for the specific booking (where applicable).</p>
        </div>

        <div class="cnc-legal__section">
            <h2>Governing Law & Contact</h2>
            <div class="cnc-legal__callout">
                <p><strong>Jurisdiction</strong>: Subject to the courts and applicable laws of India.</p>
                <p><strong>Questions or clarifications</strong><br>
                Email: <a href="mailto:info@cncexpo.com">info@cncexpo.com</a><br>
                Phone/WhatsApp: +91 9505 050 007</p>
            </div>
        </div>
    </section>
</div>
