<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Redirect;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_slug_generation(): void
    {
        $seoService = app(SeoService::class);
        
        $slug = $seoService->generateSlug('Test Product Name', Product::class);
        
        $this->assertEquals('test-product-name', $slug);
    }

    public function test_category_slug_generation(): void
    {
        $seoService = app(SeoService::class);
        
        $slug = $seoService->generateSlug('Test Category Name', Category::class);
        
        $this->assertEquals('test-category-name', $slug);
    }

    public function test_redirect_creation(): void
    {
        $redirect = Redirect::create([
            'old_path' => '/old-url',
            'new_path' => '/new-url',
            'status_code' => 301,
        ]);

        $this->assertDatabaseHas('redirects', [
            'old_path' => '/old-url',
            'status_code' => 301,
        ]);
    }

    public function test_redirect_model_has_permanent_method(): void
    {
        $redirect = Redirect::create([
            'old_path' => '/test',
            'new_path' => '/test-new',
            'status_code' => 301,
        ]);

        $this->assertTrue($redirect->isPermanent());
        $this->assertFalse($redirect->isTemporary());
    }

    public function test_redirect_model_has_temporary_method(): void
    {
        $redirect = Redirect::create([
            'old_path' => '/test',
            'new_path' => '/test-new',
            'status_code' => 302,
        ]);

        $this->assertFalse($redirect->isPermanent());
        $this->assertTrue($redirect->isTemporary());
    }

    public function test_sitemap_is_accessible(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    public function test_robots_txt_is_accessible(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
    }

    public function test_product_page_has_canonical_url(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $response = $this->get("/products/{$product->slug}");

        $response->assertStatus(200);
        $response->assertSee('<link rel="canonical"', false);
    }

    public function test_inactive_product_returns_404(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create([
            'is_active' => false,
        ]);

        $response = $this->get("/products/{$product->slug}");

        $response->assertStatus(404);
    }

    public function test_soft_deleted_product_returns_404(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();
        $product->delete();

        $response = $this->get("/products/{$product->slug}");

        $response->assertStatus(404);
    }

    public function test_product_page_includes_schema_org_markup(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $response = $this->get("/products/{$product->slug}");

        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
    }

    public function test_category_page_includes_meta_tags(): void
    {
        $category = Category::factory()->create();

        $response = $this->get("/categories/{$category->slug}");

        $response->assertStatus(200);
        $response->assertSee('<title>', false);
    }
}
