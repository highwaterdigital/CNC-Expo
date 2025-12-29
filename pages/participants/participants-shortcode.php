<?php
ob_start();
?>
<style>
    .participants-wrapper {
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 22px 80px;
        font-family: 'Poppins', sans-serif;
        color: #242424;
    }
    .participants-hero {
        text-align: center;
        padding-bottom: 40px;
    }
    .participants-hero h1 {
        font-size: clamp(3rem, 4vw, 4.5rem);
        margin-bottom: 0.75rem;
        color: #0A372E;
    }
    .participants-hero p {
        font-size: 1.1rem;
        color: rgba(36, 36, 36, 0.7);
    }
    .participants-letter {
        font-size: 2rem;
        font-weight: 700;
        color: #D9B451;
        border-bottom: 3px solid #D9B451;
        padding-bottom: 6px;
        margin-bottom: 20px;
        display: inline-block;
        animation: slideUp 0.6s ease forwards;
    }
    .participants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-bottom: 40px;
    }
    .participant-box {
        background: #FFFFFF;
        padding: 16px 18px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .participant-box:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.12);
    }
    .participant-box span {
        display: block;
        font-size: 1rem;
    }
    .footer-cta {
        margin: 60px 0 20px;
        background: linear-gradient(135deg, #D9B451, #E8C878);
        padding: 28px;
        border-radius: 12px;
        text-align: center;
        color: #0A372E;
        font-weight: 700;
        letter-spacing: 0.08em;
    }
    .footer-cta button {
        margin-top: 12px;
        background: #0A372E;
        color: #FFFFFF;
        border: none;
        padding: 14px 28px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="participants-wrapper">
    <div class="participants-hero">
        <h1>Participants</h1>
        <p>Our Trusted Participants</p>
    </div>

    <?php
    $participants = [
        "A" => ["A E Tel", "Accord Power", "ACE Microelectronics", "Aceware Fintech", "ACT", "Aggressive Digital", "Aishwarya Technologies", "Alcon", "Alibaba", "Alliance Broadband", "Alot Solutions", "Alpha Bridge Technologies", "Alquench", "Amigo", "AP Cable Times", "Aplomb", "ASCPL", "Asianet", "Assistive Netspeed Technologies", "Asttecss", "Avishkaar", "AVT Networks", "Axiom Energy"],
        "B" => ["BCN Digital", "Benison Technologies", "Bharath Pay", "Bharathi Enterprises", "Bheem TV", "Blue Lotus", "BME", "Branding", "Brightway Communications", "Bydesign India", "Bytize"],
        "C" => ["C-Net Communications", "CABLE NET CONVERGENCE", "Cable Quest", "Cable Samachar", "Cable Samacharam", "Candid Optronix", "Catvision", "CD-Tech (IVB-7)", "Centrium Digital", "Chandana Electronics", "Channel Master", "Cintamani Computers", "Citi Digital", "City Online", "Claron", "Clear Communications", "Cloud Infotech", "Com Core (3C)", "Comsfiber", "Comtech Digitronics", "Corpus", "Corvids", "Coship", "CP Enterprises", "Crypto Guard", "Current Optronics", "Custom Website Design by Markay Technologies", "Cyber FTTH Mart", "Cyber Optic"],
        "D" => ["D Enable Visual Builder", "D-Link", "DBC", "De-Cix", "Deepak Overseas", "Denky Power", "DEPL", "Deviser", "Digital Matrix", "Digital Rupay Digisol", "Discovery", "Dish TV", "DK Enterprises / N M Impex", "Dooravani Communications", "Dr Com", "Drone Edge", "Dwan Supports", "DYNAMIC PE"],
        "E" => ["E Life", "E TV", "East Photonics", "Ecaps Computers", "Efficient Digital", "Elememtal", "Elitecore", "Ericsson", "ESPN", "ESSCI", "Euro Digital", "Eurostar", "Evolgance", "Exalto", "Excitel Broadband"],
        "F" => ["F2TH Communications", "Fame Media", "Fast Tech", "Feilder", "Fiberfox India", "Fibersol", "Fifo Technologies", "Florida", "FTTH Mart", "Fujikura", "Fujitech"],
        "G" => ["Gaian Solutions", "Gallery", "Geberit", "Globe Tech", "GOIP", "GOSPEL", "Gradiant Networks", "GTPL", "Gurudatta"],
        "H" => ["Harsha Cables", "Hathway", "HBEL", "HCC", "Henrich", "HFC", "HMTV", "Hodu Soft", "Home", "Home Vu", "HRDEL Cable", "HUAWEI"],
        "I" => ["Icon Networks", "Icon Wave Tech", "Idea Bytes", "Ilsintech", "Impact SMS", "Imperius Infotech", "INB", "Infobit Inc", "Infonet Solutions (Netstar)", "ING", "Inno Instruments", "Intronics", "Invas Technologies", "Invict", "Inyogo Infynect", "Irdeta", "Irevomm", "ITE & C Dept Govt of Telangana", "ITP World"],
        "J" => ["Jainhits", "Jaze Networks", "Jekath Fibertronics", "JK Electronics", "JRS Communications", "JVM Tech"],
        "K" => ["Kalpin Kapil IT", "KBS Digital Service Platform", "Keith Electronics", "Khushi Communications", "Kine Scope", "KingVon", "KMTS Solid", "KP Technologies", "Kruass International"],
        "L" => ["Ladder Wala", "Lalith Electronics", "Lapis Lightron", "Legrand", "Limras Eronet", "Logic Eastern", "Logosys", "Lotus Broadband", "LRIPL", "Lukup"],
        "M" => ["M-Core Technologies", "M2M OTT", "M3 Electronics", "MAA TV", "Macrothink", "Maheshwari Electronics", "Markay Technologies", "MCBS", "Media Soft", "Mega TV", "Mehta Infocomm", "Metro Electronics", "Mettle Networks", "MG Electronics", "Mi TV", "Minnu Web Solutions", "Mithril Telecom", "Mobo Collector", "Mukund Industries", "Multicraft", "Multilink Computers", "Multiple Electronics", "Multivirt", "Mumbai IX", "MX"],
        "N" => ["Naksh Infotech", "Nayaseva", "Netfox Intelligent Networks", "NetLife", "Netlink ICT", "Netro Networks", "Netrotonics", "Netsat", "Network E Labs", "New", "New Mangal Electronics", "Newland", "Next Innovation", "NEXT Trillion", "Next Vision", "Nirmala Cables", "Nitin Electronics", "NNNanak", "Novastar", "NTV", "Nuovafil Infotech", "NXT Digital"],
        "O" => ["OFR Telecom", "One Stop Entertainment", "One Take Media", "OPL", "Opplin", "Optilink", "Optinua Technologies", "Optivision", "Orient Cables", "Original Products", "OTT Play"],
        "P" => ["Panaccess", "Pay Cable", "PDR Videotronics", "Peercast", "Pelorus", "Phando", "Pioneer", "Pioneer E Labs", "Polycab", "Polywires", "Prathap Industries", "Prathem Links", "Preciso", "Procom Pvt Ltd"],
        "Q" => [],
        "R" => ["R&M India", "Radius Telecom", "Rahul Commerce", "Rahul Industries", "Railtel", "Rajguru", "RC NET", "Recibo", "Rectus India", "Redington", "Reliablesoft", "Resonet Systems", "RIEPL", "RR Industries", "RS Computers TP Link", "RUR Telenet", "Ryax Technology"],
        "S" => ["Safeview", "SagarTronics", "Sattilite@Internet", "Savit Broadband Networks", "Scat", "Scopus International", "SCTE", "SecureTV", "SG Belden", "SharpPlus", "Sharpvision", "Shyam Electronics & Magnetics", "SignumTV", "SitiCable", "SkillIndia", "SkyWire Broadcast", "Smart Digital Service", "Smartlink", "SMS Striker", "Sokhi Extrusion", "SONY", "Space", "SpaceCom", "SPI Engineers Spec", "SriTechnologies", "SSabot One", "SSLC Smart Play", "StarTechnologies", "STBTechnologies", "Stel Fiber", "StudioN", "SumitomoElectric", "SunnyDigital", "SunskySoftware", "SuperCommAsia", "Superfill", "Surbhi", "SVBC", "SVFcommunications", "Syrotech"],
        "T" => ["T Fiber", "T Sat", "TaraConsultants", "TataTeleservices", "TCL", "TechnologyNext", "Tejasridigital", "Telangana Tourism", "Telogica", "TNT Cybertronics", "TPLink", "Tricom", "Tricube", "Triflex", "TV5", "TV9"],
        "U" => ["ULKA TV", "UNI Way", "Unique Core", "Usha Martin", "UVC Group"],
        "V" => ["VCV Digital", "Velankini Electronics", "Vinsat Digital", "Visisht Digital", "Voltek", "Vortex Infotech", "VP Broadband"],
        "W" => ["Wellmark", "Welltech", "Willagies", "Willet", "WorldPhone"],
        "X" => ["XDB Technologies"],
        "Y" => ["YUPP TV"],
        "Z" => ["Zebyte Rentals", "Zenith Technologies", "Zest Net Technology", "Zyetek"],
    ];

    foreach ($participants as $letter => $items) {
        echo '<div class="participants-section">';
        echo '<div class="participants-letter">' . esc_html($letter) . '</div>';
        echo '<div class="participants-grid">';
        foreach ($items as $participant) {
            echo '<div class="participant-box"><span>' . esc_html($participant) . '</span></div>';
        }
        echo '</div></div>';
    }
    ?>

    <div class="footer-cta">
        Be part of the Cable Net Convergence 2025
        <button onclick="window.location.href='<?php echo esc_url(home_url('/register')); ?>'">Register Your Brand</button>
    </div>
</div>

<?php
return ob_get_clean();
