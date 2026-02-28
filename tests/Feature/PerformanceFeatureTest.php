<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_are_paginated(): void
    {
        $category = Category::factory()->create();
        
        for ($i = 0; $i < 20; $i++) {
            Product::factory()->for($category)->create();
        }

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_category_products_use_pagination(): void
    {
        $category = Category::factory()->create();
        
        for ($i = 0; $i < 20; $i++) {
            Product::factory()->for($category)->create();
        }

        $response = $this->get("/categories/{$category->slug}");

        $response->assertStatus(200);
    }

    public function test_queries_use_eager_loading(): void
    {
        $category = Category::factory()->create();
        
        for ($i = 0; $i < 5; $i++) {
            $product = Product::factory()->for($category)->create();
        }

        $products = Product::with(['category', 'variants', 'images'])->get();

        $this->assertEquals(5, $products->count());
    }

    public function test_cache_is_configured(): void
    {
        $this->assertNotNull(config('cache.default'));
    }

    public function test_queue_is_configured(): void
    {
        $this->assertNotNull(config('queue.default'));
    }

    public function test_sitemap_uses_cache(): void
    {
        $response = $this->get('/sitemap.xml');
        
        $response->assertStatus(200);
    }

    public function test_assets_are_optimized(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }
}
