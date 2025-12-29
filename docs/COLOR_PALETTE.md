# CNC Expo Child Theme - Modern Color Palette v3.1

## 🎨 Logo-Based Design System (January 2025)

This document describes the **modern color palette** based on the **CNC Expo logo** - featuring vibrant magenta, deep purple, and electric blue for a contemporary technology expo aesthetic.

### 🎯 Signature Colors

```
🟣 Deep Purple     #5E3A8E  (Secondary Background)
💗 Magenta         #C6308C  (Primary Brand - THE HERO COLOR!)
🔵 Electric Blue   #0090D9  (Accent CTA)
🌊 Modern Teal     #00B4B4  (Support Color)
⚪ Pure White      #FFFFFF  (Text on Dark)
⚫ Near Black      #0D0D0D  (Text & Headers)
🟡 Bright Gold     #FFC107  (Premium Elements)
```

**Design Philosophy**: Modern, vibrant, high-energy expo theme with the magenta as the hero color.

---

## Primary Brand Color

### Magenta (Primary Brand)
- **Variable**: `--shape-primary`
- **Hex**: `#C6308C`
- **RGB**: `rgb(198, 48, 140)`
- **Use**: **PRIMARY BRAND COLOR**, logo, main CTAs, headings
- **Psychology**: Bold, modern, tech-forward, energetic
- **Note**: This is THE signature color from the logo!

### Darker Magenta
- **Variable**: `--shape-primary-dark`
- **Hex**: `#A02773`
- **RGB**: `rgb(160, 39, 115)`
- **Use**: Hover states, darker sections

### Lighter Magenta
- **Variable**: `--shape-primary-light`
- **Hex**: `#E94FAF`
- **RGB**: `rgb(233, 79, 175)`
- **Use**: Highlights, lighter accents

---

## Secondary Colors

### Deep Purple (Secondary)
- **Variable**: `--shape-secondary`
- **Hex**: `#5E3A8E`
- **RGB**: `rgb(94, 58, 142)`
- **Use**: Secondary backgrounds, cards, depth
- **Psychology**: Premium, sophisticated, tech

### Electric Blue (Accent)
- **Variable**: `--shape-accent`
- **Hex**: `#0090D9`
- **RGB**: `rgb(0, 144, 217)`
- **Use**: Secondary CTAs, links, accents
- **Psychology**: Trust, technology, innovation

### Modern Teal (Support)
- **Variable**: `--shape-teal`
- **Hex**: `#00B4B4`
- **RGB**: `rgb(0, 180, 180)`
- **Use**: Tertiary accents, info elements
- **Psychology**: Fresh, modern, balanced

---

## Neutral Colors

### Near Black
- **Variable**: `--shape-black`
- **Hex**: `#0D0D0D`
- **RGB**: `rgb(13, 13, 13)`
- **Use**: Text, dark backgrounds

### Pure White
- **Variable**: `--shape-white`
- **Hex**: `#FFFFFF`
- **RGB**: `rgb(255, 255, 255)`
- **Use**: Text on dark, card backgrounds

### Light Gray
- **Variable**: `--shape-gray`
- **Hex**: `#F8F9FA`
- **RGB**: `rgb(248, 249, 250)`
- **Use**: Light section backgrounds

### Dark Gray
- **Variable**: `--shape-dark`
- **Hex**: `#212529`
- **RGB**: `rgb(33, 37, 41)`
- **Use**: Footer, dark sections

---

## Gradients

### Primary Gradient (Purple to Magenta)
```css
background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
```
**Use**: Hero sections, primary buttons

### Secondary Gradient (Blue to Teal)
```css
background: linear-gradient(135deg, #0090D9 0%, #00B4B4 100%);
```
**Use**: Secondary buttons, alternative sections

### Accent Gradient (Magenta Shades)
```css
background: linear-gradient(135deg, #C6308C 0%, #E94FAF 100%);
```
**Use**: Highlights, special elements

---

## Button Styles

### Primary Button - Magenta/Purple Gradient
```css
.btn-primary {
    background: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    color: #FFFFFF;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(198, 48, 140, 0.3);
}
```

### Secondary Button - Blue/Teal Gradient
```css
.btn-secondary {
    background: linear-gradient(135deg, #0090D9 0%, #00B4B4 100%);
    color: #FFFFFF;
    box-shadow: 0 4px 14px rgba(0, 144, 217, 0.3);
}
```

### Outline Button
```css
.btn-outline {
    background: transparent;
    color: #C6308C;
    border: 2px solid #C6308C;
}
```

---

## Usage Guidelines

### Typography Colors
- **Headings on Light**: `#C6308C` (Magenta) or `#5E3A8E` (Purple)
- **Body Text on Light**: `#0D0D0D` (Black)
- **Headings on Dark**: `#FFFFFF` (White)
- **Body Text on Dark**: `rgba(255, 255, 255, 0.85)`
- **Links**: `#0090D9` (Blue) with `#C6308C` (Magenta) on hover

### Background Combinations
- **Hero Sections**: Purple-to-Magenta gradient
- **Alternate Sections**: Light gray (`#F8F9FA`)
- **Dark Sections**: `#212529` with magenta accents
- **Cards on Light**: White with shadow
- **Cards on Dark**: `rgba(255, 255, 255, 0.05)` with border

---

## CSS Variable Reference

```css
:root {
    /* Primary Brand */
    --shape-primary: #C6308C;
    --shape-primary-dark: #A02773;
    --shape-primary-light: #E94FAF;
    
    /* Secondary */
    --shape-secondary: #5E3A8E;
    --shape-accent: #0090D9;
    --shape-teal: #00B4B4;
    
    /* Neutrals */
    --shape-black: #0D0D0D;
    --shape-white: #FFFFFF;
    --shape-gray: #F8F9FA;
    --shape-dark: #212529;
    
    /* Accents */
    --shape-gold: #FFC107;
    
    /* Gradients */
    --gradient-primary: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    --gradient-secondary: linear-gradient(135deg, #0090D9 0%, #00B4B4 100%);
}
```

---

## Migration Notes

### Changes from Previous Version
- **Primary Color**: Changed from red (#E11B22) to magenta (#C6308C)
- **Accent Color**: Changed from lime green to electric blue (#0090D9)
- **Secondary**: Added deep purple (#5E3A8E) as core color
- **Added**: Modern teal (#00B4B4) as support color

### Legacy Variable Mapping
```css
--shape-red: var(--shape-primary);    /* Now magenta */
--cnc-blue: var(--shape-accent);      /* Now electric blue */
--cnc-magenta: var(--shape-primary);  /* Primary brand */
```

---

*Color palette updated: January 11, 2025*
*Design System: CNC Expo Logo-Based*
*Theme Version: 3.1.0*