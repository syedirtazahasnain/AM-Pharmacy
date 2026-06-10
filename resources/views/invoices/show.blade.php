@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_number)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-center border-b pb-4 mb-6">
            <h2 class="text-3xl font-bold text-green-700">{{ $pharmacy['business']['name'] }}</h2>
            <p class="text-sm">{{ $pharmacy['business']['tagline'] }}</p>
            <p class="text-sm">{{ $pharmacy['business']['address'] }} | Ph: {{ implode(', ', $pharmacy['business']['phones']) }}</p>
            <p class="text-xs text-gray-600">
                NTN: {{ $pharmacy['business']['ntn'] }} | STRN: {{ $pharmacy['business']['strn'] }} | License: {{ $pharmacy['business']['license'] }}
            </p>
            <p class="text-lg font-bold mt-2 underline">SALES TAX INVOICE</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div>
                <p><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Customer No:</strong> {{ $invoice->customer->customer_number }}</p>
                <p><strong>Customer:</strong> {{ $invoice->customer->name }}</p>
                <p><strong>District:</strong> {{ $invoice->customer->district }}</p>
                <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
            </div>
            <div class="text-right">
                <p><strong>Date:</strong> {{ $invoice->date->format('d-M-Y') }}</p>
                <p><strong>Salesperson:</strong> {{ $invoice->salesperson }}</p>
                <p><strong>Account No:</strong> {{ $pharmacy['business']['bank']['account_number'] }}</p>
                <p class="text-xs">{{ $pharmacy['business']['bank']['bank_name'] }}</p>
            </div>
        </div>
        @if($invoice->remarks)
            <p class="text-sm mb-4"><strong>Remarks:</strong> {{ $invoice->remarks }}</p>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border text-xs">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="border p-2">Product</th>
                        <th class="border p-2">Manufacturer</th>
                        <th class="border p-2">Batch</th>
                        <th class="border p-2">Expiry</th>
                        <th class="border p-2">Pack</th>
                        <th class="border p-2">Qty</th>
                        <th class="border p-2">Rate</th>
                        <th class="border p-2">D%</th>
                        <th class="border p-2">Dis Value</th>
                        <th class="border p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="border p-2">{{ $item->display_product_name }}</td>
                        <td class="border p-2">{{ $item->display_manufacturer }}</td>
                        <td class="border p-2 text-center">{{ $item->display_batch_number }}</td>
                        <td class="border p-2 text-center">{{ $item->display_expiry?->format('d-M-y') }}</td>
                        <td class="border p-2 text-center">{{ $item->display_pack_size }}</td>
                        <td class="border p-2 text-center">{{ $item->quantity }}</td>
                        <td class="border p-2 text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="border p-2 text-center">{{ number_format($item->discount_percent, 0) }}</td>
                        <td class="border p-2 text-right">{{ number_format($item->discount_value, 2) }}</td>
                        <td class="border p-2 text-right font-semibold">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <p class="text-sm"><strong>Total No. of Items:</strong> {{ $invoice->item_count }}</p>
            <div class="text-right space-y-1 text-sm">
                <p>Subtotal: Rs. {{ number_format($invoice->subtotal, 2) }}</p>
                <p>Discount Value: Rs. {{ number_format($invoice->discount_value, 2) }}</p>
                <p class="font-medium">Gross Total: Rs. {{ number_format($invoice->gross_total, 2) }}</p>
                <p>GST ({{ number_format($invoice->gst_percent, 0) }}%): Rs. {{ number_format($invoice->gst_value, 2) }}</p>
                <p class="text-xl font-bold text-green-700">Net Total: Rs. {{ number_format($invoice->total, 2) }}</p>
            </div>
        </div>

        <div class="text-xs text-gray-600 mt-8 border-t pt-4">
            <p class="font-bold mb-1">WARRANTY</p>
            <p>{{ $invoice->warranty_notes }}</p>
            <p class="mt-2"><strong>A.</strong> {{ $pharmacy['invoice']['warranty_note_a'] }}</p>
            <p><strong>B.</strong> {{ $pharmacy['invoice']['warranty_note_b'] }}</p>
            <p class="mt-4 text-right"><strong>Signature</strong> ____________________</p>
        </div>

        <div class="mt-6 flex flex-wrap justify-between gap-3">
            <a href="{{ route('invoices.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
            <div class="flex gap-2">
                <a href="{{ route('invoices.print', ['invoice' => $invoice, 'format' => 'sales_tax']) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded">Print Sales Tax Invoice</a>
                <a href="{{ route('invoices.print', ['invoice' => $invoice, 'format' => 'warranty']) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded">Print Warranty Invoice</a>
            </div>
        </div>
    </div>
</div>
@endsection
