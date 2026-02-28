<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::where('is_active', true)->get();
        
        if ($categories->isEmpty()) {
            $this->command->warn('No categories found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            [
                'title' => 'Modern Velvet Sofa',
                'slug' => 'modern-velvet-sofa',
                'description' => 'Luxurious modern velvet sofa perfect for contemporary living rooms.',
                'meta_title' => 'Modern Velvet Sofa | DecorMotto',
                'meta_description' => 'Shop our modern velvet sofa - luxurious comfort meets contemporary design.',
                'is_active' => true,
                'featured' => true,
                'variants' => [
                    ['sku' => 'MVS-BLK', 'price' => 2999.99, 'attributes' => ['color' => 'Black']],
                    ['sku' => 'MVS-EM', 'price' => 2999.99, 'attributes' => ['color' => 'Emerald']],
                    ['sku' => 'MVS-NVY', 'price' => 2999.99, 'attributes' => ['color' => 'Navy']],
                ],
            ],
            [
                'title' => 'Oak Dining Table',
                'slug' => 'oak-dining-table',
                'description' => 'Solid oak dining table seats 6-8 people.',
                'meta_title' => 'Oak Dining Table | DecorMotto',
                'meta_description' => 'Beautiful solid oak dining table for your family gatherings.',
                'is_active' => true,
                'featured' => true,
                'variants' => [
                    ['sku' => 'ODT-160', 'price' => 4599.00, 'attributes' => ['size' => '160cm']],
                    ['sku' => 'ODT-200', 'price' => 5499.00, 'attributes' => ['size' => '200cm']],
                ],
            ],
            [
                'title' => 'Minimalist Bookshelf',
                'slug' => 'minimalist-bookshelf',
                'description' => 'Sleek minimalist bookshelf with 5 shelves.',
                'meta_title' => 'Minimalist Bookshelf | DecorMotto',
                'meta_description' => 'Organize your space with our elegant minimalist bookshelf.',
                'is_active' => true,
                'featured' => false,
                'variants' => [
                    ['sku' => 'MB-WHT', 'price' => 1299.00, 'attributes' => ['color' => 'White']],
                    ['sku' => 'MB-WAL', 'price' => 1299.00, 'attributes' => ['color' => 'Walnut']],
                ],
            ],
            [
                'title' => 'Cozy Area Rug',
                'slug' => 'cozy-area-rug',
                'description' => 'Soft and cozy area rug for any room.',
                'meta_title' => 'Cozy Area Rug | DecorMotto',
                'meta_description' => 'Add warmth and comfort to your home with our cozy area rugs.',
                'is_active' => true,
                'featured' => true,
                'variants' => [
                    ['sku' => 'CAR-200', 'price' => 899.00, 'attributes' => ['size' => '200x300cm']],
                    ['sku' => 'CAR-240', 'price' => 1199.00, 'attributes' => ['size' => '240x340cm']],
                ],
            ],
            [
                'title' => 'Pendant Light',
                'slug' => 'pendant-light',
                'description' => 'Modern pendant light with adjustable cord.',
                'meta_title' => 'Modern Pendant Light | DecorMotto',
                'meta_description' => 'Illuminate your space with our stylish pendant lights.',
                'is_active' => true,
                'featured' => false,
                'variants' => [
                    ['sku' => 'PLD-GLD', 'price' => 459.00, 'attributes' => ['color' => 'Gold']],
                    ['sku' => 'PLD-BLK', 'price' => 459.00, 'attributes' => ['color' => 'Black']],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);
            
            $productData['category_id'] = $categories->random()->id;
            
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );

            foreach ($variants as $variantData) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'price' => $variantData['price'],
                        'compare_at_price' => $variantData['price'] * 1.2,
                        'attributes' => json_encode($variantData['attributes']),
                        'sku' => $variantData['sku'],
                    ]
                );

                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'movement_type' => 'purchase',
                    'quantity_change' => rand(10, 50),
                    'notes' => 'Initial stock',
                ]);
            }
        }

        $this->command->info('Products seeded successfully.');
    }
}
