(function() {
    'use strict';
    
    console.log('✅ CNC v7.0: Animation system initialized');

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                
                // Add animation class based on data attribute
                if (element.dataset.animate) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
                
                // Animate child elements
                animateChildren(element);
                
                // Stop observing after animation
                observer.unobserve(element);
            }
        });
    }, observerOptions);

    // Animate child elements with stagger
    function animateChildren(parent) {
        const cards = parent.querySelectorAll('.cnc-highlight-card, .cnc-metric, .cnc-exhibitor-card, .cnc-testimonial-card, .cnc-schedule-item, .cnc-venue-feature, .cnc-sponsor-logo, .cnc-about__content');
        
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animate-in');
            }, index * 100);
        });
    }

    // Apply initial animation styles
    function initAnimations() {
        const sections = document.querySelectorAll('[data-animate]');
        sections.forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
        
        console.log(`🎬 Observing ${sections.length} sections for animations`);
    }

    // Schedule tab functionality
    function initScheduleTabs() {
        const tabs = document.querySelectorAll('.cnc-schedule-tab');
        const days = document.querySelectorAll('.cnc-schedule-day');
        
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', function() {
                // Remove active class from all
                tabs.forEach(t => t.classList.remove('active'));
                days.forEach(d => d.classList.remove('active'));
                
                // Add active to clicked
                this.classList.add('active');
                if (days[index]) {
                    days[index].classList.add('active');
                    
                    // Re-animate schedule items
                    const items = days[index].querySelectorAll('.cnc-schedule-item');
                    items.forEach((item, i) => {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(-30px)';
                        setTimeout(() => {
                            item.classList.add('animate-in');
                        }, i * 100);
                    });
                }
                
                console.log(`📅 Switched to day ${index + 1}`);
            });
        });
    }

    // Animated counter for metrics
    function animateCounter(element, target) {
        let current = 0;
        const increment = target / 50;
        const duration = 1500;
        const stepTime = duration / 50;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + (target >= 1000 ? '+' : '');
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
        }, stepTime);
    }

    // Video controls functionality
    function initVideoControls() {
        const videoToggle = document.getElementById('cnc-video-toggle');
        const videoSound = document.getElementById('cnc-video-sound');
        const videoIframe = document.getElementById('cnc-hero-video');
        let isPlaying = true;
        let isMuted = true;
        
        if (videoToggle && videoIframe) {
            videoToggle.addEventListener('click', function() {
                const icon = this.querySelector('.cnc-video-icon');
                const text = this.querySelector('.cnc-video-text');
                
                if (isPlaying) {
                    const currentSrc = videoIframe.src;
                    videoIframe.src = currentSrc.replace('autoplay=1', 'autoplay=0');
                    icon.textContent = '▶️';
                    text.textContent = 'Play Video';
                    isPlaying = false;
                } else {
                    const currentSrc = videoIframe.src;
                    videoIframe.src = currentSrc.replace('autoplay=0', 'autoplay=1');
                    icon.textContent = '⏸️';
                    text.textContent = 'Pause Video';
                    isPlaying = true;
                }
            });
        }
        
        if (videoSound && videoIframe) {
            videoSound.addEventListener('click', function() {
                const icon = this.querySelector('.cnc-video-icon');
                
                if (isMuted) {
                    const currentSrc = videoIframe.src;
                    videoIframe.src = currentSrc.replace('mute=1', 'mute=0');
                    icon.textContent = '🔊';
                    isMuted = false;
                } else {
                    const currentSrc = videoIframe.src;
                    videoIframe.src = currentSrc.replace('mute=0', 'mute=1');
                    icon.textContent = '🔇';
                    isMuted = true;
                }
            });
        }
        
        // Initialize video control icons
        setTimeout(() => {
            if (videoToggle) {
                const icon = videoToggle.querySelector('.cnc-video-icon');
                const text = videoToggle.querySelector('.cnc-video-text');
                icon.textContent = '⏸️';
                text.textContent = 'Pause Video';
            }
        }, 2000);
    }

    // Counter animation for metrics
    function initMetricsCounter() {
        const metricsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.cnc-metric-number');
                    counters.forEach(counter => {
                        const text = counter.textContent;
                        const number = parseInt(text.replace(/[^\d]/g, ''));
                        if (number && !counter.dataset.animated) {
                            counter.dataset.animated = 'true';
                            animateCounter(counter, number);
                        }
                    });
                    metricsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        const metricsSection = document.querySelector('.cnc-metrics');
        if (metricsSection) {
            metricsObserver.observe(metricsSection);
        }
    }

    // Smooth scroll for anchor links
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '#0') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'start' 
                        });
                    }
                }
            });
        });
    }

    // Initialize everything when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    function init() {
        console.log('🚀 CNC Animations: Initializing...');
        initAnimations();
        initScheduleTabs();
        initVideoControls();
        initMetricsCounter();
        initSmoothScroll();
        console.log('✨ CNC Animations: All systems ready!');
    }

})();
