/**
 * CNC Main JavaScript
 * Enhanced debug logging for CSS and DOM elements
 * v5.0 - Updated cleanup scan to IGNORE .cnc-header and .cnc-footer
 */

console.log('🚀 CNC Debug: main.js loaded (v5.0)');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 CNC Debug: DOM Content Loaded');
    
    const homeContainer = document.querySelector('.cnc-home, .cnc-home-container, .cnc-home-wrapper');
    console.log('🏠 CNC Debug: Home container found:', !!homeContainer);
    
    if (homeContainer) {
        console.log('✅ CNC Debug: Home container EXISTS');
        const styles = getComputedStyle(homeContainer);
        console.log('🎨 CNC Debug: Container styles:', {
            fontFamily: styles.fontFamily,
            color: styles.color,
            display: styles.display,
            backgroundColor: styles.backgroundColor
        });
    } else {
        // Only warn if we are on the home page (path is / or /home)
        if (window.location.pathname === '/' || window.location.pathname === '/home') {
            // console.warn('❌ CNC Debug: Home container NOT FOUND');
            console.log('ℹ️ CNC Debug: .cnc-home container not found (using Theme Builder layout)');
        } else {
            console.log('ℹ️ CNC Debug: Home container not found (expected on non-home pages)');
        }
    }

    const mainContent = document.querySelector('.cnc-main-content');
    if (mainContent) {
        console.log('📦 CNC Debug: Main content found. Children:', mainContent.children.length);
        console.log('📦 CNC Debug: Main content innerHTML length:', mainContent.innerHTML.length);
        if (mainContent.children.length === 0 && mainContent.innerHTML.trim().length === 0) {
             console.error('❌ CNC Debug: Main content is EMPTY');
        }
    } else {
        // console.error('❌ CNC Debug: Main content element NOT FOUND');
        console.log('ℹ️ CNC Debug: .cnc-main-content not found (likely using Theme Builder layout)');
    }
    
    console.log('✅ CNC Debug: main.js fully loaded');
});


// =========================
// Header & Footer Debug Scan
// v5.0 - This scan now correctly IGNORES the .cnc-header and .cnc-footer
// =========================
/*
(function() {
    'use strict';

    function debugScan() {
        console.log('🔍 CNC Debug: Scanning for unwanted parent theme elements...');
        
        const unwantedSelectors = [
            'header:not(.cnc-header)', // Ignore our header
            'footer:not(.cnc-footer)', // Ignore our footer
            '.site-header',
            '.site-footer',
            '#masthead',
            '#colophon',
            '.page-header',
            '.entry-header',
            'h1.entry-title'
        ];
        
        let unwantedFound = 0;

        unwantedSelectors.forEach(selector => {
            try {
                const elements = document.querySelectorAll(selector);
                elements.forEach((el, i) => {
                    // Check if this element is inside our header/footer
                    if (el.closest('.cnc-header') || el.closest('.cnc-footer')) {
                        return; // This is part of our components, skip it
                    }
                    
                    console.warn(`🚫 Found unwanted element: ${selector}`, el);
                    unwantedFound++;
                    
                    // Mark for visual debugging
                    el.style.border = '3px solid red';
                    el.style.outline = '3px solid red';
                    el.setAttribute('data-cnc-debug', 'unwanted-theme-element');
                });
            } catch(e) {
                console.log(`Could not check selector: ${selector}`);
            }
        });
        
        if (unwantedFound === 0) {
            console.log('✅ CNC Debug: No unwanted parent theme elements found.');
        } else {
            console.warn(`Total unwanted elements found: ${unwantedFound}`);
        }

        // Check DOM structure
        console.log('🔍 CNC Debug: DOM Structure Analysis:');
        console.log('  <body> children:', document.body.children.length);
        Array.from(document.body.children).forEach((child, i) => {
            if (i < 15) { // Only log the first 15 to keep it clean
                 console.log(`    ${i + 1}. ${child.tagName}.${child.className || 'no-class'}`);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', debugScan);
    } else {
        debugScan();
    }
})();
*/