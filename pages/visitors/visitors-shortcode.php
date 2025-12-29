<?php
ob_start();
?>
<style>
.cnc-visitors-page {
    font-family: 'Inter', sans-serif;
    color: var(--cnc-black);
    padding: 80px 0;
    background: #fff;
}

.cnc-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.cnc-page-header {
    text-align: center;
    margin-bottom: 60px;
}

.cnc-page-header h1 {
    font-family: 'Poppins', sans-serif;
    font-size: 48px;
    font-weight: 700;
    color: var(--cnc-purple);
    margin: 0 0 10px;
}

.cnc-page-header p {
    font-size: 18px;
    color: #555;
    max-width: 700px;
    margin: 0 auto;
}

.cnc-visitors-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
}

.cnc-visitors-content img {
    width: 100%;
    border-radius: var(--cnc-radius);
    box-shadow: var(--cnc-shadow);
}

.cnc-visitors-text h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 32px;
    color: var(--cnc-magenta);
    margin-bottom: 20px;
}

.cnc-visitors-text p {
    line-height: 1.8;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .cnc-visitors-content {
        grid-template-columns: 1fr;
    }
    .cnc-page-header h1 {
        font-size: 36px;
    }
}

/* Form Styles */
.cnc-visitor-form-section {
    background: #f9f9f9;
    padding: 80px 0;
    margin-top: 60px;
}

.cnc-visitor-form-container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 40px;
    border-radius: var(--cnc-radius);
    box-shadow: var(--cnc-shadow);
}

.cnc-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.cnc-form-group {
    margin-bottom: 20px;
}

.cnc-form-group.full-width {
    grid-column: span 2;
}

.cnc-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--cnc-black);
}

.cnc-form-group input,
.cnc-form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-size: 16px;
}

.cnc-submit-btn {
    background: var(--gradient-primary);
    color: #fff;
    border: none;
    padding: 15px 40px;
    font-size: 18px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    transition: var(--cnc-transition);
    width: 100%;
    margin-top: 20px;
}

.cnc-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(198, 48, 140, 0.4);
}

.cnc-form-message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    display: none;
    text-align: center;
}

.cnc-form-message.success {
    background: #d4edda;
    color: #155724;
    display: block;
}

.cnc-form-message.error {
    background: #f8d7da;
    color: #721c24;
    display: block;
}

@media (max-width: 768px) {
    .cnc-form-grid {
        grid-template-columns: 1fr;
    }
    .cnc-form-group.full-width {
        grid-column: span 1;
    }
    .cnc-visitor-form-container {
        padding: 20px;
    }
}
</style>

<div class="cnc-visitors-page">
    <div class="cnc-container">
        <div class="cnc-page-header">
            <h1>Why Visit the Expo?</h1>
            <p>Discover the future of cable, broadband, and internet technology. Connect with industry leaders, explore cutting-edge products, and gain invaluable insights.</p>
        </div>

        <div class="cnc-visitors-content">
            <div class="cnc-visitors-text">
                <h2>Connect, Learn, and Grow</h2>
                <p>The Cable Net Convergence Expo is the premier event for professionals in the telecommunications and media industries. As a visitor, you will have the unique opportunity to network with exhibitors from across the globe, showcasing the latest advancements in network infrastructure, streaming technology, and customer solutions.</p>
                <p>Attend insightful seminars, participate in hands-on workshops, and see live demonstrations of the technologies that are shaping the future of connectivity. Whether you are a network operator, content provider, or technology enthusiast, this expo is your gateway to the next wave of innovation.</p>
            </div>
            <div>
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/placeholder-visitors.jpg" alt="Networking at the expo">
            </div>
        </div>
    </div>

    <div class="cnc-visitor-form-section">
        <div class="cnc-container">
            <div class="cnc-page-header">
                <h1>Visitor Registration</h1>
                <p>Secure your spot at the expo. Register now for free entry.</p>
            </div>
            
            <div class="cnc-visitor-form-container">
                <form id="cnc-visitor-form">
                    <div class="cnc-form-grid">
                        <div class="cnc-form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" required>
                        </div>
                        <div class="cnc-form-group">
                            <label>Email Address *</label>
                            <input type="email" name="email" required>
                        </div>
                        <div class="cnc-form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" required>
                        </div>
                        <div class="cnc-form-group">
                            <label>Company Name</label>
                            <input type="text" name="company">
                        </div>
                        <div class="cnc-form-group">
                            <label>Designation</label>
                            <input type="text" name="designation">
                        </div>
                        <div class="cnc-form-group">
                            <label>City</label>
                            <input type="text" name="city">
                        </div>
                        <div class="cnc-form-group full-width">
                            <label>Area of Interest</label>
                            <select name="interest">
                                <option value="">Select an option</option>
                                <option value="Networking">Networking</option>
                                <option value="Products">New Products</option>
                                <option value="Seminars">Seminars & Workshops</option>
                                <option value="Business">Business Opportunities</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <input type="hidden" name="action" value="cnc_register_visitor">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cnc_visitor_nonce'); ?>">
                    
                    <button type="submit" class="cnc-submit-btn">Register Now</button>
                    <div id="cnc-form-message" class="cnc-form-message"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cnc-visitor-form');
    const messageDiv = document.getElementById('cnc-form-message');
    
    // Define AJAX URL safely
    const cnc_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = form.querySelector('button');
            const originalText = btn.innerText;
            btn.innerText = 'Processing...';
            btn.disabled = true;
            messageDiv.className = 'cnc-form-message';
            messageDiv.style.display = 'none';
            
            const formData = new FormData(form);
            
            fetch(cnc_ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerText = data.data.message;
                    messageDiv.classList.add('success');
                    form.reset();
                } else {
                    messageDiv.innerText = data.data.message || 'An error occurred.';
                    messageDiv.classList.add('error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerText = 'Connection error. Please try again.';
                messageDiv.classList.add('error');
            })
            .finally(() => {
                btn.innerText = originalText;
                btn.disabled = false;
                messageDiv.style.display = 'block';
            });
        });
    }
});
</script>

<?php
return ob_get_clean();
