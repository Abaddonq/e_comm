@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Stock Management</h1>
    <button onclick="showAdjustModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
        Adjust Stock
    </button>
</div>

<div class="bg-white shadow rounded-lg mb-6">
    <form method="GET" action="{{ route('admin.stock.index') }}" class="p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by SKU or product name..." 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <label class="flex items-center">
            <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">Low Stock Only</span>
        </label>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700">
            Filter
        </button>
        <a href="{{ route('admin.stock.index') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2">
            Clear
        </a>
    </form>
</div>

<div class="bg-white shadow overflow-hidden rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($variants as $variant)
                <tr class="{{ $variant->current_stock < 10 ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $variant->product->title }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $variant->sku }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ₺{{ number_format($variant->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $variant->current_stock < 10 ? 'bg-red-100 text-red-800' : ($variant->current_stock < 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ $variant->current_stock }}
                        </span>
                        @if($variant->current_stock < 10)
                            <span class="text-xs text-red-600 ml-1">Low!</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.stock.history', $variant->id) }}" class="text-indigo-600 hover:text-indigo-900">History</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No variants found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $variants->links() }}
</div>

<div id="adjustModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Adjust Stock</h3>
            <button onclick="hideAdjustModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <form method="POST" action="{{ route('admin.stock.adjust') }}">
            @csrf
            <div id="adjustmentsContainer" class="space-y-4 max-h-64 overflow-y-auto">
                <div class="grid grid-cols-4 gap-4">
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Quantity (+/-)</label>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-4 adjustment-row">
                    <div class="col-span-1">
                        <input type="text" name="adjustments[0][sku]" placeholder="Enter SKU" required
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="col-span-1">
                        <input type="number" name="adjustments[0][quantity]" placeholder="e.g. 10 or -5" required
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="col-span-2">
                        <input type="text" name="adjustments[0][reason]" placeholder="Reason for adjustment" required
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="addAdjustmentRow()" class="mt-4 text-indigo-600 hover:text-indigo-900 text-sm">
                + Add Another
            </button>
            
            <div class="mt-6 flex justify-end gap-4">
                <button type="button" onclick="hideAdjustModal()" class="text-gray-600 hover:text-gray-800 px-4 py-2">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Adjust Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showAdjustModal() {
    document.getElementById('adjustModal').classList.remove('hidden');
}

function hideAdjustModal() {
    document.getElementById('adjustModal').classList.add('hidden');
}

let adjustmentCount = 1;

function addAdjustmentRow() {
    const container = document.getElementById('adjustmentsContainer');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-4 gap-4 adjustment-row';
    row.innerHTML = `
        <div class="col-span-1">
            <input type="text" name="adjustments[${adjustmentCount}][sku]" placeholder="Enter SKU" required
                class="w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div class="col-span-1">
            <input type="number" name="adjustments[${adjustmentCount}][quantity]" placeholder="e.g. 10 or -5" required
                class="w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div class="col-span-2">
            <input type="text" name="adjustments[${adjustmentCount}][reason]" placeholder="Reason for adjustment" required
                class="w-full rounded-md border-gray-300 shadow-sm">
        </div>
    `;
    container.appendChild(row);
    adjustmentCount++;
}
</script>
@endsection
