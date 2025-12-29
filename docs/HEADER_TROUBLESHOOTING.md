# 🔧 Header Troubleshooting Guide

## ✅ What Was Fixed

### Issue #1: Duplicate CSS Rules
**Problem**: Two conflicting `.cnc-header.scrolled` rules
- First rule: Dark transparent background (correct)
- Second rule: White background (wrong - overwrote the first)

**Solution**: Removed duplicate, kept dark transparent version

### Issue #2: Header Hiding Content
**Problem**: Fixed position header covers page content
**Solution**: Added `padding-top: 80px` to body (60px on mobile)

### Issue #3: Poor Visibility
**Problem**: Logo and text hard to see on transparent background
**Solution**: 
- Changed nav links to white color with text shadow
- Changed mobile toggle bars to white with shadow
- Improved logo drop-shadow for better contrast

---

## 🧪 Testing Instructions

### 1. Clear Browser Cache
```
Ctrl + Shift + Delete (Chrome/Edge)
Ctrl + Shift + R (Hard refresh)
```

### 2. Check Header Visibility
**What to look for:**
- ✅ Logo visible in top-left corner
- ✅ Hamburger menu (☰) visible in top-right
- ✅ Header is transparent (you can see content behind it)
- ✅ White text on logo and menu button
- ✅ Content is not hidden under header

### 3. Scroll Behavior Test
**Scroll down the page:**
- ✅ Header should become semi-transparent dark background
- ✅ Magenta border should appear at bottom
- ✅ Smooth transition animation

### 4. Menu Toggle Test
**Click the hamburger icon:**
- ✅ Icon transforms to X
- ✅ Side panel slides in from right
- ✅ Dark overlay appears over content
- ✅ Menu has organized sections (Exhibition, Visitor, About)
- ✅ Clicking overlay or X closes menu

### 5. Mobile Test
**Resize browser to < 768px:**
- ✅ Logo shrinks to 40px height
- ✅ Header becomes more compact
- ✅ Menu opens full-screen

---

## 🐛 Still Having Issues?

### Header Not Showing
**Check:**
1. Clear WordPress cache (if using caching plugin)
2. Open browser DevTools (F12) → Console tab
3. Look for error: `🎯 CNC Header: JavaScript initialized`

**If you see this error:**
```
Uncaught SyntaxError: Identifier 'lazyloadRunObserver' has already been declared
```
This is from another part of the page, not the header. The header should still work.

### Header Not Transparent
**Check:**
1. Is there a background image/video in hero section?
2. DevTools → Inspect header element
3. Verify CSS: `background: transparent`

**Fix**: The header IS transparent now, but you need content behind it to see through.

### Menu Not Opening
**Check:**
1. DevTools → Console for JavaScript errors
2. Click the hamburger icon (☰)
3. Verify element exists: `document.getElementById('cnc-mobile-toggle')`

**Fix**: JavaScript is loaded inline in header.php, should work immediately.

### Logo Not Showing
**Current logo paths being checked:**
1. `/assets/cnc-logov3.png`
2. `/assets/img/cnc-logo.png`
3. `/assets/img/Shape Logo V1100x.png`

**If logo missing**: Fallback text "CNC EXPO" will show in white.

---

## 📊 Current Header Specs

### Desktop
- **Height**: 80px (with 1rem padding)
- **Position**: Fixed top
- **Background**: Transparent → Dark on scroll
- **Logo**: 60px height
- **Menu**: Side drawer (400px width)

### Mobile (<768px)
- **Height**: 60px (with 0.5rem padding)
- **Body padding**: 60px top
- **Logo**: 40px height
- **Menu**: Full-screen drawer

---

## 🎨 Current Styling

### Colors
```css
Text: #FFFFFF (white)
Background (scrolled): rgba(13, 13, 13, 0.95)
Accent: #C6308C (magenta)
Shadow: rgba(0, 0, 0, 0.3)
```

### Animations
- **Header entrance**: fadeInDown 0.6s
- **Scroll effect**: Background + blur + shadow
- **Menu slide**: 0.4s cubic-bezier
- **Hover effects**: scale(1.05) on logo

---

## 🔍 Debug Checklist

- [ ] Browser cache cleared
- [ ] Page hard-refreshed (Ctrl+Shift+R)
- [ ] Console shows "🎯 CNC Header: JavaScript initialized"
- [ ] Header element exists in DOM (Inspect element)
- [ ] CSS applied to `.cnc-header` element
- [ ] Body has `padding-top: 80px`
- [ ] Logo file exists or fallback text showing
- [ ] Menu toggle button clickable
- [ ] No z-index conflicts with other elements

---

## 📞 Quick Fixes

### Force Header to Show
Add this to browser console:
```javascript
document.querySelector('.cnc-header').style.display = 'block';
document.querySelector('.cnc-header').style.visibility = 'visible';
document.querySelector('.cnc-header').style.opacity = '1';
```

### Test Menu Toggle
```javascript
document.getElementById('cnc-mobile-toggle').click();
```

### Check Computed Styles
```javascript
const header = document.querySelector('.cnc-header');
console.log('Position:', window.getComputedStyle(header).position);
console.log('Z-index:', window.getComputedStyle(header).zIndex);
console.log('Background:', window.getComputedStyle(header).background);
```

---

## ✅ Expected Console Messages

When page loads, you should see:
```
🎯 CNC Header: JavaScript initialized
✅ Sticky header initialized
✅ Mobile menu initialized
✅ CNC Header: All features loaded successfully
```

---

## 📝 Files Modified

1. `components/header/header.php` - Complete rewrite
   - Fixed duplicate CSS rules
   - Added body padding
   - Changed text colors to white
   - Improved shadows and visibility

2. Backup created: `components/header/header-old.backup`

---

## 🚀 Next Steps

If header is now visible:
1. **Upload a logo** to `assets/img/cnc-logo.png`
2. **Test all menu links** - update URLs as needed
3. **Add hero background** - video or image behind header
4. **Customize colors** - if needed for brand

If still having issues:
1. **Share screenshot** of what you see
2. **Share console errors** from DevTools
3. **Verify WordPress version** (needs 5.8+)

---

**Last Updated**: Header v5.0 fixes applied  
**Status**: ✅ Ready for testing
