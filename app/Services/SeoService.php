<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    public function generateSlug(string $title, string $modelClass, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $modelClass, $excludeId)) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, string $modelClass, ?int $excludeId = null): bool
    {
        $query = $modelClass::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function generateSitemap(): string
    {
        return Cache::remember('sitemap.xml', 3600, function () {
            $products = Product::active()
                ->with('category')
                ->get()
                ->map(function ($product) {
                    return [
                        'loc' => route('product.show', $product->slug),
                        'lastmod' => $product->updated_at->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => 0.8,
                    ];
                });

            $categories = Category::active()
                ->get()
                ->map(function ($category) {
                    return [
                        'loc' => route('category.show', $category->slug),
                        'lastmod' => $category->updated_at->toIso8601String(),
                        'changefreq' => 'weekly',
                        'priority' => 0.7,
                    ];
                });

            $items = $products->concat($categories);

            return view('sitemap.index', ['items' => $items])->render();
        });
    }

    public function invalidateSitemapCache(): void
    {
        Cache::forget('sitemap.xml');
    }

    public function generateProductSchema(Product $product): array
    {
        $variants = $product->variants;
        $lowestPrice = $variants->min('price');
        $highestPrice = $variants->max('price');
        $inStock = $variants->contains(fn ($v) => $v->current_stock > 0);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->title,
            'description' => strip_tags($product->description),
            'sku' => $variants->first()?->sku,
            'brand' => [
                '@type' => 'Brand',
                'name' => config('app.name'),
            ],
            'offers' => [
                '@type' => 'AggregateOffer',
                'lowPrice' => $lowestPrice,
                'highPrice' => $highestPrice,
                'priceCurrency' => 'TRY',
                'availability' => $inStock 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
            ],
            'image' => $product->mainImage?->url,
        ];
    }

    public function generateBreadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function ($item, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            }, $items, array_keys($items)),
        ];
    }

    public function createRedirect(string $oldPath, string $newPath): Redirect
    {
        return Redirect::updateOrCreate(
            ['old_path' => $oldPath],
            [
                'new_path' => $newPath,
                'is_permanent' => true,
            ]
        );
    }

    public function findRedirect(string $path): ?Redirect
    {
        return Redirect::where('old_path', $path)->first();
    }
}
