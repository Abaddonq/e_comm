# Professional Product Page Frontend Development Prompt

## Overview
Create a modern, elegant product page for a furniture e-commerce store inspired by the Guma Store design aesthetic. The page should be minimalist, sophisticated, and optimized for high-end furniture products.

---

## Reference Site Analysis: gumastore.com/roma-elysia-modern-berjer

### Key Design Elements Identified

**1. Layout Structure**
- Full-width header with centered logo
- Split navigation (left and right of logo)
- Product detail section with image gallery (left) and product info (right)
- Clean white background with minimal distractions
- Subtle shadows for depth

**2. Typography**
- Sans-serif fonts (Avenir Next style)
- Uppercase navigation links (small, 12px)
- Product title in medium weight
- Price prominently displayed

**3. Color Palette**
- Primary: Pure white (#FFFFFF) background
- Secondary: Deep black (#212121) for text and accents
- Minimal use of color - let the product images shine

**4. Product Gallery**
- Vertical thumbnail strip on the left
- Large main image area
- Zoom-on-hover functionality
- Responsive image loading with srcset
- WebP format optimization

---

## Issues Identified & Optimizations Required

### Issue 1: Performance Concerns
**Problem:** Heavy third-party script loading (Google Tag Manager, Facebook Pixel, Klaviyo, multiple analytics)
**Optimization:** Implement lazy loading for non-critical scripts; use Partytown or similar to run scripts in web workers

### Issue 2: Header Complexity
**Problem:** Complex absolute positioning with before pseudo-elements
**Optimization:** Simplify with CSS Grid for header layout; use sticky positioning instead of absolute

### Issue 3: Image Loading Strategy
**Problem:** All image variants loaded in srcset regardless of viewport
**Optimization:** Implement proper responsive images with art direction; use Intersection Observer for lazy loading below-fold images

### Issue 4: Mobile Navigation
**Problem:** Hidden mobile menu implementation unclear
**Optimization:** Implement smooth slide-in drawer with proper focus management and ARIA attributes

---

## Technical Requirements


### Performance Standards
- Lighthouse score: 90+ for all metrics
- First Contentful Paint: < 1.5s
- Largest Contentful Paint: < 2.5s
- Cumulative Layout Shift: < 0.1

---

## Component Architecture

```
src/
├── app/
│   ├── layout.tsx
│   ├── page.tsx
│   └── globals.css
├── components/
│   ├── layout/
│   │   ├── Header.tsx
│   │   ├── Navigation.tsx
│   │   ├── MobileMenu.tsx
│   │   └── Footer.tsx
│   ├── product/
│   │   ├── ProductGallery.tsx
│   │   ├── ProductInfo.tsx
│   │   ├── ProductOptions.tsx
│   │   ├── AddToCart.tsx
│   │   └── ProductSpecs.tsx
│   └── ui/
│       ├── button.tsx
│       ├── badge.tsx
│       └── separator.tsx
└── lib/
    └── utils.ts
```

---

## Detailed Component Specifications

### 1. Header Component

**Structure:**
```tsx
- <header> (sticky, white background, subtle shadow on scroll)
  - Logo (centered)
  - Left Navigation (hidden on mobile)
  - Right Navigation (hidden on mobile)
  - Icon Actions (Search, Account, Cart)
  - Mobile Menu Toggle
```

**Behavior:**
- Sticky header that gains shadow on scroll
- Mobile menu slides in from left
- Cart icon shows item count badge
- Search opens modal overlay

### 2. Product Gallery Component

**Structure:**
```tsx
- <div> (flex container)
  - Thumbnail Strip (vertical, scrollable)
    - Thumbnail items (click to change main image)
  - Main Image Container
    - Image (responsive, zoom on hover)
    - Navigation arrows (optional)
```

**Features:**
- Smooth transition between images
- Zoom lens effect on hover
- Keyboard navigation support
- Touch/swipe support for mobile
- Lazy loading for thumbnails

### 3. Product Info Component

**Structure:**
```tsx
- <div> (product details)
  - Breadcrumb
  - Product Title (h1)
  - Price (with currency formatting)
  - Short Description
  - Color/Variant Selector
  - Quantity Selector
  - Add to Cart Button
  - Product Tabs (Description, Specs, Shipping)
```

**Features:**
- Animated quantity selector
- Color swatches with visual feedback
- Sticky "Add to Cart" on mobile
- Accordion for product tabs
- Schema.org structured data

---

## Styling Guidelines

### Spacing System
- Use Tailwind's default spacing scale
- Section padding: py-12 md:py-16 lg:py-20
- Component gaps: gap-4 md:gap-6 lg:gap-8

### Typography Scale
- H1 Product Title: text-2xl md:text-3xl lg:text-4xl font-medium
- Price: text-xl md:text-2xl font-semibold
- Body: text-base leading-relaxed
- Navigation: text-xs uppercase tracking-wider

### Color Tokens
```css
--background: 0 0% 100%;
--foreground: 0 0% 13%;           /* #212121 */
--muted: 0 0% 96%;                /* #f5f5f5 */
--muted-foreground: 0 0% 46%;     /* #767676 */
--border: 0 0% 90%;               /* #e5e5e5 */
--accent: 0 0% 13%;               /* #212121 */
--ring: 0 0% 13%;
```

### Animation Standards
- Duration: 300ms for UI interactions
- Easing: ease-out for entrances, ease-in for exits
- Hover states: scale(1.02) for cards, opacity changes for buttons

---

## Accessibility Requirements

### WCAG 2.1 AA Compliance
- All images must have descriptive alt text
- Color contrast ratio: minimum 4.5:1
- Focus indicators visible on all interactive elements
- Keyboard navigation for gallery
- Screen reader announcements for cart updates

### ARIA Labels
- Product gallery navigation
- Quantity increase/decrease buttons
- Add to cart status
- Color/variant selection

---

## Responsive Breakpoints

```css
/* Mobile First */
sm: 640px   /* Small tablets */
md: 768px   /* Tablets */
lg: 1024px  /* Small laptops */
xl: 1280px  /* Desktops */
2xl: 1536px /* Large screens */
```

### Layout Behavior

**Mobile (< 768px):**
- Single column layout
- Image gallery with swipe
- Sticky add-to-cart button
- Collapsible product details
- Hamburger menu

**Tablet (768px - 1024px):**
- Two column layout (60/40 split)
- Thumbnail strip horizontal
- Full navigation visible

**Desktop (> 1024px):**
- Two column layout (55/45 split)
- Vertical thumbnail strip
- Hover effects enabled
- Full navigation with mega-menu potential

---

## Data Structure

### Product Interface
```typescript
interface Product {
  id: string;
  name: string;
  slug: string;
  price: number;
  currency: string;
  description: string;
  shortDescription: string;
  images: ProductImage[];
  variants?: ProductVariant[];
  specifications: ProductSpec[];
  category: Category;
  brand?: string;
  sku: string;
  inStock: boolean;
  stockQuantity?: number;
}

interface ProductImage {
  id: string;
  url: string;
  alt: string;
  width: number;
  height: number;
  srcset?: string;
}

interface ProductVariant {
  id: string;
  name: string;
  type: 'color' | 'size' | 'material';
  value: string;
  colorCode?: string;
  priceModifier?: number;
  inStock: boolean;
}

interface ProductSpec {
  label: string;
  value: string;
}
```

---

## Sample Implementation Prompt

```


1. A sticky header with:
   - Centered logo (use placeholder SVG)
   - Left navigation: Categories (BERJER, SEHPA, PUF, KIRLENT)
   - Right navigation: Links (VIP, ABOUT, CONTACT)
   - Icon buttons: Search, Account, Cart (with badge)

2. Main product section with two-column layout:
   - Left: Image gallery with vertical thumbnail strip and large main image with zoom effect
   - Right: Product details including:
     - Breadcrumb navigation
     - Product title "Roma Elysia Modern Berjer"
     - Price "53.500 ₺"
     - Short description
     - Color variant selector (show 3 color swatches)
     - Quantity selector with +/- buttons
     - "SEPETE EKLE" (Add to Cart) button - full width, black background
     - Collapsible tabs for Description, Specifications, Shipping

3. Footer with newsletter signup and links

Design requirements:
- Minimalist black and white aesthetic
- Smooth animations using Framer Motion
- Mobile-responsive with hamburger menu
- Accessibility features (ARIA labels, keyboard navigation)
- Use Tailwind CSS for styling
- Include hover effects and micro-interactions

The component should be production-ready with proper TypeScript types.
```

---

## Additional Features to Consider

### Enhanced UX Features
1. **Quick View Modal** - Preview products without leaving page
2. **Sticky Product Bar** - Shows on scroll with key info and CTA
3. **Image Zoom Modal** - Full-screen image viewing
4. **Recently Viewed** - Track and display browsing history
5. **Social Sharing** - Share product on social platforms

### Conversion Optimization
1. **Urgency Indicators** - "Only 2 left in stock"
2. **Trust Badges** - Secure payment, free shipping icons
3. **Reviews Section** - Star ratings and customer feedback
4. **Related Products** - Cross-sell recommendations
5. **Size Guide** - Product dimension visualization

---

## File Deliverables

When implementing, provide:
1. `src/app/page.tsx` - Main product page
2. `src/components/layout/Header.tsx` - Header component
3. `src/components/product/ProductGallery.tsx` - Gallery component
4. `src/components/product/ProductInfo.tsx` - Product details
5. `src/lib/types.ts` - TypeScript interfaces
6. `src/app/globals.css` - Additional styles

---

## Quality Checklist

Before finalizing, verify:
- [ ] All components render without errors
- [ ] Responsive design works on all breakpoints
- [ ] Images load correctly with proper srcset
- [ ] Cart functionality works (add, update quantity)
- [ ] Keyboard navigation is complete
- [ ] Screen reader testing passed
- [ ] Performance metrics meet targets
- [ ] No console errors or warnings
- [ ] Code is properly typed with TypeScript
- [ ] Animations are smooth (60fps)

---

This prompt provides everything needed to create a professional, production-ready product page that matches or exceeds the quality of the reference site while implementing important optimizations for performance, accessibility, and user experience.
