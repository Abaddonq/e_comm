<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $cacheKey = 'sitemap_' . md5(date('Y-m-d'));
        
        $sitemap = Cache::remember($cacheKey, 3600, function () {
            $products = Product::where('is_active', true)
                ->select('slug', 'updated_at')
                ->get();
            
            $categories = Category::where('is_active', true)
                ->select('slug', 'updated_at')
                ->get();
            
            return [
                'products' => $products,
                'categories' => $categories,
            ];
        });

        $content = view('sitemap.index', [
            'products' => $sitemap['products'],
            'categories' => $sitemap['categories'],
        ])->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
