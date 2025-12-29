<?php
/**
 * Nearby Stays Page
 */
if (!defined('ABSPATH')) {
    exit;
}

ob_start();
?>
<section style="padding:120px 0 80px;background:#0a0616;color:#fff;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;">
        <p style="color:#C8FF33;letter-spacing:0.12em;font-weight:700;text-transform:uppercase;margin-bottom:8px;">Plan Your Stay</p>
        <h1 style="font-family:'Poppins',sans-serif;font-size:40px;margin:0 0 16px;">Nearby Stays &amp; Hotels</h1>
        <p style="max-width:820px;line-height:1.8;margin:0 0 28px;color:rgba(255,255,255,0.78);">
            Hotels and accommodations close to HITEX Exhibition Center. Explore the map for quick directions.
        </p>
        <div id="cnc-map" style="width:100%;height:480px;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);"></div>
    </div>
</section>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('cnc-map').setView([17.470564521558728, 78.37508475401538], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const locations = [
        { name: 'HITEX Expo Center (Venue)', coords: [17.470564521558728, 78.37508475401538] },
        { name: 'Novotel Hyderabad', coords: [17.47290366779703, 78.3727733475503] },
    ];

    locations.forEach(loc => {
        L.marker(loc.coords).addTo(map).bindPopup(loc.name);
    });
});
</script>
<?php
return ob_get_clean();
