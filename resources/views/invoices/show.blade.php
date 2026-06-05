{{-- resources/views/invoices/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-center border-b pb-4 mb-6">
            <h2 class="text-3xl font-bold text-green-700">A.M PHARMACY</h2>
            <p class="text-sm">Deals in All Kinds of Medicines Surgical & Disposable Items</p>
            <p class="text-sm">Tulsa Road Rawalpindi | Ph: 0307-5558892</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $invoice->date->format('d-M-Y') }}</p>
                <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
                <p><strong>District:</strong> {{ $invoice->customer->district }}</p>
            </div>
        </div>

        <table class="w-full border-collapse border">
            <thead class="bg-gray-100">
                <tr><th class="border p-2">Product</th><th class="border p-2">Batch</th><th class="border p-2">Qty</th><th class="border p-2">Rate</th><th class="border p-2">Total</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td class="border p-2">{{ $item->product->name }}</td>
                    <td class="border p-2">{{ $item->batch->batch_number }}</td>
                    <td class="border p-2 text-center">{{ $item->quantity }}</td>
                    <td class="border p-2 text-right">Rs. {{ number_format($item->rate, 2) }}</td>
                    <td class="border p-2 text-right">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-4">
            <p>Subtotal: Rs. {{ number_format($invoice->subtotal, 2) }}</p>
            <p>Discount ({{ $invoice->discount_percent }}%): Rs. {{ number_format($invoice->discount_value, 2) }}</p>
            <p>GST (18%): Rs. {{ number_format($invoice->gst_value, 2) }}</p>
            <p class="text-xl font-bold">Total: Rs. {{ number_format($invoice->total, 2) }}</p>
        </div>

        <div class="text-xs text-gray-600 mt-8 border-t pt-4">
            <p>{{ $invoice->warranty_notes }}</p>
            <p class="mt-2"><strong>Note:</strong> A. For dated items we must be informed five months prior to expiry</p>
            <p class="mt-4 text-right"><strong>Signature</strong></p>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('invoices.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
            <a href="{{ route('invoices.print', $invoice) }}" class="bg-green-600 text-white px-4 py-2 rounded">Download PDF</a>
        </div>
    </div>
</div>
@endsection
