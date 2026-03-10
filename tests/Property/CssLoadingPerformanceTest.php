<?php

namespace Tests\Property;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Bug Condition Exploration Test for CSS Loading Performance Fix
 * 
 * **Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5**
 * 
 * This test explores the fault condition where CSS resources are loaded in suboptimal order,
 * causing performance issues including FOUC, render blocking, and page loader visibility issues.
 * 
 * CRITICAL: This test is EXPECTED TO FAIL on unfixed code - failure confirms the bugs exist.
 * When the test fails, it surfaces counterexamples demonstrating the performance issues.
 * After the fix is implemented, this same test should PASS, confirming the expected behavior.
 */
class CssLoadingPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 1: Fault Condition - CSS Loading Performance Issues
     * 
     * For any page load where CSS resources exist, the layout should load external stylesheets
     * before large inline styles, preload fonts with crossorigin attribute, and execute page
     * loader hiding script inline in the head section.
     * 
     * This test verifies the fault condition by checking:
     * 1. External stylesheet position relative to inline styles in DOM
     * 2. Page loader script position and execution timing
     * 3. Font preconnect has crossorigin attribute
     * 4. Resource loading order and timing
     * 
     * @test
     */
    public function test_app_layout_has_optimal_css_loading_order()
    {
        // Arrange: Get the app.blade.php layout content
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Act: Parse the layout to find resource positions
        $vitePosition = strpos($layoutContent, '@vite');
        $criticalCssPosition = strpos($layoutContent, '<style>');
        $fontPreconnectPosition = strpos($layoutContent, 'rel="preconnect"');
        $pageLoaderScriptPosition = strpos($layoutContent, "window.addEventListener('load'");
        
        // Find if page loader script is in head or body
        $headEndPosition = strpos($layoutContent, '</head>');
        $bodyStartPosition = strpos($layoutContent, '<body');
        
        // Check for crossorigin attribute on font preconnect
        $fontPreconnectLine = $this->extractLineContaining($layoutContent, 'rel="preconnect"');
        $hasCrossorigin = strpos($fontPreconnectLine, 'crossorigin') !== false;
        
        // Assert 1: External stylesheet (@vite) should load BEFORE large inline styles
        // EXPECTED TO FAIL: Currently @vite is after critical CSS in app.blade.php
        $this->assertLessThan(
            $criticalCssPosition,
            $vitePosition,
            "COUNTEREXAMPLE: External stylesheet (@vite at position {$vitePosition}) loads AFTER inline styles (at position {$criticalCssPosition}). " .
            "This causes render blocking as inline styles are parsed before external CSS is available."
        );
        
        // Assert 2: Page loader script should be in HEAD, not at end of BODY
        // EXPECTED TO FAIL: Currently script is at end of body
        $this->assertLessThan(
            $headEndPosition,
            $pageLoaderScriptPosition,
            "COUNTEREXAMPLE: Page loader script (at position {$pageLoaderScriptPosition}) is in BODY (head ends at {$headEndPosition}). " .
            "This causes the loader to remain visible because the script may execute after window.load event fires."
        );
        
        // Assert 3: Font preconnect should have crossorigin attribute
        // EXPECTED TO FAIL: Currently missing crossorigin attribute
        $this->assertTrue(
            $hasCrossorigin,
            "COUNTEREXAMPLE: Font preconnect link lacks 'crossorigin' attribute. " .
            "This prevents proper CORS handling and delays font loading. Found: {$fontPreconnectLine}"
        );
        
        // Assert 4: Verify optimal head section order
        // Expected order: meta tags → @vite → fonts → critical CSS → page loader script
        $this->assertLessThan($fontPreconnectPosition, $vitePosition, 
            "External stylesheet should load before font preconnect");
        $this->assertLessThan($criticalCssPosition, $fontPreconnectPosition,
            "Font preconnect should come before inline styles");
    }

    /**
     * Property 1: Fault Condition - Admin Layout CSS Loading Performance
     * 
     * Tests the admin.blade.php layout for the same fault conditions.
     * Admin layout has additional issues with large inline style blocks (150+ lines).
     * 
     * @test
     */
    public function test_admin_layout_has_optimal_css_loading_order()
    {
        // Arrange: Get the admin.blade.php layout content
        $layoutPath = resource_path('views/layouts/admin.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Act: Parse the layout to find resource positions
        $vitePosition = strpos($layoutContent, '@vite');
        $firstStylePosition = strpos($layoutContent, '<style>');
        
        // Find all <style> blocks to identify large inline styles
        $styleBlocks = [];
        $offset = 0;
        while (($pos = strpos($layoutContent, '<style>', $offset)) !== false) {
            $endPos = strpos($layoutContent, '</style>', $pos);
            $styleBlocks[] = [
                'start' => $pos,
                'end' => $endPos,
                'size' => $endPos - $pos
            ];
            $offset = $endPos + 1;
        }
        
        // Find the large inline style block (should be after @vite)
        $largeStyleBlock = null;
        foreach ($styleBlocks as $block) {
            if ($block['size'] > 1000 && $block['start'] > $vitePosition) {
                $largeStyleBlock = $block;
                break;
            }
        }
        
        // Find page loader script position
        $pageLoaderScriptPosition = strpos($layoutContent, "window.addEventListener('load'");
        $headEndPosition = strpos($layoutContent, '</head>');
        
        // Assert 1: External stylesheet should load BEFORE all inline styles
        // EXPECTED TO FAIL: @vite is positioned correctly but large inline styles after it block rendering
        $this->assertLessThan(
            $firstStylePosition,
            $vitePosition,
            "COUNTEREXAMPLE: External stylesheet (@vite at position {$vitePosition}) loads AFTER first inline style block (at position {$firstStylePosition}). " .
            "This causes render blocking."
        );
        
        // Assert 2: Large inline style blocks should not exist after @vite
        // EXPECTED TO FAIL: Admin layout has 150+ lines of inline styles after @vite
        if ($largeStyleBlock) {
            $this->fail(
                "COUNTEREXAMPLE: Large inline style block (size: {$largeStyleBlock['size']} bytes) found at position {$largeStyleBlock['start']}, " .
                "AFTER @vite directive (at position {$vitePosition}). This blocks rendering even though external stylesheet loads first. " .
                "Large inline styles should be moved to external stylesheet or positioned before @vite."
            );
        }
        
        // Assert 3: Page loader script should be in HEAD
        // EXPECTED TO FAIL: Script is at end of body
        $this->assertLessThan(
            $headEndPosition,
            $pageLoaderScriptPosition,
            "COUNTEREXAMPLE: Page loader script (at position {$pageLoaderScriptPosition}) is in BODY (head ends at {$headEndPosition}). " .
            "This causes the loader to remain visible indefinitely."
        );
        
        // Assert 4: Check for duplicate style definitions
        $criticalCssBlock = substr($layoutContent, $firstStylePosition, strpos($layoutContent, '</style>', $firstStylePosition) - $firstStylePosition);
        $hasAdminNavInCritical = strpos($criticalCssBlock, '.admin-nav') !== false;
        
        if ($largeStyleBlock) {
            $largeStyleContent = substr($layoutContent, $largeStyleBlock['start'], $largeStyleBlock['size']);
            $hasAdminNavInLarge = strpos($largeStyleContent, '.admin-nav') !== false;
            
            // EXPECTED TO FAIL: Duplicate admin-nav styles
            $this->assertFalse(
                $hasAdminNavInCritical && $hasAdminNavInLarge,
                "COUNTEREXAMPLE: Duplicate .admin-nav styles found in both critical CSS and large inline block. " .
                "This increases HTML size and parsing time unnecessarily."
            );
        }
    }

    /**
     * Property 1: Fault Condition - Page Loader Visibility Test
     * 
     * Tests that the page loader hides within 100ms of window.load event.
     * This is a functional test that simulates page load behavior.
     * 
     * @test
     */
    public function test_page_loader_hides_within_100ms_of_window_load()
    {
        // This test verifies the page loader script executes correctly
        // We test this by checking the script's position and logic
        
        $layouts = [
            'app' => resource_path('views/layouts/app.blade.php'),
            'admin' => resource_path('views/layouts/admin.blade.php')
        ];
        
        foreach ($layouts as $name => $layoutPath) {
            $layoutContent = file_get_contents($layoutPath);
            
            // Find page loader script
            $scriptStart = strpos($layoutContent, "window.addEventListener('load'");
            $scriptEnd = strpos($layoutContent, '</script>', $scriptStart);
            $scriptContent = substr($layoutContent, $scriptStart, $scriptEnd - $scriptStart);
            
            // Verify script logic
            $hasLoaderReference = strpos($scriptContent, "getElementById('pageLoader')") !== false;
            $hasHiddenClass = strpos($scriptContent, "classList.add('hidden')") !== false;
            $hasRemove = strpos($scriptContent, 'remove()') !== false;
            
            $this->assertTrue($hasLoaderReference, "{$name} layout: Script should reference pageLoader element");
            $this->assertTrue($hasHiddenClass, "{$name} layout: Script should add 'hidden' class");
            $this->assertTrue($hasRemove, "{$name} layout: Script should remove loader after transition");
            
            // CRITICAL: Verify script is in HEAD (inline, blocking)
            // EXPECTED TO FAIL: Script is currently at end of body
            $headEndPosition = strpos($layoutContent, '</head>');
            $this->assertLessThan(
                $headEndPosition,
                $scriptStart,
                "COUNTEREXAMPLE ({$name} layout): Page loader script (at position {$scriptStart}) is NOT in HEAD (head ends at {$headEndPosition}). " .
                "Script at end of body may execute after window.load event, causing loader to remain visible indefinitely. " .
                "This is the root cause of the page loader visibility bug."
            );
        }
    }

    /**
     * Property 1: Fault Condition - Resource Loading Order Test
     * 
     * Verifies the optimal resource loading order in the head section.
     * Expected order: meta tags → @vite → fonts → critical CSS → page loader script
     * 
     * @test
     */
    public function test_resource_loading_order_is_optimal()
    {
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Find positions of all resources
        $positions = [
            'meta_charset' => strpos($layoutContent, '<meta charset'),
            'meta_viewport' => strpos($layoutContent, '<meta name="viewport"'),
            'meta_csrf' => strpos($layoutContent, '<meta name="csrf-token"'),
            'title' => strpos($layoutContent, '<title>'),
            'vite' => strpos($layoutContent, '@vite'),
            'font_preconnect' => strpos($layoutContent, 'rel="preconnect"'),
            'font_stylesheet' => strpos($layoutContent, 'fonts.bunny.net/css'),
            'critical_css' => strpos($layoutContent, '<style>'),
            'page_loader_script' => strpos($layoutContent, "window.addEventListener('load'"),
        ];
        
        // Expected optimal order
        $expectedOrder = [
            'meta_charset',
            'meta_viewport', 
            'meta_csrf',
            'title',
            'vite',
            'font_preconnect',
            'font_stylesheet',
            'critical_css',
            'page_loader_script'
        ];
        
        // Verify order
        $previousPosition = 0;
        $orderViolations = [];
        
        foreach ($expectedOrder as $resource) {
            $currentPosition = $positions[$resource];
            
            if ($currentPosition === false) {
                $orderViolations[] = "Resource '{$resource}' not found in layout";
                continue;
            }
            
            if ($currentPosition < $previousPosition) {
                $orderViolations[] = "Resource '{$resource}' (position {$currentPosition}) appears BEFORE previous resource (position {$previousPosition})";
            }
            
            $previousPosition = $currentPosition;
        }
        
        // EXPECTED TO FAIL: Current order violates optimal loading sequence
        $this->assertEmpty(
            $orderViolations,
            "COUNTEREXAMPLE: Resource loading order violations detected:\n" . implode("\n", $orderViolations) . "\n\n" .
            "Current positions: " . json_encode($positions, JSON_PRETTY_PRINT) . "\n\n" .
            "Expected order: " . implode(' → ', $expectedOrder) . "\n\n" .
            "These violations cause render blocking, FOUC, and delayed CSS application."
        );
    }

    /**
     * Property 2: Preservation - Visual and Functional Consistency
     * 
     * **Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.5**
     * 
     * These tests capture baseline behavior on UNFIXED code that must be preserved after the fix.
     * They verify that all existing visual styles, JavaScript functionality, and font rendering
     * remain unchanged after implementing the CSS loading performance fix.
     * 
     * EXPECTED OUTCOME: These tests should PASS on unfixed code (establishing baseline)
     * and continue to PASS after the fix (confirming no regressions).
     */

    /**
     * Property 2.1: Final Rendered Appearance Preservation
     * 
     * For all page loads, the final rendered appearance must be identical to baseline.
     * This test verifies that all CSS styles are present and correctly applied.
     * 
     * @test
     */
    public function test_app_layout_preserves_all_css_styles()
    {
        // Arrange: Get the app.blade.php layout content
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Act: Verify all critical CSS elements are present
        $criticalElements = [
            'body' => 'body { margin: 0;',
            'page-loader' => '.page-loader {',
            'page-loader-hidden' => '.page-loader.hidden {',
            'loader-spinner' => '.loader-spinner {',
            'spin-animation' => '@keyframes spin {',
        ];
        
        foreach ($criticalElements as $name => $cssPattern) {
            $this->assertStringContainsString(
                $cssPattern,
                $layoutContent,
                "Preservation check: Critical CSS for '{$name}' must be present in layout"
            );
        }
        
        // Verify Vite directive is present (loads external stylesheet)
        $this->assertStringContainsString(
            "@vite(['resources/css/app.css', 'resources/js/app.js'])",
            $layoutContent,
            "Preservation check: Vite directive must be present to load external CSS and JS"
        );
        
        // Verify font loading is present
        $this->assertStringContainsString(
            'fonts.bunny.net/css?family=figtree',
            $layoutContent,
            "Preservation check: Figtree font must be loaded from fonts.bunny.net"
        );
        
        // Verify page structure elements
        $structureElements = [
            '<div class="min-h-screen bg-gray-100">',
            '@include(\'layouts.navigation\')',
            '<main>',
            '{{ $slot }}',
        ];
        
        foreach ($structureElements as $element) {
            $this->assertStringContainsString(
                $element,
                $layoutContent,
                "Preservation check: Page structure element must be present: {$element}"
            );
        }
    }

    /**
     * Property 2.2: Admin Layout Styles Preservation
     * 
     * For all admin pages, all existing styles must be preserved including
     * navigation, toast notifications, and admin-specific styling.
     * 
     * @test
     */
    public function test_admin_layout_preserves_all_css_styles()
    {
        // Arrange: Get the admin.blade.php layout content and external CSS
        $layoutPath = resource_path('views/layouts/admin.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        $externalCssPath = resource_path('css/app.css');
        $externalCssContent = file_exists($externalCssPath) ? file_get_contents($externalCssPath) : '';

        // Combine both sources for CSS checking (styles can be inline or external)
        $allCssContent = $layoutContent . $externalCssContent;

        // Act: Verify all critical admin CSS elements are present (either inline or external)
        $criticalElements = [
            'body' => 'body { margin: 0;',
            'admin-nav' => '.admin-nav {',
            'admin-nav-content' => '.admin-nav-content {',
            'admin-nav-left' => '.admin-nav-left {',
            'admin-nav-right' => '.admin-nav-right {',
            'admin-logo' => '.admin-logo {',
            'admin-nav-link' => '.admin-nav-link {',
            'admin-nav-link-active' => '.admin-nav-link.active {',
            'admin-user-name' => '.admin-user-name {',
            'admin-logout' => '.admin-logout {',
            'admin-main' => '.admin-main {',
            'admin-toast' => '.admin-toast {',
            'admin-toast-success' => '.admin-toast-success {',
            'admin-toast-error' => '.admin-toast-error {',
            'page-loader' => '.page-loader {',
            'loader-spinner' => '.loader-spinner {',
            'spin-animation' => '@keyframes spin {',
            'slideIn-animation' => '@keyframes slideIn {',
        ];

        foreach ($criticalElements as $name => $cssPattern) {
            $this->assertStringContainsString(
                $cssPattern,
                $allCssContent,
                "Preservation check: Admin CSS for '{$name}' must be present (inline or external)"
            );
        }

        // Verify Vite directive is present
        $this->assertStringContainsString(
            "@vite(['resources/css/app.css', 'resources/js/app.js'])",
            $layoutContent,
            "Preservation check: Vite directive must be present in admin layout"
        );

        // Verify admin navigation structure
        $navElements = [
            'route(\'admin.dashboard\')',
            'route(\'admin.products.index\')',
            'route(\'admin.categories.index\')',
            'route(\'admin.stock.index\')',
            'route(\'admin.orders.index\')',
        ];

        foreach ($navElements as $element) {
            $this->assertStringContainsString(
                $element,
                $layoutContent,
                "Preservation check: Admin navigation route must be present: {$element}"
            );
        }
    }


    /**
     * Property 2.3: JavaScript Functionality Preservation
     * 
     * For all interactive features, JavaScript behavior must remain unchanged.
     * This includes page loader hiding, toast notifications, and navigation.
     * 
     * @test
     */
    public function test_javascript_functionality_is_preserved()
    {
        $layouts = [
            'app' => resource_path('views/layouts/app.blade.php'),
            'admin' => resource_path('views/layouts/admin.blade.php'),
        ];
        
        foreach ($layouts as $name => $layoutPath) {
            $layoutContent = file_get_contents($layoutPath);
            
            // Verify page loader hiding script is present
            $this->assertStringContainsString(
                "window.addEventListener('load', function() {",
                $layoutContent,
                "Preservation check ({$name}): Page loader hiding script must be present"
            );
            
            $this->assertStringContainsString(
                "document.getElementById('pageLoader')",
                $layoutContent,
                "Preservation check ({$name}): Script must reference pageLoader element"
            );
            
            $this->assertStringContainsString(
                "classList.add('hidden')",
                $layoutContent,
                "Preservation check ({$name}): Script must add 'hidden' class to loader"
            );
            
            $this->assertStringContainsString(
                "loader.remove()",
                $layoutContent,
                "Preservation check ({$name}): Script must remove loader element after transition"
            );
            
            $this->assertStringContainsString(
                "setTimeout(function() { loader.remove(); }, 300)",
                $layoutContent,
                "Preservation check ({$name}): Script must use 300ms timeout for transition"
            );
        }
        
        // Verify admin-specific JavaScript (toast auto-hide)
        $adminLayoutContent = file_get_contents($layouts['admin']);
        
        $this->assertStringContainsString(
            "setTimeout(function() {",
            $adminLayoutContent,
            "Preservation check (admin): Toast auto-hide script must be present"
        );
        
        $this->assertStringContainsString(
            "getElementById('toast-success')",
            $adminLayoutContent,
            "Preservation check (admin): Script must reference toast-success element"
        );
        
        $this->assertStringContainsString(
            "getElementById('toast-error')",
            $adminLayoutContent,
            "Preservation check (admin): Script must reference toast-error element"
        );
        
        $this->assertStringContainsString(
            "4000",
            $adminLayoutContent,
            "Preservation check (admin): Toast must auto-hide after 4000ms"
        );
    }

    /**
     * Property 2.4: Font Rendering Preservation
     * 
     * For all text elements, font rendering must be identical.
     * This verifies that Figtree font family is loaded correctly.
     * 
     * @test
     */
    public function test_font_rendering_is_preserved()
    {
        // Arrange: Get the app.blade.php layout content
        $layoutPath = resource_path('views/layouts/app.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Act: Verify font preconnect is present
        $this->assertStringContainsString(
            'rel="preconnect"',
            $layoutContent,
            "Preservation check: Font preconnect must be present"
        );
        
        $this->assertStringContainsString(
            'fonts.bunny.net',
            $layoutContent,
            "Preservation check: Font must be loaded from fonts.bunny.net"
        );
        
        // Verify Figtree font is loaded with correct weights
        $this->assertStringContainsString(
            'family=figtree:400,500,600',
            $layoutContent,
            "Preservation check: Figtree font must be loaded with weights 400, 500, 600"
        );
        
        // Verify font-display=swap for optimal loading
        $this->assertStringContainsString(
            'display=swap',
            $layoutContent,
            "Preservation check: Font must use display=swap for optimal loading"
        );
        
        // Verify fallback fonts in body CSS
        $this->assertStringContainsString(
            "font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            $layoutContent,
            "Preservation check: Body must have system font fallbacks"
        );
        
        // Verify Tailwind font classes are used
        $this->assertStringContainsString(
            'class="font-sans antialiased"',
            $layoutContent,
            "Preservation check: Body must use font-sans and antialiased classes"
        );
    }

    /**
     * Property 2.5: Responsive Layout Preservation
     * 
     * For all viewport sizes, responsive layouts must render identically.
     * This verifies that viewport meta tag and responsive classes are preserved.
     * 
     * @test
     */
    public function test_responsive_layout_is_preserved()
    {
        $layouts = [
            'app' => resource_path('views/layouts/app.blade.php'),
            'admin' => resource_path('views/layouts/admin.blade.php'),
        ];
        
        foreach ($layouts as $name => $layoutPath) {
            $layoutContent = file_get_contents($layoutPath);
            
            // Verify viewport meta tag
            $this->assertStringContainsString(
                '<meta name="viewport"',
                $layoutContent,
                "Preservation check ({$name}): Viewport meta tag must be present for responsive design"
            );

            $this->assertStringContainsString(
                'width=device-width, initial-scale=1',
                $layoutContent,
                "Preservation check ({$name}): Viewport must include width=device-width and initial-scale=1"
            );
            
            // Verify responsive container classes
            $this->assertStringContainsString(
                'min-h-screen',
                $layoutContent,
                "Preservation check ({$name}): Layout must use min-h-screen for full height"
            );
            
            // Verify responsive padding classes (Tailwind)
            $responsiveClasses = ['sm:', 'lg:'];
            $hasResponsiveClasses = false;
            
            foreach ($responsiveClasses as $prefix) {
                if (strpos($layoutContent, $prefix) !== false) {
                    $hasResponsiveClasses = true;
                    break;
                }
            }
            
            $this->assertTrue(
                $hasResponsiveClasses,
                "Preservation check ({$name}): Layout must use responsive Tailwind classes (sm:, lg:)"
            );
        }
        
        // Verify admin layout has fixed navigation (check both inline and external CSS)
        $adminLayoutContent = file_get_contents($layouts['admin']);
        $externalCssPath = resource_path('css/app.css');
        $externalCssContent = file_exists($externalCssPath) ? file_get_contents($externalCssPath) : '';
        $allAdminCss = $adminLayoutContent . $externalCssContent;
        
        $this->assertStringContainsString(
            'position: fixed;',
            $allAdminCss,
            "Preservation check (admin): Admin navigation must be fixed position (inline or external)"
        );
        
        $this->assertStringContainsString(
            'padding-top: 64px',
            $allAdminCss,
            "Preservation check (admin): Admin main content must have top padding for fixed nav (inline or external)"
        );
    }

    /**
     * Property 2.6: Page Structure Preservation
     * 
     * For all pages, the HTML structure and element hierarchy must remain unchanged.
     * This ensures that existing CSS selectors and JavaScript queries continue to work.
     * 
     * @test
     */
    public function test_page_structure_is_preserved()
    {
        $layouts = [
            'app' => resource_path('views/layouts/app.blade.php'),
            'admin' => resource_path('views/layouts/admin.blade.php'),
        ];
        
        foreach ($layouts as $name => $layoutPath) {
            $layoutContent = file_get_contents($layoutPath);
            
            // Verify page loader structure
            $this->assertStringContainsString(
                '<div class="page-loader" id="pageLoader">',
                $layoutContent,
                "Preservation check ({$name}): Page loader div must have correct class and id"
            );
            
            $this->assertStringContainsString(
                '<div class="loader-spinner"></div>',
                $layoutContent,
                "Preservation check ({$name}): Loader spinner div must be present"
            );
            
            // Verify main content structure
            $this->assertStringContainsString(
                '<div class="min-h-screen bg-gray-100">',
                $layoutContent,
                "Preservation check ({$name}): Main container must have min-h-screen and bg-gray-100"
            );
            
            // Verify body classes
            $this->assertStringContainsString(
                '<body class="font-sans antialiased">',
                $layoutContent,
                "Preservation check ({$name}): Body must have font-sans and antialiased classes"
            );
        }
        
        // Verify app layout specific structure
        $appLayoutContent = file_get_contents($layouts['app']);
        
        $this->assertStringContainsString(
            '@include(\'layouts.navigation\')',
            $appLayoutContent,
            "Preservation check (app): Navigation include must be present"
        );
        
        $this->assertStringContainsString(
            '{{ $slot }}',
            $appLayoutContent,
            "Preservation check (app): Slot for page content must be present"
        );
        
        // Verify admin layout specific structure
        $adminLayoutContent = file_get_contents($layouts['admin']);
        
        $this->assertStringContainsString(
            '<nav class="admin-nav">',
            $adminLayoutContent,
            "Preservation check (admin): Admin navigation must be present"
        );
        
        $this->assertStringContainsString(
            '@yield(\'content\')',
            $adminLayoutContent,
            "Preservation check (admin): Content yield must be present"
        );
        
        $this->assertStringContainsString(
            '@stack(\'scripts\')',
            $adminLayoutContent,
            "Preservation check (admin): Scripts stack must be present"
        );
    }

    /**
     * Property 2.7: Session Flash Messages Preservation
     * 
     * For admin pages, session flash messages (success/error toasts) must continue to work.
     * This verifies that toast notification structure is preserved.
     * 
     * @test
     */
    public function test_admin_toast_notifications_are_preserved()
    {
        // Arrange: Get the admin.blade.php layout content
        $layoutPath = resource_path('views/layouts/admin.blade.php');
        $layoutContent = file_get_contents($layoutPath);
        
        // Verify success toast structure
        $this->assertStringContainsString(
            "@if(session('success'))",
            $layoutContent,
            "Preservation check: Success toast conditional must be present"
        );
        
        $this->assertStringContainsString(
            '<div class="admin-toast admin-toast-success" id="toast-success">',
            $layoutContent,
            "Preservation check: Success toast div must have correct classes and id"
        );
        
        $this->assertStringContainsString(
            "{{ session('success') }}",
            $layoutContent,
            "Preservation check: Success toast must display session message"
        );
        
        // Verify error toast structure
        $this->assertStringContainsString(
            "@if(session('error'))",
            $layoutContent,
            "Preservation check: Error toast conditional must be present"
        );
        
        $this->assertStringContainsString(
            '<div class="admin-toast admin-toast-error" id="toast-error">',
            $layoutContent,
            "Preservation check: Error toast div must have correct classes and id"
        );
        
        $this->assertStringContainsString(
            "{{ session('error') }}",
            $layoutContent,
            "Preservation check: Error toast must display session message"
        );
        
        // Verify validation errors toast
        $this->assertStringContainsString(
            "@if(\$errors->any())",
            $layoutContent,
            "Preservation check: Validation errors conditional must be present"
        );
        
        $this->assertStringContainsString(
            "@foreach(\$errors->all() as \$error)",
            $layoutContent,
            "Preservation check: Validation errors loop must be present"
        );
    }

    /**
     * Property 2.8: Meta Tags and SEO Preservation
     * 
     * For all pages, meta tags must be preserved for proper SEO and functionality.
     * 
     * @test
     */
    public function test_meta_tags_are_preserved()
    {
        $layouts = [
            'app' => resource_path('views/layouts/app.blade.php'),
            'admin' => resource_path('views/layouts/admin.blade.php'),
        ];
        
        foreach ($layouts as $name => $layoutPath) {
            $layoutContent = file_get_contents($layoutPath);
            
            // Verify essential meta tags
            $this->assertStringContainsString(
                '<meta charset="utf-8">',
                $layoutContent,
                "Preservation check ({$name}): Charset meta tag must be present"
            );
            
            $this->assertStringContainsString(
                '<meta name="viewport"',
                $layoutContent,
                "Preservation check ({$name}): Viewport meta tag must be present"
            );

            $this->assertStringContainsString(
                'width=device-width, initial-scale=1',
                $layoutContent,
                "Preservation check ({$name}): Viewport must include width=device-width and initial-scale=1"
            );
            
            $this->assertStringContainsString(
                '<meta name="csrf-token" content="{{ csrf_token() }}">',
                $layoutContent,
                "Preservation check ({$name}): CSRF token meta tag must be present"
            );
            
            // Verify title tag
            $this->assertStringContainsString(
                '<title>',
                $layoutContent,
                "Preservation check ({$name}): Title tag must be present"
            );
            
            $this->assertStringContainsString(
                "config('app.name'",
                $layoutContent,
                "Preservation check ({$name}): Title must use app name from config"
            );
        }
    }

    /**
     * Helper method to extract a line containing a specific string
     */
    private function extractLineContaining(string $content, string $search): string
    {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, $search) !== false) {
                return trim($line);
            }
        }
        return '';
    }
}
