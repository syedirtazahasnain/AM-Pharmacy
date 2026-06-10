<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Tax Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .title { font-size: 14px; font-weight: bold; margin-top: 6px; text-decoration: underline; }
        .meta { width: 100%; margin-bottom: 10px; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .customer-block { text-align: center; margin: 8px 0; font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table.items th { background: #333; color: #fff; padding: 5px 4px; font-size: 9px; text-align: center; border: 1px solid #333; }
        table.items td { padding: 4px; border: 1px solid #ccc; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-box { float: right; width: 220px; background: #f0f0f0; border: 1px solid #999; padding: 8px; margin-top: 8px; }
        .totals-box p { margin: 3px 0; }
        .totals-box .net { font-size: 13px; font-weight: bold; border-top: 1px solid #999; padding-top: 4px; margin-top: 4px; }
        .summary-left { float: left; margin-top: 8px; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .warranty { margin-top: 20px; font-size: 8px; border-top: 1px solid #000; padding-top: 8px; }
        .signature { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $pharmacy['business']['name'] }}</div>
        <div>{{ $pharmacy['business']['tagline'] }}</div>
        <div>{{ $pharmacy['business']['address'] }}. Ph # {{ implode(', ', $pharmacy['business']['phones']) }}</div>
        <div>NTN NO {{ $pharmacy['business']['ntn'] }} &nbsp; STRN NO {{ $pharmacy['business']['strn'] }}</div>
        <div class="title">SALES TAX INVOICE</div>
    </div>

    <table class="meta">
        <tr>
            <td width="50%"><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</td>
            <td width="50%" class="text-right"><strong>Date:</strong> {{ $invoice->date->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td><strong>Customer No:</strong> {{ $invoice->customer->customer_number }}</td>
            <td class="text-right"><strong>Salesperson:</strong> {{ $invoice->salesperson }}</td>
        </tr>
    </table>

    <div class="customer-block">
        {{ $invoice->customer->name }}<br>
        {{ $invoice->customer->district }} &amp; {{ $invoice->customer->phone }}
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>ITEM NAME</th>
                <th>PACK</th>
                <th>QTY</th>
                <th>RATE</th>
                <th>BATCH-NO</th>
                <th>AMOUNT</th>
                <th>Disc%</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->display_product_name }}</td>
                <td class="text-center">{{ $item->display_pack_size }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                <td class="text-center">{{ $item->display_batch_number }}</td>
                <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                <td class="text-center">{{ number_format($item->discount_percent, 0) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="summary-left">
            <p><strong>TOTAL NO OF ITEMS:</strong> {{ $invoice->item_count }}</p>
            @if($invoice->remarks)
                <p><strong>Remarks:</strong> {{ $invoice->remarks }}</p>
            @endif
        </div>
        <div class="totals-box">
            <p><strong>GROSS TOTAL:</strong> <span style="float:right">{{ number_format($invoice->gross_total, 2) }}</span></p>
            <p><strong>GST @ {{ number_format($invoice->gst_percent, 0) }}%:</strong> <span style="float:right">{{ number_format($invoice->gst_value, 2) }}</span></p>
            <p class="net"><strong>NET TOTAL AMOUNT:</strong> <span style="float:right">{{ number_format($invoice->total, 2) }}</span></p>
        </div>
    </div>

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
