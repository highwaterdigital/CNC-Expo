<style>
/* Schedule Section Styles - Modern & Robust */
.cnc-schedule {
    padding: 100px 20px;
    background: linear-gradient(135deg, #F9FAFB 0%, #F3F0FF 100%);
    position: relative;
    display: flex;
    justify-content: center;
}

.cnc-schedule-tabs {
    display: flex;
    justify-content: center;
    margin-bottom: 4rem;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.cnc-schedule-tab {
    background: #FFFFFF;
    padding: 1.25rem 2.5rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    border: 1px solid #EAEAEA;
    min-width: 160px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}

.cnc-schedule-tab:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

.cnc-schedule-tab.active {
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 10px 20px rgba(198, 48, 140, 0.3);
}

.cnc-schedule-tab h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.cnc-schedule-tab span {
    font-size: 0.875rem;
    opacity: 0.9;
    font-weight: 400;
}

.cnc-schedule-content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.cnc-schedule-day {
    display: none;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.cnc-schedule-day.active {
    display: block;
}

.cnc-schedule-item {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 0.85rem;
    padding: 2rem;
    background: #FFFFFF;
    border-radius: 14px;
    margin-bottom: 1.2rem;
    border: 1px solid rgba(94, 58, 142, 0.08);
    box-shadow: 0 12px 32px rgba(13, 13, 13, 0.06);
    transition: all 0.35s ease;
    opacity: 0;
    transform: translateY(16px);
}

.cnc-schedule-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 48px rgba(94, 58, 142, 0.18);
    border-color: rgba(198, 48, 140, 0.2);
}

.cnc-schedule-time {
    background: #F0F4F8;
    color: #5E3A8E;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 700;
    min-width: 140px;
    text-align: center;
    font-size: 0.95rem;
}

.cnc-schedule-details {
    flex: 1;
    text-align: center;
}

.cnc-schedule-details h4 {
    font-size: 1.25rem;
    color: #0D0D0D;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.cnc-schedule-details p {
    color: #666;
    font-size: 0.95rem;
    margin: 0;
}

@media (max-width: 768px) {
    .cnc-schedule {
        padding: 80px 20px;
    }
    .cnc-schedule-item {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
    }
    .cnc-schedule-time {
        margin-right: 0;
        margin-bottom: 1rem;
        width: 100%;
    }
    .cnc-schedule-details {
        text-align: center;
    }
}
</style>

<!-- Event Schedule Section -->
<section class="cnc-schedule" data-animate="fade-up">
    <div class="cnc-section__content">
        <h2 class="cnc-section-title">Event Schedule</h2>
        <div class="cnc-schedule-tabs">
            <div class="cnc-schedule-tab active" data-day="1">
                <h3>Day 1</h3>
                <span>Aug 13, 2026</span>
            </div>
            <div class="cnc-schedule-tab" data-day="2">
                <h3>Day 2</h3>
                <span>Aug 14, 2026</span>
            </div>
            <div class="cnc-schedule-tab" data-day="3">
                <h3>Day 3</h3>
                <span>Aug 15, 2026</span>
            </div>
        </div>
        <div class="cnc-schedule-content">
            <div class="cnc-schedule-day active" id="day-1">
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">09:00 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Registration & Welcome Coffee</h4>
                        <p>Network with industry professionals as you register for the event</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">10:00 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Inaugural Session</h4>
                        <p>Official opening ceremony with keynote from industry leaders</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">11:30 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Exhibition Floor Opens</h4>
                        <p>Explore cutting-edge technologies and solutions from 500+ exhibitors</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">02:00 PM</div>
                    <div class="cnc-schedule-details">
                        <h4>Panel: Future of Broadband in India</h4>
                        <p>Industry experts discuss market trends and opportunities</p>
                    </div>
                </div>
            </div>
            
            <div class="cnc-schedule-day" id="day-2">
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">09:30 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Technology Showcase</h4>
                        <p>Live demonstrations of the latest cable and broadband technologies</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">11:00 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Startup Pitch Session</h4>
                        <p>Emerging companies present innovative connectivity solutions</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">01:30 PM</div>
                    <div class="cnc-schedule-details">
                        <h4>Technical Workshop: 5G Integration</h4>
                        <p>Hands-on session on implementing 5G in cable networks</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">04:00 PM</div>
                    <div class="cnc-schedule-details">
                        <h4>Networking Reception</h4>
                        <p>Connect with exhibitors and fellow industry professionals</p>
                    </div>
                </div>
            </div>
            
            <div class="cnc-schedule-day" id="day-3">
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">10:00 AM</div>
                    <div class="cnc-schedule-details">
                        <h4>Industry Awards Ceremony</h4>
                        <p>Recognizing excellence and innovation in the connectivity sector</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">12:00 PM</div>
                    <div class="cnc-schedule-details">
                        <h4>Business Matchmaking</h4>
                        <p>One-on-one meetings between suppliers and service providers</p>
                    </div>
                </div>
                <div class="cnc-schedule-item">
                    <div class="cnc-schedule-time">02:30 PM</div>
                    <div class="cnc-schedule-details">
                        <h4>Closing Session & Next Steps</h4>
                        <p>Event wrap-up and announcements for Cable Net Convergence Expo 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
