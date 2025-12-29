# 🎨 CNC Expo v5.0 Visual Guide

## 📋 Overview

Modern transparent header inspired by **Convergence India** with:
- ✅ Transparent header with video background
- ✅ Logo on left, menu toggle on right
- ✅ Side navigation drawer
- ✅ Auto-scrolling participant logos at bottom

---

## 🎯 Header Layout (Desktop)

```
┌──────────────────────────────────────────────────────────────────┐
│  [LOGO]                                          [☰ EXPO MENU]   │ ← Transparent Header
└──────────────────────────────────────────────────────────────────┘
                         VIDEO PLAYING BELOW
```

**States:**
- **Default**: Fully transparent, logo visible
- **Scrolled**: Semi-transparent white background with blur effect

---

## 📱 Header Layout (Mobile)

```
┌─────────────────────────────────┐
│ [LOGO]              [☰]         │ ← Ultra Compact
└─────────────────────────────────┘
```

**Hamburger Menu:**
```
                    ┌─────────────────┐
                    │  GET STARTED    │
                    │  □ Book Now     │
                    │  □ Register     │
                    ├─────────────────┤
                    │  EXHIBITION     │
                    │  > Exhibitors   │
                    │  > Floor Plan   │
                    │  > Brochure     │
                    ├─────────────────┤
                    │  VISITOR        │
                    │  > Register     │
                    │  > Schedule     │
                    │  > Gallery      │
                    ├─────────────────┤
                    │  ABOUT          │
                    │  > About Us     │
                    │  > Contact      │
                    │  > Sponsors     │
                    └─────────────────┘
```

---

## 🎪 Participant Logos Slider

```
┌──────────────────────────────────────────────────────────────────────┐
│                      🤝 OUR PARTICIPANTS                              │
│              Trusted by industry leaders across India                 │
├──────────────────────────────────────────────────────────────────────┤
│                                                                        │
│  [Haas] [DMG] [Mazak] [Fanuc] [Siemens] [Makino] → → → (scrolling)  │
│                                                                        │
└──────────────────────────────────────────────────────────────────────┘
```

**Features:**
- ✅ Auto-scrolls left (30 seconds loop)
- ✅ Grayscale → color on hover
- ✅ Pause on hover
- ✅ Seamless infinite loop
- ✅ Gradient fade edges

---

## 🎨 Color Palette

### Primary Colors
```
┌─────────┬─────────┬─────────┬─────────┐
│ #0D0D0D │ #CDFF00 │ #C6308C │ #5E3A8E │
│  Black  │  Lime   │ Magenta │ Purple  │
└─────────┴─────────┴─────────┴─────────┘
```

### Secondary Colors
```
┌─────────┬─────────┐
│ #0090D9 │ #FFFFFF │
│  Blue   │  White  │
└─────────┴─────────┘
```

---

## 🎬 Animation Timeline

### Page Load (0-1s)
```
0.0s → Header fades in from top ▼
0.2s → Logo scales up (1.05x)
0.4s → Menu toggle fades in
```

### Scroll Behavior
```
Scroll > 50px → Header background: rgba(13,13,13,0.95)
              → Backdrop blur: 20px
              → Border bottom: magenta
```

### Menu Toggle Animation
```
Click → Hamburger ☰ transforms to ✕
     → Overlay fades in (0.3s)
     → Menu slides from right (0.4s cubic-bezier)
     → Body overflow: hidden
```

### Logo Slider Animation
```
Continuous → translateX(-50%) over 30s
Hover      → animation-play-state: paused
```

---

## 📐 Responsive Breakpoints

| Device | Width | Header Height | Logo Height | Menu |
|--------|-------|---------------|-------------|------|
| Desktop | > 1024px | 80px | 60px | Side drawer |
| Tablet | 768-1024px | 70px | 55px | Side drawer |
| Mobile | < 768px | 50px | 40px | Full-screen drawer |

---

## 🔧 Component Structure

```
components/
├── header/
│   ├── header.php (v5.0 - NEW)
│   └── header-old.backup
│
├── participants/
│   └── participants-slider.php (NEW)
│
└── footer/
    └── footer.php

pages/
└── home/
    ├── home-shortcode.php (includes participants)
    └── sections/
        ├── hero.php
        ├── highlights.php
        ├── metrics.php
        ├── about.php
        ├── exhibitors.php
        ├── schedule.php
        ├── venue.php
        ├── sponsors.php
        ├── testimonials.php
        └── cta.php
```

