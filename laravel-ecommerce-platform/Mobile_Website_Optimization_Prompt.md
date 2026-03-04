Mobile Website Optimization Prompt
Inspired by GumaStore.com Mobile Experience
Executive Summary
This document provides a comprehensive prompt for optimizing mobile website experiences, drawing inspiration from GumaStore.com, a Turkish e-commerce platform specializing in home decor products. GumaStore exemplifies modern mobile-first design principles through its clean interface, intuitive navigation, and seamless user experience. The following prompt can be used to guide AI systems or development teams in creating similar high-quality mobile experiences.
Section 1: GumaStore.com Mobile Version Analysis
1.1 Business Overview
GumaStore (gumastore.com) is a Turkish online retailer specializing in stylish cushions (kırlent) and armchairs (berjer). The platform offers a curated selection of home decor products designed to add aesthetic value to living spaces. Their product categories include berjer (armchairs), sehpa (coffee tables), puf (poufs), kırlent (cushions), aksesuar (accessories), tablo (wall art), and sandalye (chairs). The brand positions itself as a provider of affordable yet stylish decorative products that reflect the customer's personal taste.
1.2 Technical Architecture
The website is built on a modern technology stack that prioritizes performance and user experience:
•	Framework: Next.js with React for server-side rendering and optimal SEO
•	CDN: Content delivered via myikas.com CDN for fast global access
•	Image Format: WebP format with responsive srcset for optimal image delivery
•	Carousel: Swiper.js for touch-friendly image sliders
•	Analytics: Google Tag Manager, Facebook Pixel, and Klaviyo integration
1.3 Mobile Navigation Design
GumaStore implements a sophisticated navigation system optimized for mobile users:
1.	Hamburger Menu: A collapsible menu icon appears on mobile (max-xl breakpoint), replacing the full desktop navigation bar. This ensures maximum screen real estate for product display while maintaining easy access to all categories.
2.	Sticky Header: The navigation remains fixed at the top during scrolling, providing constant access to search, cart, and menu functions. The header height is optimized at 85px for comfortable thumb reach.
3.	Icon-Based Actions: Search, user account, and cart are represented by intuitive icons sized at 26x27 pixels, optimized for touch interaction.
4.	Centered Logo: The brand logo occupies the center position, creating a balanced layout and reinforcing brand identity.
1.4 Hero Section Design
The hero section showcases the brand's premium positioning through immersive design elements:
•	Full-Screen Video Hero: Auto-playing, muted, looping video background creates an immersive first impression
•	Minimal Overlay: A single "KESŐFETMEYE BAŞLA" (Start Exploring) button with transparent background allows the visual content to shine
•	Vertical Height: 100vh height ensures the hero section occupies the entire mobile viewport, creating a focused user journey
 
Section 2: The Optimization Prompt
The following comprehensive prompt can be used with AI systems or as a guideline for development teams to create mobile-optimized e-commerce websites:
PROMPT:
Create a mobile-optimized e-commerce website with the following specifications:
TECHNICAL REQUIREMENTS:
1.	Implement responsive viewport meta tag with user-scalable=no and maximum-scale=1 to prevent accidental zoom and maintain consistent layout across devices
2.	Use Next.js or React framework with server-side rendering for optimal SEO and initial load performance
3.	Implement CSS Grid with responsive breakpoints using CSS custom properties (--webCols) for flexible column layouts
4.	Configure CDN delivery with preconnect and dns-prefetch hints for faster resource loading
5.	Serve images in WebP format with responsive srcset attributes for optimal quality-to-size ratio
NAVIGATION DESIGN:
1.	Design a sticky header that remains fixed during scrolling with optimized 85px height for mobile comfort
2.	Implement hamburger menu for mobile views (hidden on desktop) using max-xl breakpoint toggle
3.	Position logo centrally in the header with aspect-ratio maintained for brand consistency
4.	Use 26x27 pixel touch-optimized icons for search, account, and cart functions
5.	Apply smooth header transition effects on hover with color inversion for visual feedback
HERO SECTION:
1.	Create full-screen (100vh) hero section with autoplay, muted, looping video background
2.	Position single prominent CTA button centered with transparent background and white text
3.	Implement Swiper carousel for multiple hero slides with pagination bullets
4.	Ensure video/image content covers entire viewport with object-fit: cover for consistent appearance
PRODUCT DISPLAY:
1.	Display products in responsive grid with 2 columns on mobile, scaling to 3-4 columns on tablet and desktop
2.	Implement lazy loading for images with blur placeholder for improved perceived performance
3.	Add hover effects with scale transform and overlay for product interaction feedback
4.	Include quick-view functionality and add-to-cart buttons accessible within thumb zone
5.	Display price prominently with original and discounted price comparison where applicable
PERFORMANCE & UX:
1.	Implement smooth scrolling with optimized scroll-snap for section-by-section navigation
2.	Add loading skeletons for content areas to maintain visual stability during data fetching
3.	Configure font preloading for critical typography to prevent FOUT (Flash of Unstyled Text)
4.	Implement infinite scroll or load-more buttons for product listings with intersection observer
5.	Optimize touch targets to minimum 44x44 pixels for comfortable mobile interaction
 
