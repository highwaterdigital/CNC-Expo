<?php 
/**
 * Template Name: CNC Participants
 */

get_header();
?>

<style>
    .participants-wrapper{
        max-width:1400px;
        margin:40px auto;
        padding:0 20px;
        font-family:"Poppins", sans-serif;
    }
    .participants-title{
        text-align:center;
        font-size:42px;
        font-weight:700;
        margin-bottom:10px;
    }
    .participants-sub{
        text-align:center;
        font-size:22px;
        margin-bottom:40px;
        color:#555;
    }
    .participants-section{
        margin-bottom:50px;
    }
    .participants-letter{
        font-size:32px;
        font-weight:700;
        padding:10px 0;
        color:#c88a00; /* CNC Yellow */
        border-bottom:3px solid #c88a00;
        margin-bottom:20px;
    }
    .participants-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill, minmax(260px,1fr));
        gap:20px;
    }
    .participant-box{
        background:#f3f3f3;
        padding:18px 20px;
        border-radius:8px;
        font-size:16px;
        line-height:1.5;
        box-shadow:0 2px 6px rgba(0,0,0,0.08);
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .participant-box:hover{
        transform: translateY(-6px);
        box-shadow:0 6px 20px rgba(0,0,0,0.15);
    }
</style>

<div class="participants-wrapper">

    <h1 class="participants-title">Participants</h1>
    <div class="participants-sub">Our Trusted Participants</div>

<?php
$participants = [

"A" => [
"Adams Probe", "Adel Solutions", "Adish Metals", "ACT TV", "Airtel", "Airtel Xtreme",
"Akshara Technologies", "Alutix", "Ample Signage", "Annapurna Engineering Works",
"Anu Multiplex", "Apurva Enterprises", "Aptron", "Arking", "AS Broadband", "ASGK",
"Aishwaraya technologies", "ASKH Engg", "Astro Digital", "ATN", "Auto Print Multiplex",
"Auxesis Infotech"
],

"B" => [
"BCH Digital", "Bhoomi Communications", "Bhagyanagar Polymers", "Bhagyawathi Enterprises",
"Bharat Broadband", "Bright Enterprises", "Bright Sign", "BSL Broadband", "BSL Digital",
"BSL Networks", "BSM Digital"
],

"C" => [
"CR Fiber Communications", "CCC Digital", "CDN Comnet", "Cera comm", "Citi Communications",
"CLE India", "Cloud Clovis", "CNL", "CNC Networking", "CNC Tech Solutions",
"Comprint Tech Solutions", "Connect Exel", "Cortex", "C-Tech", "Cyberwave"
],

"D" => [
"Dax Networks", "Dendu", "Digisol", "Digital World", "DVR Solutions"
],

"E" => [
"East Coast Digital", "Ecom Express", "Elite Digital", "Ernet India", "Eurostar",
"Excell Broadband", "Excitel", "Extreme Digital", "Evofiber", "Ecomet"
],

"F" => [
"Fastnet", "Fibra Route", "Fibrenet Broadband", "Finix Cable", "Finix Industries",
"Finix Tools", "Focus Tech", "FTTH Tech"
],

"G" => [
"Gadar Solutions", "Galaxynet", "Gateway", "Giga fiber", "Global Link", "Glo Broadband"
],

"H" => [
"Hathway Cable", "Halonix", "Heeyo", "Heritage", "Hertz Cable", "HGCL", "Hi Reach",
"Hi5", "Hifi Cable", "Home Cable", "Huzoor"
],

"I" => [
"Idea Wave Tech", "Indus Net", "Indus Towers", "Indus Satcom", "Infinito", "Infonet",
"Infynect", "Infynect Labs", "Innotech", "Insat", "ISP Union", "ITF World", "ITL Infotech",
"IdeaTek"
],

"J" => [
"Janatha", "JSR Enterprises", "JSR Polymers", "J-Tech", "Jio", "JSP Networks"
],

"K" => [
"Kailash", "Kalpa", "Kare", "Kare Digital", "Kmax Broadband", "KM Broadband", "KNR Cable",
"KNR Digital", "Knet", "KSV Enterprises", "KVM Tech"
],

"L" => [
"Lakshmi Valves", "Latent Digital", "Leotech", "Linkwell", "Luminous", "Linqs"
],

"M" => [
"Macrobit Technologies", "Maheshwara Tech", "Marigold", "Matrix Technologies", "Maxvision",
"Maxtek", "Megacable", "MNR Cable", "MSO Vision", "MSR Cable", "Microchip", "Minix",
"Multicraft Tech", "Mykare"
],

"N" => [
"NAHI TV", "Nandini Enterprises", "Narmada Cable", "Navkar", "Next Wave", "Nexgen", "Nexus",
"Nilkanth Electronics", "Nimbus", "NXT TV"
],

"O" => [
"Opti Nodes", "Optilink", "Optical Power Products", "Ozark", "Ozone", "Orange"
],

"P" => [
"Primecabs", "Polycab", "Powerplus", "Powergrid", "Prism", "Prisma", "Printo", "Protek",
"Panasonic", "Parrot", "Pioneer Links", "Pioneer Cable"
],

"Q" => [
"Quad Networks", "Qmax", "Qube"
],

"R" => [
"Radial Enterprises", "Rajesh Digital", "Ravi Broadband", "RAX", "RC Broadband", "RCN Cable",
"RTN", "Reetech", "Rida Cable", "Ritz Enterprises", "Roshni Infotech", "R-Tech"
],

"S" => [
"Sadguru Digitech", "Sai Sat", "Sai Enterprises", "Samrat", "Sanpada Cable", "Satyam",
"SB Cable", "SB Digital", "SC Digital", "Sea Cable", "Seaways", "Shiva Communications",
"Signum", "Siri Cable", "Siri Digital", "Sky", "Smartcom", "SNY Broadband", "Sonic Fiber",
"Southern Communications", "Spear BroadBand", "Spectrum", "Srashta", "Sri Krishna Enterprise",
"Sun Cable", "Surya Cable"
],

"T" => [
"Tejasree Multiple", "Telenaga", "Tera Digital", "Topup Cable", "Trinet", "Trisnet",
"Turners", "TV9", "TV Vision"
],

"U" => [
"Ultra Airnet", "United Digital", "Unity Broadband"
],

"V" => [
"Vardhaman Electronics", "Vasavi Digital", "Vijetha", "Vmax Digital", "Vmax Broadband",
"Vmax Fiber"
],

"W" => [
"Webcom", "Winmax", "Wings Cable"
],

"X" => [
"XDO Technologies"
],

"Y" => [
"Yelayu Broad", "Yogi TV"
],

"Z" => [
"Zen's Realties", "Zoom Technologies", "Zenet"
],

];

foreach ($participants as $letter => $items) {
    echo '<div class="participants-section">';
    echo '<div class="participants-letter">' . $letter . '</div>';
    echo '<div class="participants-grid">';
    
    foreach ($items as $p) {
        echo '<div class="participant-box">' . esc_html($p) . '</div>';
    }

    echo '</div></div>';
}

?>

</div>

<?php get_footer(); ?>
