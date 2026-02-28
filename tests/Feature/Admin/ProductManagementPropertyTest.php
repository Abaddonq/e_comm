<?php

namespace Tests\Unit\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 7: Product Slug Generation Is Unique
     * Validates: Requirements 3.4
     */
    public function test_product_slug_generation_is_unique(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $product1 = Product::factory()->create([
            'title' => 'Unique Desk',
            'slug' => 'unique-desk',
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), [
            'title' => 'Unique Desk',
            'description' => 'Test description',
            'category_id' => $category->id,
        ]);

        // SeoService handles slug generation, append -1 if exists
        $product2 = Product::latest()->first();

        $this->assertNotEquals($product1->slug, $product2->slug);
        $this->assertEquals('unique-desk-1', $product2->slug);
    }

    /**
     * Property 44: Image Upload Validates File Type and Size
     * Validates: Requirements 13.5
     */
    public function test_image_upload_validates_file_type_and_size(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        // Large file test
        $largeFile = \Illuminate\Http\Testing\File::create('large.jpg', 6000); // 6MB
        // Invalid type test
        $invalidFile = \Illuminate\Http\Testing\File::create('document.pdf', 100);

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), [
            'title' => 'New Product',
            'description' => 'Test description',
            'category_id' => $category->id,
            'images' => [
                $largeFile,
                $invalidFile
            ]
        ]);

        $response->assertSessionHasErrors([
            'images.0' => 'Image size cannot exceed 5MB.',
            'images.1' => 'Each file must be an image.'
        ]);
    }

    /**
     * Property 51: Stock Adjustments Create Movement Records
     * Validates: Requirements 18.6
     */
    public function test_stock_adjustments_create_movement_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($admin)
            ->post(route('admin.stock.adjust'), [
            'adjustments' => [
                [
                    'variant_id' => $variant->id,
                    'quantity' => 15,
                    'reason' => 'Inventory Count',
                ]
            ]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'movement_type' => 'manual_adjustment',
            'quantity_change' => 15,
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Property 52: Admin Stock Display Calculates From Movements
     * Validates: Requirements 18.7
     */
    public function test_admin_stock_display_calculates_from_movements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        // Setup initial stock via movement
        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'purchase',
            'quantity_change' => 50,
        ]);

        // Setup a sale movement
        StockMovement::create([
            'product_variant_id' => $variant->id,
            'movement_type' => 'sale',
            'quantity_change' => -10,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.stock.index'));

        $response->assertStatus(200);
        $response->assertSee('40'); // 50 - 10 = 40
    }
}
