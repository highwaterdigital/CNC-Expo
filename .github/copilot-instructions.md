# CNC Expo WordPress Child Theme - AI Agent Guide

## Architecture Overview

**Event Site**: Cable & Internet Expo Hyderabad 2025  
**Architecture**: Component-driven WordPress child theme, no page builders  
**Critical Pattern**: Inline CSS strategy for maximum portability

### Template Loading Flow

```
functions.php → cnc_override_template() → cnc-complete-page.php
    ↓
header.php + [cnc_home_page] shortcode + footer.php
    ↓
pages/home/home-shortcode.php (inline CSS + markup)
```

**Key**: `template_redirect` hook (priority 1) completely overrides WordPress template system for pages 6, 214, and front page.

## Critical Conventions

### 1. Inline CSS Requirement
**ALL shortcode files MUST include complete CSS in `<style>` tags**. This is non-negotiable for portability.

```php
// ✅ Correct - pages/home/home-shortcode.php
<?php
ob_start();
?>
<style>
.cnc-home { /* Complete styles here */ }
@media (max-width: 768px) { /* Mobile styles */ }
</style>
<div class="cnc-home">Content</div>
<?php
return ob_get_clean();
```

### 2. Naming Conventions
- **CSS classes**: `cnc-{component}-{element}` (e.g., `cnc-hero-title`)
- **PHP functions**: `cnc_{function_name}()` (e.g., `cnc_home_page_shortcode()`)
- **Shortcodes**: `[cnc_{name}]` (e.g., `[cnc_home_page]`)
- **Files**: kebab-case (e.g., `home-shortcode.php`)

### 3. Design System Variables
**Always use these CSS variables from the logo-based color palette:**

```css
:root {
    --cnc-magenta: #C6308C;     /* PRIMARY brand color */
    --cnc-purple: #5E3A8E;      /* Secondary */
    --cnc-blue: #0090D9;        /* CTAs */
    --cnc-teal: #00B4B4;        /* Accents */
    --cnc-black: #0D0D0D;       /* Text */
    --cnc-gold: #FFC107;        /* Highlights */
    --gradient-primary: linear-gradient(135deg, #5E3A8E 0%, #C6308C 100%);
    --cnc-radius: 20px;
    --cnc-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    --cnc-transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
```

Typography: `'Poppins'` (headings, 600-700 weight), `'Inter'` (body, 400-500 weight)

## File Structure Patterns

```
components/{name}/
├── {name}.php          # Markup + inline CSS (for shortcodes)
└── {name}.css          # Reference only (not loaded)

pages/{name}/
├── template-{name}.php    # Full page template
├── {name}-shortcode.php   # Shortcode version with inline CSS
└── {name}.css             # Reference styles

assets/
├── js/{feature}.js        # Vanilla JS, DOMContentLoaded wrapper
└── img/                   # Images
```

## Component Development Workflow

### Creating a New Component
1. Create folder: `components/{name}/`
2. Create `{name}.php` with inline CSS
3. Use `cnc-{name}` as root class
4. Include mobile breakpoints in inline CSS
5. Reference from shortcode via `include` or `get_template_part()`

### Creating a New Shortcode
1. Add to `functions.php` (main registrations here, not `inc/shortcodes.php` which is empty)
2. Use `ob_start()/ob_get_clean()` pattern
3. Sanitize attributes with `shortcode_atts()` and `sanitize_text_field()`
4. Include complete inline `<style>` block
5. Add recursion protection for complex shortcodes

**Example from codebase:**
```php
function cnc_home_page_shortcode() {
    static $executing = false;
    if ($executing) return '<!-- Recursion prevented -->';
    $executing = true;
    
    ob_start();
    include get_stylesheet_directory() . '/pages/home/home-shortcode.php';
    $content = ob_get_clean();
    
    $executing = false;
    return $content;
}
add_shortcode('cnc_home_page', 'cnc_home_page_shortcode');
```

## JavaScript Conventions

**Vanilla JS only** - jQuery explicitly avoided. Pattern from `assets/js/main.js`:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    const component = document.querySelector('.cnc-component');
    if (!component) return; // Exit early if not found
    
    // Feature implementation
    function init() {
        bindEvents();
    }
    
    function bindEvents() {
        // Event listeners
    }
    
    init();
});
```

## Debugging Patterns

The codebase uses extensive `error_log()` with emoji prefixes for clarity:
- `🚀` - Initialization
- `✅` - Success
- `❌` - Error
- `📄` - Content processing
- `🎯` - Template loading

**Example**: `error_log('✅ CNC v5.0: Loading shortcode from: ' . $shortcode_file);`

## Parent Theme Override Strategy

`functions.php` uses aggressive CSS to hide parent theme elements:
```php
function cnc_hide_default_elements() {
    // Hides .site-header, .site-footer, .entry-header for specific pages
    // Forces .cnc-* elements to display: block !important
}
add_action('wp_head', 'cnc_hide_default_elements', 1);
```

## Integration Points (Planned)

The `integrations/` folder contains placeholder classes for future implementation:
- **Analytics**: Google Tag Manager
- **CRM**: WhatsApp API  
- **Data**: Google Sheets sync

These are architectural placeholders - implement as needed for the project.

## Common Pitfalls

1. **Don't forget inline CSS** - Shortcodes without inline styles won't work
2. **Container class mismatch** - Use `.cnc-container` consistently (max-width: 1200px, margin: 0 auto)
3. **All logic in functions.php** - Core functionality lives in `functions.php`, not distributed across `inc/` files
4. **Full-width breakout** - Use `left: 50%; margin-left: -50vw` pattern for full-width sections
5. **Recursion protection** - Always add static flag to prevent infinite shortcode loops

## Version History Pattern

Current version: v4.3.0 (from `CNC_VERSION` constant in `functions.php`)

The codebase has been cleaned for a fresh start - all backup files removed.

## Testing Approach

1. Check specific page IDs (6, 214) for template override
2. Verify shortcode execution via debug logs
3. Inspect inline `<style>` tags in DOM
4. Mobile-first responsive testing (breakpoint: 768px)
5. Ensure CSS variables are used (not hardcoded colors)

## Related Documentation

- `.copilot-instructions.md` - Comprehensive 426-line guide (older, more detailed)
- `docs/CNC_Design_System_Prompt.md` - Design philosophy
- `docs/COLOR_PALETTE.md` - Complete color system with RGB values
