@extends('layouts.web')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Addresses</h1>
        <a href="{{ route('addresses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Add New Address
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($addresses->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">You haven't added any addresses yet.</p>
            <a href="{{ route('addresses.create') }}" class="text-indigo-600 hover:text-indigo-800">
                Add your first address
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white rounded-lg shadow p-6 relative {{ $address->is_default ? 'border-2 border-indigo-500' : '' }}">
                    @if($address->is_default)
                        <span class="absolute top-4 right-4 bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">
                            Default
                        </span>
                    @endif
                    
                    <h3 class="text-lg font-semibold text-gray-900">{{ $address->full_name }}</h3>
                    <p class="text-gray-600 mt-2">{{ $address->address_line1 }}</p>
                    @if($address->address_line2)
                        <p class="text-gray-600">{{ $address->address_line2 }}</p>
                    @endif
                    <p class="text-gray-600">
                        {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}
                    </p>
                    <p class="text-gray-600">{{ $address->country }}</p>
                    <p class="text-gray-500 mt-2">{{ $address->phone }}</p>

                    <div class="mt-4 flex space-x-4">
                        <a href="{{ route('addresses.edit', $address->id) }}" class="text-indigo-600 hover:text-indigo-800">
                            Edit
                        </a>
                        <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
