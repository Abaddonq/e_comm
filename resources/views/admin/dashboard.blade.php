@extends('layouts.admin')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Order::count() }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Pending Orders</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Order::where('status', 'pending')->count() }}</dd>
            </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Product::count() }}</dd>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Orders</h3>
                <ul class="divide-y divide-gray-200">
                    @forelse(\App\Models\Order::latest()->limit(5)->get() as $order)
                        <li class="py-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                                <span class="text-sm text-gray-500">{{ $order->status }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">No orders yet</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.products.create') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        Add New Product
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Add New Category
                    </a>
                    <a href="{{ route('admin.stock.index') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        Manage Stock
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
