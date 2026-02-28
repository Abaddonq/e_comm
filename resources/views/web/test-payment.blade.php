@extends('layouts.web')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Test Payment (Iyzico)</h1>

    @php
        $apiKey = config('payment.iyzico.api_key');
        $baseUrl = config('payment.iyzico.base_url');
        $isConfigured = !empty($apiKey);
        $isSandbox = str_contains($baseUrl ?? '', 'sandbox');
    @endphp

    @if(!$isConfigured)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">
            <p class="font-semibold">Payment Gateway Not Configured</p>
            <p class="text-sm mt-1">Please configure IYZICO_API_KEY and IYZICO_SECRET_KEY in your .env file.</p>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
            <p class="font-semibold">Payment Gateway Active</p>
            <p class="text-sm mt-1">
                Environment: {{ $isSandbox ? 'Sandbox (Test Mode)' : 'Production' }}<br>
                Base URL: {{ $baseUrl }}
            </p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Test Payment</h2>
        
        <form action="{{ route('test-payment.create') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Amount (TRY)
                </label>
                <input type="number" name="amount" id="amount" value="10.00" 
                    step="0.01" min="1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <hr class="my-6">

            <h3 class="text-lg font-medium text-gray-900 mb-4">Card Information</h3>

            <div class="mb-4">
                <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">
                    Card Number
                </label>
                <input type="text" name="card_number" id="card_number" 
                    placeholder="4506 9410 0000 0000"
                    value="4506 9410 0000 0000"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="card_holder" class="block text-sm font-medium text-gray-700 mb-2">
                    Card Holder Name
                </label>
                <input type="text" name="card_holder" id="card_holder" 
                    placeholder="John Doe"
                    value="John Doe"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="expire_month" class="block text-sm font-medium text-gray-700 mb-2">
                        Month
                    </label>
                    <select name="expire_month" id="expire_month" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $m == 12 ? 'selected' : '' }}>
                                {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="expire_year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year
                    </label>
                    <select name="expire_year" id="expire_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @for($y = date('Y'); $y <= date('Y') + 10; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-2">
                        CVV
                    </label>
                    <input type="text" name="cvv" id="cvv" 
                        placeholder="123"
                        value="123"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
            </div>

            <button type="submit" {{ !$isConfigured ? 'disabled' : '' }}
                class="w-full bg-indigo-600 text-white py-3 rounded-md hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed mt-6">
                {{ $isConfigured ? 'Pay with Iyzico' : 'Configure API Keys First' }}
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Test Card Numbers (Iyzico Sandbox)</h2>
        
        <div class="space-y-3 text-sm">
            <div class="p-3 bg-gray-50 rounded">
                <p class="font-mono text-xs">4506 9410 0000 0000</p>
                <p class="text-gray-500">Visa - Success</p>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <p class="font-mono text-xs">5526 0800 0000 0005</p>
                <p class="text-gray-500">Mastercard - Success</p>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <p class="font-mono text-xs">9792 0266 0000 0001</p>
                <p class="text-gray-500">Troy - Success</p>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <p class="font-mono text-xs">5526 0800 0000 0006</p>
                <p class="text-gray-500">Mastercard - Fail (Insufficient)</p>
            </div>
            <div class="p-3 bg-gray-50 rounded">
                <p class="font-mono text-xs">4506 9410 0000 0001</p>
                <p class="text-gray-500">Visa - Fail</p>
            </div>
        </div>
        
        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">
                <strong>CVV:</strong> Any 3 digits (e.g., 123)<br>
                <strong>Expiry:</strong> Any future date<br>
                <strong>Note:</strong> American Express requires production environment
            </p>
        </div>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">
            Back to Home
        </a>
    </div>
</div>
@endsection
