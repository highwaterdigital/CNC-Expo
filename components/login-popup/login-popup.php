<?php
/**
 * Component: CNC Expo Login Popup
 * Handles OTP-based login/registration for customers
 * Integrates with existing REST API endpoints and expo-login.js
 *
 * @package cnc_child
 */
if (!defined('ABSPATH')) exit;

/**
 * Render login/register popup HTML in footer
 */
add_action('wp_footer', 'cnc_expo_login_popup_html');
function cnc_expo_login_popup_html() {
    // Skip if admin or user already logged in as admin
    if (is_admin() || (is_user_logged_in() && current_user_can('manage_options'))) {
        return;
    }
    
    $dashboard_url = home_url('/my-account');
    ?>
    <div id="expo-login-modal" class="expo-login-modal" hidden>
        <div class="expo-login-content">
            <button class="expo-login-close" type="button" aria-label="Close">&times;</button>
            
            <div class="expo-login-header">
                <h2>Welcome to CNC Expo 2026</h2>
                <p>Login or register to manage your bookings</p>
            </div>
            
            <form id="expo-login-form" class="expo-login-form">
                <div class="expo-form-group">
                    <label for="expo-login-phone">Mobile Number</label>
                    <div class="expo-phone-input-wrapper">
                        <span class="expo-country-code">+91</span>
                        <input 
                            type="tel" 
                            id="expo-login-phone" 
                            name="phone" 
                            placeholder="Enter 10-digit mobile number"
                            pattern="[0-9]{10}"
                            maxlength="10"
                            required
                        >
                    </div>
                </div>
                
                <div class="expo-form-group">
                    <label for="expo-login-email">Email Address</label>
                    <input 
                        type="email" 
                        id="expo-login-email" 
                        name="email" 
                        placeholder="your@email.com"
                        required
                    >
                </div>
                
                <div class="expo-form-group expo-otp-field" hidden>
                    <label for="expo-login-otp">Enter OTP</label>
                    <input 
                        type="text" 
                        id="expo-login-otp" 
                        name="otp" 
                        placeholder="6-digit OTP"
                        pattern="[0-9]{6}"
                        maxlength="6"
                    >
                    <p class="expo-form-hint">Check your phone for the OTP</p>
                </div>
                
                <p class="expo-login-status"></p>
                
                <button type="submit" class="expo-btn expo-btn--primary">
                    Send OTP
                </button>
            </form>
            
            <div class="expo-login-footer">
                <p>By continuing, you agree to our Terms & Privacy Policy</p>
            </div>
        </div>
    </div>
    
    <div id="expo-login-backdrop" class="expo-login-backdrop" hidden></div>
    <?php
}

/**
 * Enqueue login popup styles
 */
add_action('wp_footer', 'cnc_expo_login_popup_styles', 5);
function cnc_expo_login_popup_styles() {
    if (is_admin()) return;
    ?>
    <style id="expo-login-popup-styles">
        /* Login Modal */
        .expo-login-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100001;
            width: 90%;
            max-width: 450px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translate(-50%, -40%); opacity: 0; }
            to { transform: translate(-50%, -50%); opacity: 1; }
        }

        .expo-login-modal[hidden] {
            display: none;
        }

        .expo-login-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 100000;
        }

        .expo-login-backdrop[hidden] {
            display: none;
        }

        .expo-login-content {
            padding: 32px 28px;
            position: relative;
        }

        .expo-login-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 28px;
            line-height: 1;
            color: #999;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }

        .expo-login-close:hover {
            color: #333;
        }

        .expo-login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .expo-login-header h2 {
            margin: 0 0 8px;
            font-size: 24px;
            font-weight: 700;
            color: var(--cnc-purple, #5E3A8E);
        }

        .expo-login-header p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        .expo-login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .expo-form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .expo-form-group label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .expo-phone-input-wrapper {
            display: flex;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .expo-phone-input-wrapper:focus-within {
            border-color: var(--cnc-magenta, #C6308C);
        }

        .expo-country-code {
            background: #f5f5f5;
            padding: 12px 14px;
            font-weight: 600;
            color: #666;
            border-right: 1px solid #e0e0e0;
            user-select: none;
        }

        .expo-phone-input-wrapper input {
            flex: 1;
            border: none;
            padding: 12px 14px;
            font-size: 16px;
            outline: none;
        }

        .expo-form-group input[type="email"],
        .expo-form-group input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.2s;
        }

        .expo-form-group input:focus {
            border-color: var(--cnc-magenta, #C6308C);
            outline: none;
        }

        .expo-form-hint {
            margin: 4px 0 0;
            font-size: 13px;
            color: #888;
        }

        .expo-login-status {
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            min-height: 20px;
            color: var(--cnc-purple, #5E3A8E);
        }

        .expo-btn {
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .expo-btn--primary {
            background: linear-gradient(135deg, var(--cnc-purple, #5E3A8E) 0%, var(--cnc-magenta, #C6308C) 100%);
            color: #fff;
        }

        .expo-btn--primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(198, 48, 140, 0.3);
        }

        .expo-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .expo-login-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .expo-login-footer p {
            margin: 0;
            font-size: 12px;
            color: #999;
        }

        body.expo-login-open {
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .expo-login-modal {
                width: 95%;
                max-width: none;
            }

            .expo-login-content {
                padding: 28px 20px;
            }
        }
    </style>
    <?php
}

// Note: expoLogin data is now localized in functions.php via wp_localize_script
