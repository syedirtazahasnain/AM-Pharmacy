<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function generateInvoiceNumber(): string
    {
        $starting = config('pharmacy.invoice.starting_number', 1001);

        $lastNumber = Invoice::lockForUpdate()
            ->orderByRaw('CAST(invoice_number AS UNSIGNED) DESC')
            ->value('invoice_number');

        if (!$lastNumber) {
            return (string) $starting;
        }

        $next = max((int) $lastNumber + 1, $starting);

        return (string) $next;
    }

    public function buildWarrantyText(): string
    {
        $template = config('pharmacy.invoice.warranty_text');

        return str_replace(
            [':owner', ':shop'],
            [config('pharmacy.business.owner_name'), config('pharmacy.business.shop_address')],
            $template
        );
    }

    /**
     * @param  array<int, array{product_id: int, stock_batch_id: int, quantity: int, rate?: float, discount_percent?: float}>  $items
     * @return array{invoice: array, items: array}
     */
    public function calculate(array $items, float $invoiceDiscountPercent, float $gstPercent): array
    {
        $subtotal = 0;
        $totalDiscountValue = 0;
        $grossTotal = 0;
        $calculatedItems = [];

        foreach ($items as $index => $item) {
            $product = Product::with('manufacturer')->findOrFail($item['product_id']);
            $batch = StockBatch::findOrFail($item['stock_batch_id']);

            $rate = isset($item['rate']) ? round((float) $item['rate'], 2) : round((float) $product->mrp, 2);
            $quantity = (int) $item['quantity'];
            $lineDiscountPercent = isset($item['discount_percent'])
                ? round((float) $item['discount_percent'], 2)
                : round($invoiceDiscountPercent, 2);

            $amount = round($rate * $quantity, 2);
            $discountValue = round($amount * ($lineDiscountPercent / 100), 2);
            $lineTotal = round($amount - $discountValue, 2);

            $subtotal += $amount;
            $totalDiscountValue += $discountValue;
            $grossTotal += $lineTotal;

            $calculatedItems[] = [
                'index' => $index,
                'product' => $product,
                'batch' => $batch,
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => $amount,
                'discount_percent' => $lineDiscountPercent,
                'discount_value' => $discountValue,
                'total' => $lineTotal,
                'product_name' => $product->name,
                'manufacturer_name' => $product->manufacturer?->name ?? 'N/A',
                'batch_number' => $batch->batch_number,
                'expiry_date' => $batch->expiry_date,
                'pack_size' => $product->pack_size,
            ];
        }

        $grossTotal = round($grossTotal, 2);
        $gstValue = round($grossTotal * ($gstPercent / 100), 2);
        $netTotal = round($grossTotal + $gstValue, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_value' => round($totalDiscountValue, 2),
            'gross_total' => $grossTotal,
            'gst_value' => $gstValue,
            'total' => $netTotal,
            'item_count' => count($calculatedItems),
            'items' => $calculatedItems,
        ];
    }

    /**
     * @param  array<int, array{product_id: int, stock_batch_id: int, quantity: int, rate?: float, discount_percent?: float}>  $items
     */
    public function validateItems(array $items): void
    {
        if (empty($items)) {
            throw new \InvalidArgumentException('At least one invoice item is required.');
        }

        $seen = [];

        foreach ($items as $index => $item) {
            $row = $index + 1;
            $product = Product::find($item['product_id']);
            $batch = StockBatch::find($item['stock_batch_id']);

            if (!$product) {
                throw new \InvalidArgumentException("Row {$row}: Product not found.");
            }

            if (!$batch) {
                throw new \InvalidArgumentException("Row {$row}: Batch not found.");
            }

            if ($batch->product_id !== $product->id) {
                throw new \InvalidArgumentException("Row {$row}: Batch {$batch->batch_number} does not belong to {$product->name}.");
            }

            if ($batch->expiry_date <= now()->startOfDay()) {
                throw new \InvalidArgumentException("Row {$row}: Batch {$batch->batch_number} has expired.");
            }

            if ($batch->quantity <= 0) {
                throw new \InvalidArgumentException("Row {$row}: Batch {$batch->batch_number} is out of stock.");
            }

            if ((int) $item['quantity'] < 1) {
                throw new \InvalidArgumentException("Row {$row}: Quantity must be at least 1.");
            }

            if ((int) $item['quantity'] > $batch->quantity) {
                throw new \InvalidArgumentException(
                    "Row {$row}: Insufficient stock for {$product->name}. Available: {$batch->quantity}, Requested: {$item['quantity']}"
                );
            }

            $rate = isset($item['rate']) ? (float) $item['rate'] : (float) $product->mrp;
            if ($rate <= 0) {
                throw new \InvalidArgumentException("Row {$row}: Rate must be greater than zero.");
            }

            $discountPercent = isset($item['discount_percent'])
                ? (float) $item['discount_percent']
                : 0;

            if ($discountPercent < 0 || $discountPercent > 100) {
                throw new \InvalidArgumentException("Row {$row}: Discount must be between 0 and 100.");
            }

            $key = $product->id . '-' . $batch->id;
            if (isset($seen[$key])) {
                throw new \InvalidArgumentException("Row {$row}: Duplicate product/batch combination. Merge quantities into one line.");
            }
            $seen[$key] = true;
        }
    }

    /**
     * @param  array{customer_id: int, date: string, salesperson?: string, remarks?: string, discount_percent: float, gst_percent: float, items: array}  $data
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $this->validateItems($data['items']);

            $lockedBatches = [];
            foreach ($data['items'] as $item) {
                $batch = StockBatch::lockForUpdate()->findOrFail($item['stock_batch_id']);
                if ($batch->quantity < (int) $item['quantity']) {
                    throw new \InvalidArgumentException(
                        "Insufficient stock for batch {$batch->batch_number}. Available: {$batch->quantity}"
                    );
                }
                $lockedBatches[$batch->id] = $batch;
            }

            $calculation = $this->calculate(
                $data['items'],
                (float) $data['discount_percent'],
                (float) $data['gst_percent']
            );

            $invoiceNumber = $this->generateInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $data['customer_id'],
                'salesperson' => $data['salesperson'] ?? 'SELF',
                'remarks' => $data['remarks'] ?? null,
                'date' => $data['date'],
                'subtotal' => $calculation['subtotal'],
                'item_count' => $calculation['item_count'],
                'gross_total' => $calculation['gross_total'],
                'discount_percent' => $data['discount_percent'],
                'discount_value' => $calculation['discount_value'],
                'gst_percent' => $data['gst_percent'],
                'gst_value' => $calculation['gst_value'],
                'total' => $calculation['total'],
                'warranty_notes' => $this->buildWarrantyText(),
            ]);

            foreach ($calculation['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product']->id,
                    'stock_batch_id' => $item['batch']->id,
                    'product_name' => $item['product_name'],
                    'manufacturer_name' => $item['manufacturer_name'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'],
                    'pack_size' => $item['pack_size'],
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                    'discount_percent' => $item['discount_percent'],
                    'discount_value' => $item['discount_value'],
                    'total' => $item['total'],
                ]);

                $lockedBatches[$item['batch']->id]->decrement('quantity', $item['quantity']);
            }

            return $invoice->load('customer', 'items.product', 'items.batch');
        });
    }
}
