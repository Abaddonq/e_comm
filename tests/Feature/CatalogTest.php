<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_displays_categories(): void
    {
        Category::factory()->count(3)->create(['is_active' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_displays_featured_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'is_active' => true,
            'featured' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_category_page_shows_products(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        Product::factory()->count(5)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get('/categories/' . $category->slug);

        $response->assertStatus(200);
        $response->assertSee($category->name);
    }

    public function test_category_page_uses_pagination(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        Product::factory()->count(25)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get('/categories/' . $category->slug);

        $response->assertStatus(200);
        $response->assertSee('Pagination Navigation', false);
    }

    public function test_category_hides_inactive_products(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $activeProduct = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'title' => 'Active Product',
        ]);
        $inactiveProduct = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => false,
            'title' => 'Inactive Product',
        ]);

        $response = $this->get('/categories/' . $category->slug);

        $response->assertStatus(200);
        $response->assertSee('Active Product');
        $response->assertDontSee('Inactive Product');
    }

    public function test_product_detail_page_displays_variants(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->title);
    }

    public function test_product_detail_page_displays_images(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductImage::factory()->count(2)->create(['product_id' => $product->id]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(200);
    }

    public function test_soft_deleted_products_are_hidden(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $product->delete();

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(404);
    }

    public function test_product_page_includes_seo_meta_tags(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'meta_title' => 'Test Product SEO Title',
            'meta_description' => 'Test product SEO description',
        ]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee('Test Product SEO Title');
        $response->assertSee('Test product SEO description');
    }

    public function test_product_page_includes_schema_org_markup(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);
        ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 99.99]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee('application/ld+json');
    }

    public function test_product_page_includes_canonical_url(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(200);
        $response->assertSee('canonical');
    }

    public function test_inactive_product_returns_404(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => false,
        ]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertStatus(404);
    }
}
