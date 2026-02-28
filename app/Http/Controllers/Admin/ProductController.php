<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\SeoService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected SeoService $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'images']);

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }
            elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(\App\Http\Requests\Admin\StoreProductRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->seoService->generateSlug($validated['title'], Product::class);
        }

        $product = Product::create($validated);

        // Process variants if present (we'll implement full logic in ProductVariantController but good to handle initial creation)
        if (!empty($validated['variants'])) {
        // Placeholder: basic support for initial variant creation could go here, 
        // but requirements say variant management is in ProductVariantController
        }

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'Product created successfully. Add variants and images.');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'images', 'category']);
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(\App\Http\Requests\Admin\StoreProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = $this->seoService->generateSlug(
                $validated['title'],
                Product::class ,
                $product->id
            );
        }

        $oldSlug = $product->slug;
        $product->update($validated);

        if ($oldSlug !== $product->slug) {
            $this->seoService->createRedirect($oldSlug, $product->slug);
        }

        return redirect()->route('admin.products.edit', $product->id)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Product {$status} successfully.");
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => !$product->featured]);

        $status = $product->featured ? 'added to' : 'removed from';

        return back()->with('success', "Product {$status} featured successfully.");
    }
}
