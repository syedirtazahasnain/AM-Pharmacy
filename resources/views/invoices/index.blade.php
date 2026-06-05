{{-- resources/views/invoices/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">All Invoices</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td class="px-6 py-4">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>Rs. {{ number_format($invoice->total, 2) }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600">View</a>
                                <a href="{{ route('invoices.print', $invoice) }}" class="text-green-600 ml-2">Print PDF</a>
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
