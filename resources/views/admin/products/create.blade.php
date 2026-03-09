@extends('layouts.admin')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create Product</h1>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" id="slugInput"
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
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" rows="4" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="60"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('meta_title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <input type="text" name="meta_description" value="{{ old('meta_description') }}" maxlength="160"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('meta_description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-6 mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Featured</span>
            </label>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 min-h-[44px] w-full sm:w-auto">
                Create Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center text-gray-600 hover:text-gray-800 px-6 py-2 min-h-[44px] w-full sm:w-auto">
                Cancel
            </a>
        </div>
    </form>
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
