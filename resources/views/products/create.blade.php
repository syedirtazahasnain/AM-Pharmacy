{{-- resources/views/products/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Add New Product</h2>

            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product Name *</label>
                        <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Manufacturer *</label>
                        <select name="manufacturer_id" required class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">Select Manufacturer</option>
                            @foreach($manufacturers as $manufacturer)
                                <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pack Size</label>
                        <input type="text" name="pack_size" placeholder="e.g., 1*1, 1X100" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">MRP (Retail Price) *</label>
                        <input type="number" step="0.01" name="mrp" required class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Price *</label>
                        <input type="number" step="0.01" name="purchase_price" required class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category *</label>
                        <select name="category" required class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="Medicine">Medicine</option>
                            <option value="Surgical">Surgical</option>
                            <option value="Disposable">Disposable</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Batch Number *</label>
                        <input type="text" name="batch_number" required class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expiry Date *</label>
                        <input type="date" name="expiry_date" required class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Initial Quantity *</label>
                        <input type="number" name="initial_quantity" required min="1" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
