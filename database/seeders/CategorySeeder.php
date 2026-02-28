<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Living Room',
                'slug' => 'living-room',
                'description' => 'Modern living room furniture and decor',
                'is_active' => true,
            ],
            [
                'name' => 'Bedroom',
                'slug' => 'bedroom',
                'description' => 'Bedroom furniture, bedding, and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen',
                'slug' => 'kitchen',
                'description' => 'Kitchenware, appliances, and dining essentials',
                'is_active' => true,
            ],
            [
                'name' => 'Bathroom',
                'slug' => 'bathroom',
                'description' => 'Bathroom accessories and fixtures',
                'is_active' => true,
            ],
            [
                'name' => 'Office',
                'slug' => 'office',
                'description' => 'Home office furniture and supplies',
                'is_active' => true,
            ],
            [
                'name' => 'Outdoor',
                'slug' => 'outdoor',
                'description' => 'Garden and outdoor furniture',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully.');
    }
}
