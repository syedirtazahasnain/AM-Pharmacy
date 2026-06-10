<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function index()
    {
        $invoices = Invoice::with('customer')->orderBy('date', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $products = Product::with(['manufacturer', 'batches' => function ($q) {
            $q->where('quantity', '>', 0)
              ->where('expiry_date', '>', now())
              ->orderBy('expiry_date');
        }])->orderBy('name')->get();

        $customers = Customer::orderBy('name')->get();
        $salesmen = config('pharmacy.salesmen', []);
        $pharmacy = config('pharmacy');

        return view('invoices.create', compact('products', 'customers', 'salesmen', 'pharmacy'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            $invoice = $this->invoiceService->create($request->validated());

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Invoice #' . $invoice->invoice_number . ' created successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'items.product.manufacturer', 'items.batch');
        $pharmacy = config('pharmacy');

        return view('invoices.show', compact('invoice', 'pharmacy'));
    }

    public function print(Invoice $invoice, Request $request)
    {
        $invoice->load('customer', 'items.product.manufacturer', 'items.batch');
        $pharmacy = config('pharmacy');
        $format = $request->query('format', 'sales_tax');

        $view = $format === 'warranty' ? 'invoices.pdf-warranty' : 'invoices.pdf';

        $pdf = Pdf::loadView($view, compact('invoice', 'pharmacy'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
