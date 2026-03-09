@extends('layouts.admin')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Stock History</h1>
            <p class="text-gray-600 mt-1">
                {{ $variant->product->title }}
                <span class="mx-1">-</span>
                <span class="font-medium">{{ $variant->sku }}</span>
            </p>
        </div>
        <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium">
            &larr; Back to Stock List
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-5">
            <p class="text-sm font-medium text-gray-500">Current Stock</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $currentStock }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-5">
            <p class="text-sm font-medium text-gray-500">Price</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">₺{{ number_format($variant->price, 2) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-5">
            <p class="text-sm font-medium text-gray-500">Status</p>
            <div class="mt-3">
                @if($currentStock < 10)
                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Low Stock</span>
                @elseif($currentStock < 20)
                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Medium Stock</span>
                @else
                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Healthy Stock</span>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="admin-table-hint px-4 pt-3 text-xs text-gray-500">Swipe horizontally to view full table.</div>
        <div class="admin-table-scroll">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($history as $movement)
                        @php
                            $type = (string) $movement->movement_type;
                            $typeColor = match ($type) {
                                'sale' => 'bg-red-100 text-red-800',
                                'purchase', 'cancellation', 'refund' => 'bg-green-100 text-green-800',
                                'manual_adjustment' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ optional($movement->created_at)->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-full {{ $typeColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $movement->quantity_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ $movement->quantity_change }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($movement->reference_type && $movement->reference_id)
                                    {{ ucfirst($movement->reference_type) }} #{{ $movement->reference_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs break-words">
                                {{ $movement->notes ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ optional($movement->creator)->name ?: 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No stock movements found for this variant.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $history->links() }}
    </div>
</div>
@endsection