---

## 🚀 Quick Start

### 1. Upload Participant Logos
```
assets/img/participants/
├── haas-logo.png
├── dmg-mori-logo.png
├── mazak-logo.png
└── ...
```

**Specs:** 400x200px, PNG transparent, < 100KB

### 2. Use Shortcode
```php
[cnc_participants_slider]
```

### 3. Customize Menu Links
Edit `components/header/header.php` lines 580-650

### 4. Change Slider Speed
Edit `components/participants/participants-slider.php` line 90:
```css
animation: scroll-left 30s linear infinite; /* Change 30s */
```

---

## 🎭 User Interactions

### Desktop
| Action | Result |
|--------|--------|
| Click menu toggle | Side panel slides from right |
| Click overlay | Menu closes |
| Hover logo | Scales 1.05x + shadow glow |
| Hover participant logo | Colorizes + lifts up 5px |
| Scroll down | Header becomes semi-transparent |

### Mobile
| Action | Result |
|--------|--------|
| Tap ☰ | Full-screen menu slides in |
| Tap menu item | Menu closes + navigate |
| Swipe menu | Closes menu |
| Press ESC | Closes menu |

---

## ✨ Design Philosophy

### Convergence India Inspiration
- **Minimalist**: No clutter, focus on content
- **Transparent**: Let hero content shine
- **Mobile-first**: Touch-optimized interactions
- **Fast**: Optimized animations (CSS only)
- **Accessible**: Keyboard navigation, ARIA labels

### Typography Hierarchy
```
H1: 3.5rem (Poppins 700) - Hero titles
H2: 2.5rem (Poppins 700) - Section headers
H3: 1.75rem (Poppins 600) - Sub-headers
Body: 1rem (Inter 400) - Paragraph text
Nav: 0.95rem (Poppins 600) - Menu items
```

---

## 🐛 Common Issues & Fixes

### Issue: Logos not showing
**Solution:**
1. Check path: `assets/img/participants/logo-name.png`
2. Verify file permissions: `chmod 644 logo.png`
3. Clear WordPress cache
4. Check browser console for 404 errors

### Issue: Header not transparent
**Solution:**
1. Ensure hero section has video/image background
2. Check CSS: `.cnc-header { background: transparent; }`
3. Verify no parent theme styles overriding

### Issue: Menu not clickable
**Solution:**
1. Check z-index: `.cnc-header { z-index: 9999; }`
2. Verify JavaScript console for errors
3. Test with: `document.getElementById('cnc-menu-toggle')`

### Issue: Slider not scrolling
**Solution:**
1. Check CSS animation support in browser
2. Verify duplicated items exist
3. Inspect element: `.cnc-participants__slider`

---

## 📊 Performance Metrics

### Header Load Time
- **HTML**: < 1KB
- **CSS**: ~8KB (inline)
- **JS**: ~2KB (inline)
- **Total**: ~11KB

### Logo Slider
- **10 logos**: ~200KB total (optimized PNGs)
- **Animation**: Pure CSS (GPU accelerated)
- **Performance**: 60fps smooth scrolling

### Lighthouse Scores (Target)
- ✅ Performance: 95+
- ✅ Accessibility: 100
- ✅ Best Practices: 100
- ✅ SEO: 100

---

## 🎓 Learning Resources

### CSS Animations
- [MDN Animation Guide](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Animations)
- [Easing Functions](https://easings.net/)

### WordPress Development
- [Theme Handbook](https://developer.wordpress.org/themes/)
- [Shortcode API](https://developer.wordpress.org/plugins/shortcodes/)

### Design Inspiration
- [Convergence India](https://www.convergenceindia.org/)
- [Awwwards](https://www.awwwards.com/)

---

## 📞 Support

**Need help?**
- 📧 Email: info@cncexpo.com
- 📱 Phone: +91 9505 050 007
- 🌐 Website: cncexpo.com
- 💬 WhatsApp: Available during office hours

---

**Version**: 5.0.0  
**Last Updated**: 2024-01-20  
**Status**: ✅ Production Ready

---

## 🎉 What's Next?

### Phase 2 Features (Planned)
- [ ] Dark mode toggle
- [ ] Multi-language support
- [ ] Search in menu
- [ ] Social sharing buttons
- [ ] Live chat integration
- [ ] Analytics tracking
- [ ] A/B testing variants

---

**Built with ❤️ for CNC Expo Hyderabad 2026**
