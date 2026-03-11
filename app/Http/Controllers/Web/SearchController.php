<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        if (!RateLimiter::attempt('search-suggestions:' . $request->ip(), 60, function () {})) {
            return response()->json(['error' => 'Too many requests'], 429);
        }

        $query = $this->sanitizeQuery($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        $locale = app()->getLocale();
        $cacheKey = 'search_suggestions:' . $locale . ':' . md5(mb_strtolower($query));

        $products = Cache::remember($cacheKey, now()->addSeconds(45), function () use ($query) {
            return Product::active()
                ->with(['category', 'variants', 'images'])
                ->select('id', 'title', 'slug')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhereHas('category', function ($cq) use ($query) {
                            $cq->where('name', 'like', "%{$query}%");
                        });
                })
                ->limit(8)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'title' => e($product->title),
                        'slug' => $product->slug,
                        'price' => $product->variants->count() > 0
                            ? number_format($product->variants->min('price'), 2)
                            : null,
                        'image' => $product->images->first()
                            ? asset('storage/' . $product->images->first()->path)
                            : null,
                    ];
                })
                ->values()
                ->all();
        });

        return response()->json(['products' => $products]);
    }

    private function sanitizeQuery(string $query): string
    {
        $query = strip_tags($query);
        $query = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $query);
        $query = preg_replace('/\s+/', ' ', $query);
        return mb_substr(trim($query), 0, 100);
    }
}
