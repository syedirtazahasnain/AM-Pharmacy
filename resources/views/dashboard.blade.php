{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Today's Sales</div>
            <div class="text-2xl font-bold text-green-600">Rs. {{ number_format($todaySales, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Monthly Sales</div>
            <div class="text-2xl font-bold text-blue-600">Rs. {{ number_format($monthlySales, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Products</div>
            <div class="text-2xl font-bold text-purple-600">{{ $totalProducts }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Customers</div>
            <div class="text-2xl font-bold text-orange-600">{{ $totalCustomers }}</div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($expiringSoon->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-800 mb-2">⚠️ Expiring Soon (3 months)</h3>
            <ul>
                @foreach($expiringSoon as $batch)
                <li class="text-sm text-yellow-700">
                    {{ $batch->product->name }} - Batch: {{ $batch->batch_number }}
                    (Expires: {{ $batch->expiry_date->format('d-m-Y') }}) - Qty: {{ $batch->quantity }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($lowStock->count() > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">🔴 Low Stock Alert</h3>
            <ul>
                @foreach($lowStock as $batch)
                <li class="text-sm text-red-700">
                    {{ $batch->product->name }} - Batch: {{ $batch->batch_number }}
                    (Only {{ $batch->quantity }} left)
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
