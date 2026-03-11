<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $featuredProducts = Product::active()
            ->featured()
            ->select(['id', 'category_id', 'title', 'slug'])
            ->withMin('variants', 'price')
            ->with([
                'variants:id,product_id,price',
                'images:id,product_id,path,is_primary,sort_order',
            ])
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return view('web.home', compact('featuredProducts', 'categories'));
    }
}
