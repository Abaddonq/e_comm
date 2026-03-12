@extends('layouts.admin')

@section('content')
<div class="px-6 py-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Orders</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" name="search" placeholder="Search order number..." 
                    value="{{ request('search') }}"
                    class="px-4 py-2 border rounded-lg w-full min-h-[44px] lg:col-span-2">
                
                <select name="status" class="px-4 py-2 border rounded-lg min-h-[44px]">
                    <option value="all">All Status</option>
                    @foreach(\App\Support\OrderStatusMapper::adminStatusOptions() as $code => $label)
                        <option value="{{ $code }}" {{ request('status') == $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 min-h-[44px]">
                    Filter
                </button>
            </form>
        </div>

        <div class="md:hidden divide-y divide-gray-200">
            @forelse($orders as $order)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <span class="font-medium text-indigo-600 text-sm">{{ $order->order_number }}</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                            {{ $order->internal_status_label }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-900">{{ $order->user?->name ?? 'Guest' }}</div>
                    <div class="text-sm text-gray-500">{{ $order->user?->email ?? 'No email' }}</div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="font-medium">₺{{ number_format($order->total, 2) }}</span>
                        <span class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center min-h-[44px] text-indigo-600 hover:text-indigo-900 font-medium">View</a>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-500">No orders found.</div>
            @endforelse
        </div>

        <table class="hidden md:table min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-indigo-600">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->user)
                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            @else
                                <div class="text-sm text-gray-500">Guest</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium">₺{{ number_format($order->total, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status_badge_class }}">
                                {{ $order->internal_status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
