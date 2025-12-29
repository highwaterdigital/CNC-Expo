<?php
ob_start();
?>
<style>
.cnc-404-page {
    text-align: center;
    padding: 100px 20px;
    font-family: 'Poppins', sans-serif;
    background: var(--gradient-primary);
    color: #fff;
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.cnc-404-page h1 {
    font-size: 120px;
    font-weight: 700;
    margin: 0;
    line-height: 1;
    text-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.cnc-404-page h2 {
    font-size: 36px;
    font-weight: 600;
    margin: 20px 0;
}
.cnc-404-page p {
    font-family: 'Inter', sans-serif;
    font-size: 18px;
    margin-bottom: 40px;
    max-width: 500px;
}
.cnc-404-home-button {
    display: inline-block;
    padding: 15px 35px;
    background: var(--cnc-blue);
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 600;
    transition: var(--cnc-transition);
    box-shadow: var(--cnc-shadow);
}
.cnc-404-home-button:hover {
    background: var(--cnc-gold);
    color: var(--cnc-black);
    transform: translateY(-3px);
}
</style>
<div class="cnc-404-page">
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="cnc-404-home-button">Go to Homepage</a>
</div>
<?php
return ob_get_clean();