Section 3: Key Design Principles Derived from GumaStore
3.1 Visual Hierarchy
GumaStore demonstrates exemplary visual hierarchy that guides users through the shopping journey. The design employs a clear top-to-bottom information flow where the hero section captures immediate attention, followed by category navigation, featured products, and supporting content. Typography uses a sophisticated combination of font weights (400-700) and sizes (12px-56px) to create distinct levels of importance. White space is used generously, allowing products to breathe and reducing cognitive load for users. The color scheme maintains consistency with subtle neutral tones accented by the brand's identity, creating a cohesive visual language throughout the mobile experience.
3.2 Touch-Friendly Interactions
The mobile interface prioritizes thumb-friendly design with all interactive elements positioned within natural thumb reach zones. Key principles observed include:
•	Touch Target Sizing: Minimum 44x44 pixel targets for all interactive elements, with icon buttons sized at 26x27 pixels plus adequate padding
•	Gesture Support: Swiper integration enables intuitive swipe gestures for product carousels and image galleries
•	Bottom Navigation: Critical actions (cart, search) remain accessible at screen edges for one-handed operation
•	Feedback Animations: Hover states and transition effects provide immediate visual confirmation of interactions
3.3 Content Strategy
The content presentation strategy focuses on visual storytelling with minimal text. Product categories are represented through high-quality imagery rather than lengthy descriptions. The homepage features a full-screen video hero that immediately communicates brand identity and product quality without requiring users to read. Product listings display essential information (image, name, price) in a clean grid layout, with detailed information accessible on demand through product detail pages. This approach respects mobile users' limited attention span and preference for visual content consumption.
Section 4: Feature Comparison and Implementation Guide
Feature	GumaStore Implementation	Best Practice
Navigation	Hamburger menu for mobile	Collapsible menu + bottom nav bar
Hero Section	Full-screen video background	Video with fallback image
Image Optimization	WebP with responsive srcset	AVIF/WebP + lazy loading
Header	Sticky 85px header	60-80px optimal for mobile
Touch Targets	26x27px icons	Minimum 44x44px recommended
Carousel	Swiper.js integration	Touch-optimized slider + dots
Table 1: GumaStore Mobile Features Comparison
 
Section 5: Implementation Checklist
The following checklist provides a structured approach to implementing mobile-optimized e-commerce websites based on GumaStore's design principles. Each item represents a critical component that contributes to the overall mobile user experience and should be verified during development and quality assurance phases.
5.1 Pre-Development Phase
Before beginning development, ensure the following foundational elements are in place:
•	Define responsive breakpoints (mobile: 320-768px, tablet: 768-1024px, desktop: 1024px+)
•	Establish design tokens for colors, typography, and spacing
•	Create mobile-first CSS architecture
•	Plan image asset strategy with multiple sizes and formats
5.2 Development Phase
•	Implement viewport meta tag with proper configuration
•	Configure CDN and resource hints (preconnect, prefetch)
•	Build responsive navigation with hamburger menu
•	Implement hero section with video/image support
•	Create product grid with lazy-loaded images
•	Add touch gestures and swipe interactions
•	Implement cart and checkout flow optimized for mobile
5.3 Quality Assurance Phase
•	Test on multiple device sizes and orientations
•	Verify touch target sizes and accessibility
•	Measure Core Web Vitals (LCP, FID, CLS)
•	Validate image optimization and loading performance
•	Test checkout flow on real mobile devices
Conclusion
GumaStore.com exemplifies modern mobile e-commerce design through its clean aesthetics, performance-focused implementation, and user-centered approach. By following the prompt and guidelines outlined in this document, development teams can create similarly effective mobile experiences that prioritize user needs while maintaining brand identity and business objectives. The key takeaway is that successful mobile optimization requires a holistic approach encompassing technical performance, visual design, interaction patterns, and content strategy - all working together to create seamless shopping experiences on mobile devices.
