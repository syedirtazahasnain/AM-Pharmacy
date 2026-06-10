<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 10px; }
        .company-name { font-size: 18px; font-weight: bold; color: #166534; }
        .title { font-size: 13px; font-weight: bold; margin-top: 4px; }
        .meta { width: 100%; margin-bottom: 8px; font-size: 10px; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .customer-block { text-align: center; margin: 6px 0; font-weight: bold; font-size: 11px; }
        table.items { width: 100%; border-collapse: collapse; margin: 8px 0; }
        table.items th { background: #333; color: #fff; padding: 5px 3px; font-size: 8px; text-align: center; border: 1px solid #333; }
        table.items td { padding: 4px 3px; border: 1px solid #ccc; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary { margin-top: 8px; }
        .summary td { padding: 3px 6px; }
        .warranty { margin-top: 16px; font-size: 8px; border-top: 1px solid #000; padding-top: 8px; }
        .signature { margin-top: 24px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $pharmacy['business']['name'] }}</div>
        <div>{{ $pharmacy['business']['tagline'] }}</div>
        <div>{{ $pharmacy['business']['address'] }} | Ph # {{ implode(', ', $pharmacy['business']['phones']) }}</div>
        <div>Drug Sale License No {{ $pharmacy['business']['license'] }} | NTN NO {{ $pharmacy['business']['ntn'] }}</div>
        <div class="title">SALE TAX INVOICE</div>
    </div>

    <table class="meta">
        <tr>
            <td width="33%"><strong>Customer No:</strong> {{ $invoice->customer->customer_number }}</td>
            <td width="34%" class="text-center"><strong>Account No:</strong> {{ $pharmacy['business']['bank']['account_number'] }}</td>
            <td width="33%" class="text-right"><strong>Date:</strong> {{ $invoice->date->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center" style="font-size:9px;">
                <strong>Bank:</strong> {{ $pharmacy['business']['bank']['bank_name'] }}
            </td>
        </tr>
        <tr>
            <td colspan="2"><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</td>
            <td class="text-right"><strong>Salesperson:</strong> {{ $invoice->salesperson }}</td>
        </tr>
    </table>

    <div class="customer-block">
        {{ $invoice->customer->name }}<br>
        DISTRICT {{ $invoice->customer->district }} &amp; {{ $invoice->customer->phone }}
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>PRODUCT NAME</th>
                <th>MANUFACTURED</th>
                <th>BATCH NO</th>
                <th>EXPIRY</th>
                <th>QTY</th>
                <th>MRP/RATE</th>
                <th>D%</th>
                <th>Dis Value</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->display_product_name }}</td>
                <td>{{ $item->display_manufacturer }}</td>
                <td class="text-center">{{ $item->display_batch_number }}</td>
                <td class="text-center">{{ $item->display_expiry?->format('d-M-y') }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-center">{{ number_format($item->discount_percent, 0) }}</td>
                <td class="text-right">{{ number_format($item->discount_value, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary" style="width:100%;">
        <tr>
            <td><strong>TOTAL NO OF ITEMS:</strong> {{ $invoice->item_count }}</td>
            <td class="text-right"><strong>GROSS TOTAL:</strong> {{ number_format($invoice->gross_total, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right"><strong>GST @ {{ number_format($invoice->gst_percent, 0) }}%:</strong> {{ number_format($invoice->gst_value, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right" style="font-size:12px;"><strong>NET TOTAL:</strong> {{ number_format($invoice->total, 2) }}</td>
        </tr>
    </table>

    <div class="warranty">
        <p><strong>WARRANTY</strong></p>
        <p>{{ $invoice->warranty_notes }}</p>
        <p><strong>A.</strong> {{ $pharmacy['invoice']['warranty_note_a'] }}</p>
        <p><strong>B.</strong> {{ $pharmacy['invoice']['warranty_note_b'] }}</p>
    </div>

    <div class="signature">
        <p>Signature ____________________</p>
    </div>
</body>
</html>
