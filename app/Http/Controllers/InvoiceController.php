<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockBatch;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')->orderBy('date', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::with(['batches' => function($q) {
            $q->where('quantity', '>', 0)->where('expiry_date', '>', now());
        }])->get();

        $customers = Customer::all();

        return view('invoices.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.stock_batch_id' => 'required|exists:stock_batches,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $invoiceItems = [];

            foreach($request->items as $item) {
                $product = Product::find($item['product_id']);
                $batch = StockBatch::find($item['stock_batch_id']);

                // Check stock availability
                if ($batch->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$batch->quantity}");
                }

                $lineTotal = $product->mrp * $item['quantity'];
                $subtotal += $lineTotal;

                $invoiceItems[] = [
                    'product' => $product,
                    'batch' => $batch,
                    'quantity' => $item['quantity'],
                    'rate' => $product->mrp,
                    'line_total' => $lineTotal
                ];
            }

            $discountValue = $subtotal * ($request->discount_percent / 100);
            $afterDiscount = $subtotal - $discountValue;
            $gstValue = $afterDiscount * (18 / 100);
            $total = $afterDiscount + $gstValue;

            // Generate invoice number
            $lastInvoice = Invoice::latest()->first();
            $invoiceNumber = $lastInvoice ? str_pad(intval($lastInvoice->invoice_number) + 1, 4, '0', STR_PAD_LEFT) : '1001';

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $request->customer_id,
                'date' => $request->date,
                'subtotal' => $subtotal,
                'discount_percent' => $request->discount_percent,
                'discount_value' => $discountValue,
                'gst_percent' => 18,
                'gst_value' => $gstValue,
                'total' => $total,
                'warranty_notes' => "I Arslan Yaseen Person Resident in Pakistan carrying business at A.M Pharmacy Shop # 44 A/4, Lalazar Tulsa Road Rawalpindi of the distributor/Wholesaler/authorized agent of the drugs on this invoice. Do Hereby give this warranty that the drugs invoice as sold by us do not contravene in any way the provision of section 23 of the Drug Act 1976."
            ]);

            // Create invoice items and reduce stock
            foreach($invoiceItems as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product']->id,
                    'stock_batch_id' => $item['batch']->id,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'discount_percent' => $request->discount_percent,
                    'total' => $item['line_total'] * (1 - $request->discount_percent / 100)
                ]);

                // Reduce stock
                $item['batch']->decrement('quantity', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully');

        } catch(\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product', 'items.batch');
        return view('invoices.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product.manufacturer', 'items.batch');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
