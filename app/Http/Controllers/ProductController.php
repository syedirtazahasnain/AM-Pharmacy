<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Manufacturer;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('manufacturer', 'batches')->get();
        return view('products.index', compact('products'));
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
