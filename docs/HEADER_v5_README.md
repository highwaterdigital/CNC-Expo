# CNC Expo Header v5.0 - Transparent Design with Sliding Participant Logos

## ✨ What's New

### Modern Transparent Header
- **Fixed position header** with transparent background
- **Logo on left** - Clean and visible
- **Menu toggle on right** - Modern hamburger menu
- **Scrolled state** - Semi-transparent background with blur effect
- **Responsive design** - Optimized for all devices

### Side Navigation Menu
- **Full-screen overlay** with smooth slide-in animation
- **Organized sections**: Exhibition, Visitor, About
- **CTA buttons** integrated into menu
- **Contact info & social links** at bottom
- **Click anywhere to close** intuitive UX

### Participant Logos Slider
- **Auto-scrolling animation** - Smooth infinite loop
- **Hover to pause** - User-controlled browsing
- **Grayscale effect** - Colorizes on hover
- **Seamless loop** - Duplicated items for continuous scrolling
- **Gradient edges** - Professional fade-out effect

## 📁 Files Updated

### Header Component
- `components/header/header.php` - Complete rewrite
- `components/header/header-old.backup` - Original backed up

### Participants Slider
- `components/participants/participants-slider.php` - NEW component
- `assets/img/participants/` - Logo storage directory
- Shortcode: `[cnc_participants_slider]`

### Functions
- Added `cnc_participants_slider_shortcode()` in `functions.php`

## 🎨 Design Features

### Color Scheme
```css
--cnc-black: #0D0D0D
--cnc-white: #FFFFFF
--cnc-lime: #CDFF00
--cnc-purple: #5E3A8E
--cnc-magenta: #C6308C
--cnc-blue: #0090D9
```

### Typography
- **Poppins** - Headers & navigation (600-700 weight)
- **Inter** - Body text (400-500 weight)

### Animations
- **Fade in down** - Header entrance
- **Scroll left** - Participant logos (30s loop)
- **Transform scale** - Logo hover effect
- **Slide right** - Menu panel transition

## 🚀 Usage

### Adding the Participant Slider

**In any page/post:**
```php
[cnc_participants_slider]
```

**In PHP template:**
```php
<?php echo do_shortcode('[cnc_participants_slider]'); ?>
```

**In home-shortcode.php (already integrated):**
```php
include get_stylesheet_directory() . '/components/participants/participants-slider.php';
```

### Adding Participant Logos

1. Save logo images to: `assets/img/participants/`
2. Update array in `participants-slider.php`:

```php
$participants = array(
    array(
        'name' => 'Company Name',
        'logo' => 'company-logo.png'
    ),
    // Add more...
);
```

### Logo Specifications
- **Format**: PNG with transparent background (preferred)
- **Size**: 400x200px (2:1 ratio recommended)
- **Quality**: High-res for retina displays
- **File size**: < 100KB for fast loading

## 🎯 Menu Structure

### Exhibition Section
- Exhibitor Profile
- Space Booking Form
- 2026 Brochure
- Floor Plan
- 2025 Post Show Report
- Exhibition Venue & Dates

### Visitor Section
- Visitor Profile
- Event Schedule
- Photo Gallery

### About Section
- About CNC Expo
- Contact Us
- Our Sponsors

## 📱 Responsive Breakpoints

- **Desktop**: > 1024px - Full menu visible
- **Tablet**: 768px - 1024px - Compact header
- **Mobile**: < 768px - Ultra-compact with drawer menu

## 🔧 Customization

### Changing Slider Speed
```css
.cnc-participants__slider {
    animation: scroll-left 30s linear infinite; /* Change 30s */
}
```

### Changing Logo Display Time
Increase gap between logos:
```css
.cnc-participants__slider {
    gap: 4rem; /* Increase for more spacing */
}
```

### Removing Grayscale Effect
```css
.cnc-participants__logo {
    filter: grayscale(0%); /* Already colored */
    opacity: 1;
}
```

## ✅ Browser Compatibility

- ✅ Chrome/Edge 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 🐛 Troubleshooting

### Header not transparent?
- Check if video background is loaded in hero section
- Verify header is not scrolled on page load

### Logos not showing?
1. Check file paths: `assets/img/participants/company-logo.png`
2. Verify file permissions (644)
3. Clear WordPress cache
4. Check browser console for 404 errors

### Menu not opening?
1. Check JavaScript console for errors
2. Verify `cnc-mobile-toggle` button exists
3. Test on different browsers

### Slider not animating?
1. Check CSS animation support
2. Verify duplicated items exist
3. Test with browser dev tools

## 📞 Support Links

- Email: info@cncexpo.com
- Phone: +91 9505 050 007
- Website: [CNC Expo Hyderabad](https://www.cncexpo.com)

## 🎉 Convergence India Inspiration

This design is inspired by the modern, clean aesthetic of Convergence India website with:
- Transparent header overlaying hero content
- Side navigation drawer for clean UI
- Sliding partner logos for social proof
- Mobile-first responsive approach
- Smooth animations and transitions

---

**Version**: 5.0.0  
**Last Updated**: 2024-01-20  
**Compatibility**: WordPress 5.8+, PHP 7.4+
