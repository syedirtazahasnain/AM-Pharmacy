{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Products</h2>
                <a href="{{ route('products.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">+ Add Product</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manufacturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pack Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">MRP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                        <tr>
                            <td class="px-6 py-4 text-sm">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $product->manufacturer->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ $product->pack_size ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">Rs. {{ number_format($product->mrp, 2) }}</td>
                            <td class="px-6 py-4 text-sm">{{ $product->available_stock }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2" onclick="return confirm('Delete this product?')">Delete</button>
                                </form>
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
