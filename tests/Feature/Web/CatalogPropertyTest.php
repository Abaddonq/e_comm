<?php

namespace Tests\Feature\Web;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 8: Category Pages Use Pagination
     * Validates: Requirements 4.3, 15.1
     */
    public function test_category_pages_use_pagination(): void
    {
        $category = Category::factory()->create();

        // Create 25 active products
        Product::factory(25)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertStatus(200);
        $response->assertViewHas('products');

        $products = $response->viewData('products');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class , $products);
        $this->assertEquals(20, $products->perPage()); // Based on $query->paginate(20) in CategoryController
        $this->assertEquals(25, $products->total());
    }

    /**
     * Property 9: Product Listings Include Required Fields
     * Validates: Requirements 4.1
     */
    public function test_product_listings_include_required_fields(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 299.99
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertStatus(200);
        $response->assertSee($product->title);
        $response->assertSee('299.99'); // Ensure price is displayed
    }

    /**
     * Property 10: Product Detail Pages Include All Variants and Images
     * Validates: Requirements 4.4, 4.5
     */
    public function test_product_detail_includes_variants_and_images(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'description' => 'Detailed product description.'
        ]);

        $variant1 = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'VAR-1', 'price' => 10]);
        $variant2 = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'VAR-2', 'price' => 20]);

        $product->images()->create([
            'path' => 'img1.jpg',
            'alt_text' => 'img1 alt',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee($product->title);
        $response->assertSee($product->description);
        $response->assertSee('VAR-1');
        $response->assertSee('VAR-2');
        $response->assertSee('storage/img1.jpg', false);
    }

    /**
     * Property 11: Product Images Use Lazy Loading
     * Validates: Requirements 4.6
     */
    public function test_product_images_use_lazy_loading(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $product->images()->create([
            'path' => 'main-image.jpg',
            'alt_text' => 'main image alt',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('loading="lazy"', false); // HTML attribute check without escaping
    }

    /**
     * Property 12: Soft-Deleted Products Are Hidden From Public Views
     * Validates: Requirements 4.1
     */
    public function test_soft_deleted_and_inactive_products_are_hidden(): void
    {
        $category = Category::factory()->create();

        $activeProduct = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'title' => 'Active Product Name'
        ]);

        $inactiveProduct = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => false,
            'title' => 'Inactive Product Name'
        ]);

        $deletedProduct = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'title' => 'Deleted Product Name',
            'deleted_at' => now(), // Soft delete
        ]);

        $response = $this->get(route('category.show', $category->slug));

        $response->assertStatus(200);
        $response->assertSee($activeProduct->title);
        $response->assertDontSee($inactiveProduct->title);
        $response->assertDontSee($deletedProduct->title);
    }
}
