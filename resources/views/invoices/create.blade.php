@extends('layouts.app')

@section('title', 'New Invoice')

@section('content')
<div class="max-w-full mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-center border-b pb-4 mb-6">
                <h2 class="text-3xl font-bold text-green-700">{{ $pharmacy['business']['name'] }}</h2>
                <p class="text-sm">{{ $pharmacy['business']['tagline'] }}</p>
                <p class="text-sm">{{ $pharmacy['business']['address'] }} | Ph: {{ implode(', ', $pharmacy['business']['phones']) }}</p>
                <p class="text-xs text-gray-600">
                    NTN: {{ $pharmacy['business']['ntn'] }} |
                    STRN: {{ $pharmacy['business']['strn'] }} |
                    License: {{ $pharmacy['business']['license'] }}
                </p>
            </div>

            <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer *</label>
                        <select name="customer_id" id="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)
                                    data-number="{{ $customer->customer_number }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-address="{{ $customer->address }}">
                                    {{ $customer->customer_number }} - {{ $customer->name }} ({{ $customer->district }})
                                </option>
                            @endforeach
                        </select>
                        <p id="customerDetails" class="text-xs text-gray-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salesperson *</label>
                        <select name="salesperson" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @foreach ($salesmen as $salesman)
                                <option value="{{ $salesman['name'] }}" @selected(old('salesperson', 'SELF') == $salesman['name'])>
                                    {{ $salesman['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Invoice Discount (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent"
                            value="{{ old('discount_percent', $pharmacy['invoice']['default_discount_percent']) }}"
                            step="0.01" min="0" max="100" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">GST (%)</label>
                        <input type="number" name="gst_percent" id="gst_percent"
                            value="{{ old('gst_percent', $pharmacy['invoice']['default_gst_percent']) }}"
                            step="0.01" min="0" max="100" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Remarks</label>
                        <input type="text" name="remarks" value="{{ old('remarks') }}" maxlength="500"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Optional notes">
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="font-semibold text-lg mb-2">Invoice Items</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border text-xs">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-2 py-2 text-left">Product</th>
                                    <th class="px-2 py-2 text-left">Manufacturer</th>
                                    <th class="px-2 py-2 text-left">Batch</th>
                                    <th class="px-2 py-2 text-left">Expiry</th>
                                    <th class="px-2 py-2 text-left">Pack</th>
                                    <th class="px-2 py-2 text-left">Qty</th>
                                    <th class="px-2 py-2 text-left">Rate</th>
                                    <th class="px-2 py-2 text-left">D%</th>
                                    <th class="px-2 py-2 text-right">Amount</th>
                                    <th class="px-2 py-2 text-right">Dis Value</th>
                                    <th class="px-2 py-2 text-right">Total</th>
                                    <th class="px-2 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody"></tbody>
                        </table>
                    </div>
                    <button type="button" id="addRowBtn"
                        class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">
                        + Add Item
                    </button>
                </div>

                <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="text-sm text-gray-600">
                        <p><strong>Bank:</strong> {{ $pharmacy['business']['bank']['bank_name'] }}</p>
                        <p><strong>Account No:</strong> {{ $pharmacy['business']['bank']['account_number'] }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-sm">Total No. of Items: <span id="itemCount">0</span></p>
                        <p class="text-sm">Subtotal (Gross Amount): Rs. <span id="subtotal">0.00</span></p>
                        <p class="text-sm">Discount Value: Rs. <span id="discountValue">0.00</span></p>
                        <p class="text-sm font-medium">Gross Total: Rs. <span id="grossTotal">0.00</span></p>
                        <p class="text-sm">GST (<span id="gstPercentDisplay">{{ $pharmacy['invoice']['default_gst_percent'] }}</span>%): Rs. <span id="gstValue">0.00</span></p>
                        <p class="text-xl font-bold text-green-700">Net Total Amount: Rs. <span id="totalAmount">0.00</span></p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('invoices.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">Cancel</a>
                    <button type="submit" id="submitBtn"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        disabled>Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const productsData = {!! json_encode(
    $products->map(fn ($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'manufacturer' => $p->manufacturer?->name ?? 'N/A',
        'pack_size' => $p->pack_size ?? '-',
        'mrp' => (float) $p->mrp,
        'batches' => $p->batches->map(fn ($b) => [
            'id' => $b->id,
            'batch_number' => $b->batch_number,
            'expiry_date' => $b->expiry_date->format('d-M-y'),
            'expiry_iso' => $b->expiry_date->format('Y-m-d'),
            'quantity' => (int) $b->quantity,
        ])->values(),
    ])->values()
) !!};

let rowCounter = 0;

function formatMoney(value) {
    return (Math.round(value * 100) / 100).toFixed(2);
}

function getInvoiceDiscount() {
    return parseFloat(document.getElementById('discount_percent').value) || 0;
}

function getGstPercent() {
    return parseFloat(document.getElementById('gst_percent').value) || 0;
}

function addNewRow() {
    const rowId = rowCounter++;
    const tbody = document.getElementById('itemsTableBody');
    const row = document.createElement('tr');
    row.dataset.rowId = rowId;
    row.className = 'invoice-row border-b';

    row.innerHTML = `
        <td class="px-2 py-2">
            <select name="items[${rowId}][product_id]" class="product-select w-full border rounded p-1" required>
                <option value="">Select Product</option>
                ${productsData.map(p => `<option value="${p.id}" data-mrp="${p.mrp}" data-manufacturer="${p.manufacturer}" data-pack="${p.pack_size}" data-batches='${JSON.stringify(p.batches)}'>${p.name}</option>`).join('')}
            </select>
        </td>
        <td class="px-2 py-2 manufacturer-display text-gray-600">-</td>
        <td class="px-2 py-2">
            <select name="items[${rowId}][stock_batch_id]" class="batch-select w-full border rounded p-1" required disabled>
                <option value="">Select product first</option>
            </select>
        </td>
        <td class="px-2 py-2 expiry-display text-gray-600">-</td>
        <td class="px-2 py-2 pack-display text-gray-600">-</td>
        <td class="px-2 py-2">
            <input type="number" name="items[${rowId}][quantity]" class="quantity w-16 border rounded p-1" min="1" step="1" value="1" required disabled>
            <span class="stock-hint text-xs text-gray-400 block"></span>
        </td>
        <td class="px-2 py-2">
            <input type="number" name="items[${rowId}][rate]" class="rate w-20 border rounded p-1" min="0.01" step="0.01" required disabled>
        </td>
        <td class="px-2 py-2">
            <input type="number" name="items[${rowId}][discount_percent]" class="line-discount w-14 border rounded p-1" min="0" max="100" step="0.01" value="${getInvoiceDiscount()}" required disabled>
        </td>
        <td class="px-2 py-2 text-right amount-display">0.00</td>
        <td class="px-2 py-2 text-right disc-value-display">0.00</td>
        <td class="px-2 py-2 text-right line-total font-semibold">0.00</td>
        <td class="px-2 py-2">
            <button type="button" class="remove-row text-red-600 hover:text-red-800">Remove</button>
        </td>
    `;

    tbody.appendChild(row);
    bindRowEvents(row);
    calculateTotals();
}

function bindRowEvents(row) {
    const productSelect = row.querySelector('.product-select');
    const batchSelect = row.querySelector('.batch-select');
    const qtyInput = row.querySelector('.quantity');
    const rateInput = row.querySelector('.rate');
    const lineDiscount = row.querySelector('.line-discount');

    productSelect.addEventListener('change', () => updateProductSelection(row));
    batchSelect.addEventListener('change', () => updateBatchSelection(row));
    qtyInput.addEventListener('input', () => validateQuantity(row));
    rateInput.addEventListener('input', () => updateRowTotal(row));
    lineDiscount.addEventListener('input', () => updateRowTotal(row));
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        calculateTotals();
    });
}

function updateProductSelection(row) {
    const productSelect = row.querySelector('.product-select');
    const option = productSelect.options[productSelect.selectedIndex];
    const batchSelect = row.querySelector('.batch-select');
    const qtyInput = row.querySelector('.quantity');
    const rateInput = row.querySelector('.rate');
    const lineDiscount = row.querySelector('.line-discount');

    if (!option.value) {
        row.querySelector('.manufacturer-display').textContent = '-';
        row.querySelector('.pack-display').textContent = '-';
        row.querySelector('.expiry-display').textContent = '-';
        batchSelect.innerHTML = '<option value="">Select product first</option>';
        batchSelect.disabled = true;
        qtyInput.disabled = true;
        rateInput.disabled = true;
        lineDiscount.disabled = true;
        updateRowTotal(row);
        return;
    }

    const batches = JSON.parse(option.dataset.batches || '[]');
    row.querySelector('.manufacturer-display').textContent = option.dataset.manufacturer;
    row.querySelector('.pack-display').textContent = option.dataset.pack;
    rateInput.value = parseFloat(option.dataset.mrp).toFixed(2);
    lineDiscount.value = getInvoiceDiscount().toFixed(2);

    batchSelect.innerHTML = batches.length
        ? '<option value="">Select Batch</option>' + batches.map(b =>
            `<option value="${b.id}" data-expiry="${b.expiry_date}" data-stock="${b.quantity}">${b.batch_number} (Exp: ${b.expiry_date}, Stock: ${b.quantity})</option>`
        ).join('')
        : '<option value="">No available batches</option>';

    batchSelect.disabled = batches.length === 0;
    rateInput.disabled = batches.length === 0;
    lineDiscount.disabled = batches.length === 0;
    qtyInput.disabled = true;
    row.querySelector('.expiry-display').textContent = '-';
    updateRowTotal(row);
}

function updateBatchSelection(row) {
    const batchSelect = row.querySelector('.batch-select');
    const option = batchSelect.options[batchSelect.selectedIndex];
    const qtyInput = row.querySelector('.quantity');

    if (!option.value) {
        qtyInput.disabled = true;
        row.querySelector('.expiry-display').textContent = '-';
        row.querySelector('.stock-hint').textContent = '';
        updateRowTotal(row);
        return;
    }

    const stock = parseInt(option.dataset.stock) || 0;
    row.querySelector('.expiry-display').textContent = option.dataset.expiry;
    qtyInput.disabled = false;
    qtyInput.max = stock;
    qtyInput.value = Math.min(parseInt(qtyInput.value) || 1, stock);
    row.querySelector('.stock-hint').textContent = `Max: ${stock}`;
    updateRowTotal(row);
}

function validateQuantity(row) {
    const qtyInput = row.querySelector('.quantity');
    const batchSelect = row.querySelector('.batch-select');
    const option = batchSelect.options[batchSelect.selectedIndex];
    const max = option?.value ? parseInt(option.dataset.stock) || 0 : 0;
    const val = parseInt(qtyInput.value) || 0;

    if (val > max) {
        qtyInput.value = max;
        qtyInput.classList.add('border-red-500');
    } else {
        qtyInput.classList.remove('border-red-500');
    }
    updateRowTotal(row);
}

function updateRowTotal(row) {
    const qty = parseInt(row.querySelector('.quantity').value) || 0;
    const rate = parseFloat(row.querySelector('.rate')?.value) || 0;
    const disc = parseFloat(row.querySelector('.line-discount')?.value) || 0;
    const amount = qty * rate;
    const discValue = amount * (disc / 100);
    const total = amount - discValue;

    row.querySelector('.amount-display').textContent = formatMoney(amount);
    row.querySelector('.disc-value-display').textContent = formatMoney(discValue);
    row.querySelector('.line-total').textContent = formatMoney(total);
    calculateTotals();
}

function calculateTotals() {
    const rows = document.querySelectorAll('#itemsTableBody .invoice-row');
    let subtotal = 0, discountValue = 0, grossTotal = 0, validRows = 0;

    rows.forEach(row => {
        const productId = row.querySelector('.product-select').value;
        const batchId = row.querySelector('.batch-select').value;
        if (!productId || !batchId) return;

        validRows++;
        subtotal += parseFloat(row.querySelector('.amount-display').textContent) || 0;
        discountValue += parseFloat(row.querySelector('.disc-value-display').textContent) || 0;
        grossTotal += parseFloat(row.querySelector('.line-total').textContent) || 0;
    });

    const gstPercent = getGstPercent();
    const gstValue = grossTotal * (gstPercent / 100);
    const netTotal = grossTotal + gstValue;

    document.getElementById('itemCount').textContent = validRows;
    document.getElementById('subtotal').textContent = formatMoney(subtotal);
    document.getElementById('discountValue').textContent = formatMoney(discountValue);
    document.getElementById('grossTotal').textContent = formatMoney(grossTotal);
    document.getElementById('gstPercentDisplay').textContent = gstPercent;
    document.getElementById('gstValue').textContent = formatMoney(gstValue);
    document.getElementById('totalAmount').textContent = formatMoney(netTotal);
    document.getElementById('submitBtn').disabled = validRows === 0;
}

function updateCustomerDetails() {
    const select = document.getElementById('customer_id');
    const option = select.options[select.selectedIndex];
    const el = document.getElementById('customerDetails');
    if (!option.value) { el.textContent = ''; return; }
    el.textContent = `Cust# ${option.dataset.number} | ${option.dataset.phone || ''} | ${option.dataset.address || ''}`;
}

function applyInvoiceDiscountToAllRows() {
    const disc = getInvoiceDiscount().toFixed(2);
    document.querySelectorAll('.line-discount').forEach(input => {
        if (!input.disabled) input.value = disc;
    });
    document.querySelectorAll('.invoice-row').forEach(updateRowTotal);
}

document.getElementById('addRowBtn').addEventListener('click', addNewRow);
document.getElementById('discount_percent').addEventListener('input', applyInvoiceDiscountToAllRows);
document.getElementById('gst_percent').addEventListener('input', calculateTotals);
document.getElementById('customer_id').addEventListener('change', updateCustomerDetails);

document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    const rows = document.querySelectorAll('#itemsTableBody .invoice-row');
    let hasValid = false;
    const seen = new Set();

    for (const row of rows) {
        const productId = row.querySelector('.product-select').value;
        const batchId = row.querySelector('.batch-select').value;
        if (!productId || !batchId) continue;
        hasValid = true;
        const key = productId + '-' + batchId;
        if (seen.has(key)) {
            e.preventDefault();
            alert('Duplicate product/batch found. Please merge quantities into one line.');
            return;
        }
        seen.add(key);
    }

    if (!hasValid) {
        e.preventDefault();
        alert('Add at least one complete line item.');
    }
});

addNewRow();
updateCustomerDetails();
</script>
@endsection
