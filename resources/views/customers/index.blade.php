{{-- resources/views/customers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Customers</h2>
                <a href="{{ route('customers.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg">+ Add Customer</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr><th class="px-6 py-3 text-left">ID</th><th>Name</th><th>District</th><th>Phone</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr><td class="px-6 py-4">{{ $customer->customer_number }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->district }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td><a href="#" class="text-blue-600">View Invoices</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
