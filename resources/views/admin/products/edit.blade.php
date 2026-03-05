@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="POST" action="{{ route('admin.products.update', $product->id) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" value="{{ old('title', $product->title) }}" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" id="slugInput"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('slug') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        <p class="text-gray-500 text-xs mt-1">Boşluklar otomatik olarak tireye (-) dönüştürülür</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="4" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="60"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <input type="text" name="meta_description" value="{{ old('meta_description', $product->meta_description) }}" maxlength="160"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex gap-6 mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Featured</span>
                    </label>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                        Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-800 px-6 py-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Variants</h2>
            
            <table class="min-w-full divide-y divide-gray-200 mb-4">
                <thead>
                    <tr>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($product->variants as $variant)
                        <tr>
                            <td class="py-2">{{ $variant->sku }}</td>
                            <td class="py-2">₺{{ number_format($variant->price, 2) }}</td>
                            <td class="py-2">
                                <span class="{{ $variant->current_stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $variant->current_stock }}
                                </span>
                            </td>
                            <td class="py-2 text-right">
                                <button type="button" onclick="editVariant({{ $variant->id }}, '{{ $variant->sku }}', {{ $variant->price }})" 
                                    class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                                <form action="{{ route('admin.products.variants.destroy', [$product->id, $variant->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">No variants yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <h3 class="text-lg font-medium mb-2">Add Variant</h3>
            <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <div>
                    <input type="text" name="sku" placeholder="SKU *" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div>
                    <input type="number" name="price" placeholder="Price *" step="0.01" min="0" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div>
                    <input type="number" name="initial_stock" placeholder="Initial Stock" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div>
                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Add
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Images</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                @forelse($product->images as $image)
                    <div class="relative border rounded p-2">
                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->alt_text }}" class="w-full h-24 object-cover rounded">
                        @if($image->is_primary)
                            <span class="absolute top-1 right-1 bg-indigo-600 text-white text-xs px-1 rounded">Primary</span>
                        @endif
                        <div class="flex justify-between mt-2">
                            @if(!$image->is_primary)
                                <form action="{{ route('admin.products.images.set-primary', [$product->id, $image->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900">Set Primary</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.products.images.destroy', [$product->id, $image->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-900" onclick="return confirm('Delete image?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="col-span-4 text-center text-gray-500 py-4">No images uploaded.</p>
                @endforelse
            </div>

            <h3 class="text-lg font-medium mb-2">Upload Images</h3>
            <form action="{{ route('admin.products.images.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="images[]" multiple accept="image/avif,image/webp" 
                    class="w-full rounded-md border-gray-300 shadow-sm mb-2">
                <p class="text-sm text-gray-500 mb-2">Max 5MB per file. AVIF and WebP only.</p>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Upload
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Product Details</h2>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Featured</dt>
                    <dd class="mt-1">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->featured ? 'Yes' : 'No' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Updated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const slugInput = document.getElementById('slugInput');
    const titleInput = document.querySelector('input[name="title"]');
    
    if (slugInput && titleInput) {
        let isManualSlug = false;
        
        slugInput.addEventListener('input', () => {
            isManualSlug = true;
            slugInput.value = slugInput.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
        });
        
        titleInput.addEventListener('input', () => {
            if (!isManualSlug && titleInput.value) {
                let slug = titleInput.value.toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9\-]/g, '')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                slugInput.value = slug;
            }
        });
    }
</script>
@endpush
