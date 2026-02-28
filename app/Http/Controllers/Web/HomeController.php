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
            ->with(['category', 'variants', 'images'])
            ->limit(8)
            ->get();

        $categories = Category::active()
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return view('web.home', compact('featuredProducts', 'categories'));
    }
}
