<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.sku' => 'required_with:variants|string|max:255|distinct|unique:product_variants,sku',
            'variants.*.price' => 'required_with:variants|numeric|min:0',
            'variants.*.compare_at_price' => 'nullable|numeric|min:0',
            'variants.*.attributes' => 'nullable|array',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.initial_stock' => 'nullable|integer|min:0',
            'variants.*.is_active' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Product title is required.',
            'title.max' => 'Product title cannot exceed 255 characters.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'meta_title.max' => 'Meta title cannot exceed 60 characters.',
            'meta_description.max' => 'Meta description cannot exceed 160 characters.',
            'variants.*.sku.required_with' => 'SKU is required for each variant.',
            'variants.*.sku.distinct' => 'Each variant must have a unique SKU.',
            'variants.*.sku.unique' => 'This SKU is already in use.',
            'variants.*.price.required_with' => 'Price is required for each variant.',
            'variants.*.price.min' => 'Price cannot be negative.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Allowed image types: JPEG, PNG, WebP.',
            'images.*.max' => 'Image size cannot exceed 5MB.',
        ];
    }
}
