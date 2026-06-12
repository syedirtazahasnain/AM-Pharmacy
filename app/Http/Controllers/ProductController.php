<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Manufacturer;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    // New method for DataTables AJAX
    public function data(Request $request)
    {
        $query = Product::with(['manufacturer', 'batches']);

        return DataTables::of($query)
            ->addColumn('manufacturer', function ($product) {
                return $product->manufacturer?->name ?? '-';
            })
            ->addColumn('pack_size', function ($product) {
                return $product->pack_size ?? '-';
            })
            ->addColumn('mrp', function ($product) {
                return 'Rs. ' . number_format($product->mrp, 2);
            })
            ->addColumn('stock', function ($product) {
                return $product->available_stock ?? 0;
            })
            ->addColumn('actions', function ($product) {
                return '
                    <a href="'.route('products.edit', $product).'" class="text-blue-600 hover:text-blue-900">Edit</a>
                    <form action="'.route('products.destroy', $product).'" method="POST" class="inline">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2"
                                onclick="return confirm(\'Delete this product?\')">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        $manufacturers = Manufacturer::all();
        return view('products.create', compact('manufacturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'mrp' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'batch_number' => 'required|string',
            'expiry_date' => 'required|date',
            'initial_quantity' => 'required|integer|min:1'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'manufacturer_id' => $request->manufacturer_id,
            'pack_size' => $request->pack_size,
            'mrp' => $request->mrp,
            'purchase_price' => $request->purchase_price,
            'category' => $request->category
        ]);

        StockBatch::create([
            'product_id' => $product->id,
            'batch_number' => $request->batch_number,
            'expiry_date' => $request->expiry_date,
            'quantity' => $request->initial_quantity,
            'cost_price' => $request->purchase_price
        ]);

        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }

    public function edit(Product $product)
    {
        $manufacturers = Manufacturer::all();
        return view('products.edit', compact('product', 'manufacturers'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'manufacturer_id' => 'required|exists:manufacturers,id',
            'mrp' => 'required|numeric|min:0',
            'category' => 'required|string'
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
