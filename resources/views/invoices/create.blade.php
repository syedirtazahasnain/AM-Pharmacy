{{-- resources/views/invoices/create.blade.php --}}
@extends('layouts.app')

@section('title', 'New Invoice')

@section('content')
    <div class="max-w-full mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="text-center border-b pb-4 mb-6">
                    <h2 class="text-3xl font-bold text-green-700">A.M PHARMACY</h2>
                    <p class="text-sm">Deals in All Kinds of Medicines Surgical & Disposable Items</p>
                    <p class="text-sm">Tulsa Road Rawalpindi | Ph: 0307-5558892</p>
                    <p class="text-xs text-gray-600">NTN: 2392714-3 | Drug License: 01-374-0176-90485P</p>
                </div>

                <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer *</label>
                            <select name="customer_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->customer_number }} -
                                        {{ $customer->name }} ({{ $customer->district }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Discount Percentage (%)</label>
                            <input type="number" name="discount_percent" id="discount_percent" value="13"
                                step="0.01" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-lg mb-2">Invoice Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Available Batch
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Quantity</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">MRP</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="addRowBtn"
                            class="mt-2 bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">+ Add
                            Item</button>
                    </div>

                    <!-- Summary -->
                    <div class="border-t pt-4 mt-4">
                        <div class="text-right">
                            <p class="text-sm">Subtotal: Rs. <span id="subtotal">0.00</span></p>
                            <p class="text-sm">Discount (<span id="discountPercentDisplay">13</span>%): Rs. <span
                                    id="discountValue">0.00</span></p>
                            <p class="text-sm">GST (18%): Rs. <span id="gstValue">0.00</span></p>
                            <p class="text-xl font-bold mt-2">Total: Rs. <span id="totalAmount">0.00</span></p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Prepare products data in PHP and pass to JavaScript safely
        const productsData = {!! json_encode(
            $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'mrp' => (float) $product->mrp,
                        'batches' => $product->batches->filter(function ($batch) {
                                return $batch->quantity > 0 && $batch->expiry_date > now();
                            })->map(function ($batch) {
                                return [
                                    'id' => $batch->id,
                                    'batch_number' => $batch->batch_number,
                                    'expiry_date' => $batch->expiry_date->format('Y-m-d'),
                                    'quantity' => (int) $batch->quantity,
                                ];
                            })->values(),
                    ];
                })->values(),
        ) !!};

        let rowCounter = 0;

        function addNewRow() {
            const rowId = rowCounter++;
            const tbody = document.getElementById('itemsTableBody');

            const newRow = document.createElement('tr');
            newRow.setAttribute('data-row-id', rowId);
            newRow.className = 'invoice-row';

            // Product select cell
            const productCell = document.createElement('td');
            productCell.className = 'px-4 py-2';
            const productSelect = document.createElement('select');
            productSelect.name = `items[${rowId}][product_id]`;
            productSelect.className = 'product-select w-full text-sm border rounded p-1';
            productSelect.setAttribute('data-row', rowId);
            productSelect.required = true;

            // Add empty option
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = 'Select Product';
            productSelect.appendChild(emptyOption);

            // Add product options
            productsData.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                option.setAttribute('data-mrp', product.mrp);
                option.setAttribute('data-batches', JSON.stringify(product.batches));
                productSelect.appendChild(option);
            });

            productCell.appendChild(productSelect);
            newRow.appendChild(productCell);

            // Batch select cell
            const batchCell = document.createElement('td');
            batchCell.className = 'px-4 py-2';
            const batchSelect = document.createElement('select');
            batchSelect.name = `items[${rowId}][stock_batch_id]`;
            batchSelect.className = 'batch-select w-full text-sm border rounded p-1';
            batchSelect.setAttribute('data-row', rowId);
            batchSelect.required = true;
            const defaultBatchOption = document.createElement('option');
            defaultBatchOption.value = '';
            defaultBatchOption.textContent = 'Select Batch First';
            batchSelect.appendChild(defaultBatchOption);
            batchCell.appendChild(batchSelect);
            newRow.appendChild(batchCell);

            // Quantity cell
            const qtyCell = document.createElement('td');
            qtyCell.className = 'px-4 py-2';
            const qtyInput = document.createElement('input');
            qtyInput.type = 'number';
            qtyInput.name = `items[${rowId}][quantity]`;
            qtyInput.className = 'quantity w-24 text-sm border rounded p-1';
            qtyInput.min = '1';
            qtyInput.step = '1';
            qtyInput.value = '1';
            qtyInput.required = true;
            qtyCell.appendChild(qtyInput);
            newRow.appendChild(qtyCell);

            // MRP display cell
            const rateCell = document.createElement('td');
            rateCell.className = 'px-4 py-2';
            const rateSpan = document.createElement('span');
            rateSpan.className = 'rate-display text-sm';
            rateSpan.textContent = '0.00';
            rateCell.appendChild(rateSpan);
            newRow.appendChild(rateCell);

            // Line total cell
            const totalCell = document.createElement('td');
            totalCell.className = 'px-4 py-2';
            const totalSpan = document.createElement('span');
            totalSpan.className = 'line-total text-sm font-semibold';
            totalSpan.textContent = '0.00';
            totalCell.appendChild(totalSpan);
            newRow.appendChild(totalCell);

            // Action cell with remove button
            const actionCell = document.createElement('td');
            actionCell.className = 'px-4 py-2';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-row text-red-600 hover:text-red-800 text-sm';
            removeBtn.textContent = 'Remove';
            actionCell.appendChild(removeBtn);
            newRow.appendChild(actionCell);

            tbody.appendChild(newRow);

            // Add event listeners
            productSelect.addEventListener('change', function() {
                updateBatchesForProduct(this, batchSelect);
                updateRowTotal(newRow);
            });

            batchSelect.addEventListener('change', function() {
                updateRowTotal(newRow);
            });

            qtyInput.addEventListener('input', function() {
                updateRowTotal(newRow);
            });

            removeBtn.addEventListener('click', function() {
                newRow.remove();
                calculateTotals();
            });
        }

        function updateBatchesForProduct(productSelect, batchSelect) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                batchSelect.innerHTML = '<option value="">Select Batch First</option>';
                return;
            }

            const batchesData = JSON.parse(selectedOption.getAttribute('data-batches') || '[]');
            const mrp = parseFloat(selectedOption.getAttribute('data-mrp') || 0);

            // Update MRP display in the row
            const row = productSelect.closest('tr');
            const rateSpan = row.querySelector('.rate-display');
            if (rateSpan && mrp > 0) {
                rateSpan.textContent = mrp.toFixed(2);
            }

            // Clear and populate batch select
            batchSelect.innerHTML = '<option value="">Select Batch</option>';

            if (batchesData.length === 0) {
                const noStockOption = document.createElement('option');
                noStockOption.value = '';
                noStockOption.textContent = 'No available batches';
                batchSelect.appendChild(noStockOption);
            } else {
                batchesData.forEach(batch => {
                    const option = document.createElement('option');
                    option.value = batch.id;
                    option.textContent =
                        `${batch.batch_number} (Exp: ${batch.expiry_date}) - Stock: ${batch.quantity}`;
                    batchSelect.appendChild(option);
                });
            }
        }

        function updateRowTotal(row) {
            const productSelect = row.querySelector('.product-select');
            const quantity = parseInt(row.querySelector('.quantity').value) || 0;
            const selectedOption = productSelect.options[productSelect.selectedIndex];

            let rate = 0;
            if (selectedOption && selectedOption.value) {
                rate = parseFloat(selectedOption.getAttribute('data-mrp') || 0);
            }

            const lineTotal = quantity * rate;
            const totalSpan = row.querySelector('.line-total');
            if (totalSpan) {
                totalSpan.textContent = lineTotal.toFixed(2);
            }

            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            const rows = document.querySelectorAll('#itemsTableBody .invoice-row');

            rows.forEach(row => {
                const totalSpan = row.querySelector('.line-total');
                if (totalSpan) {
                    subtotal += parseFloat(totalSpan.textContent) || 0;
                }
            });

            const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
            const discountValue = subtotal * (discountPercent / 100);
            const afterDiscount = subtotal - discountValue;
            const gstValue = afterDiscount * 0.18;
            const total = afterDiscount + gstValue;

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('discountPercentDisplay').textContent = discountPercent;
            document.getElementById('discountValue').textContent = discountValue.toFixed(2);
            document.getElementById('gstValue').textContent = gstValue.toFixed(2);
            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        // Event listeners
        document.getElementById('addRowBtn').addEventListener('click', addNewRow);
        document.getElementById('discount_percent').addEventListener('input', calculateTotals);

        // Add first row on page load
        addNewRow();
    </script>
@endsection
