@extends('layouts.admin')

@section('content')
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Categories</h1>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 rounded min-h-[44px]">
        Add Category
    </a>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <div class="admin-mobile-only divide-y divide-gray-200">
        @forelse($categories as $category)
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-gray-900 truncate">
                        @if($category->parent)
                            <span class="mr-1 text-gray-400">-></span>
                        @endif
                        {{ $category->name }}
                    </div>
                    <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                </div>
                <div class="text-sm text-gray-600">Parent: {{ $category->parent->name ?? '-' }}</div>
                <div class="text-sm text-gray-600">Products: {{ $category->products->count() }}</div>
                <div class="text-sm text-gray-600">Sort Order: {{ $category->sort_order }}</div>
                <div class="flex items-center gap-4 pt-2">
                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-4 py-6 text-center text-gray-500">No categories found.</div>
        @endforelse
    </div>

    <div class="admin-desktop-block">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($category->parent)
                                    <span class="mr-2 text-gray-400">↳</span>
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->parent->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->products->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $category->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $categories->links() }}
</div>
@endsection
