{{-- resources/views/stock/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Report')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Stock Report</h2>

            <div class="mb-6 grid grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded">
                    <div class="text-sm text-gray-600">Available Stock Value</div>
                    <div class="text-xl font-bold text-green-600">
                        Rs. {{ number_format($availableBatches->sum(fn($b) => $b->quantity * $b->cost_price), 2) }}
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded">
                    <div class="text-sm text-gray-600">Expiring Soon</div>
                    <div class="text-xl font-bold text-yellow-600">{{ $expiringBatches->count() }} batches</div>
                </div>
                <div class="bg-red-50 p-4 rounded">
                    <div class="text-sm text-gray-600">Expired Stock</div>
                    <div class="text-xl font-bold text-red-600">{{ $expiredBatches->count() }} batches</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Product</th>
                            <th>Batch #</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                        <tr class="{{ $batch->isExpired() ? 'bg-red-50' : ($batch->isExpiringSoon() ? 'bg-yellow-50' : '') }}">
                            <td class="px-6 py-4">{{ $batch->product->name }}</td>
                            <td>{{ $batch->batch_number }}</td>
                            <td>{{ $batch->expiry_date->format('d-m-Y') }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>Rs. {{ number_format($batch->cost_price, 2) }}</td>
                            <td>
                                @if($batch->isExpired())
                                    <span class="text-red-600 font-semibold">EXPIRED</span>
                                @elseif($batch->isExpiringSoon())
                                    <span class="text-yellow-600">Expiring Soon</span>
                                @else
                                    <span class="text-green-600">Good</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
