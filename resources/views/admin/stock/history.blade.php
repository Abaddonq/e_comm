@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Stock History</h1>
        <p class="text-gray-600 mt-1">{{ $variant->product->title }} - {{ $variant->sku }}</p>
    </div>
    <a href="{{ route('admin.stock.index') }}" class="text-indigo-600 hover:text-indigo-900">
        Back to Stock List
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white shadow rounded-lg p-6">
        <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $currentStock }}</dd>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <dt class="text-sm font-medium text-gray-500">Price</dt>
        <dd class="mt-1 text-3xl font-semibold text-gray-900">₺{{ number_format($variant->price, 2) }}</dd>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <dt class="text-sm font-medium text-gray-500">Status</dt>
        <dd class="mt-1">
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $currentStock < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                {{ $currentStock < 10 ? 'Low Stock' : 'In Stock' }}
            </span>
        </dd>
    </div>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Change</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($history as $movement)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $movement->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $movement->movement_type === 'sale' ? 'bg-red-100 text-red-800' : 
                               ($movement->movement_type === 'purchase' || $movement->movement_type === 'cancellation' ? 'bg-green-100 text-green-800' : 
                               ($movement->movement_type === 'refund' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="{{ $movement->quantity_change > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $movement->reference ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $movement->order_id ? '#' . $movement->order_id : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No stock movements yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $history->links() }}
</div>
@endsection
