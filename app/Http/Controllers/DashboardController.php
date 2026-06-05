<?php

namespace App\Http\Controllers;

use App\Models\StockBatch;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $expiringSoon = StockBatch::with('product')
            ->where('expiry_date', '<=', now()->addMonths(3))
            ->where('expiry_date', '>=', now())
            ->where('quantity', '>', 0)
            ->get();

        $lowStock = StockBatch::with('product')
            ->where('quantity', '<', 10)
            ->where('quantity', '>', 0)
            ->get();

        $todaySales = Invoice::whereDate('date', today())->sum('total');
        $monthlySales = Invoice::whereMonth('date', now()->month)->sum('total');
        $totalProducts = \App\Models\Product::count();
        $totalCustomers = \App\Models\Customer::count();

        return view('dashboard', compact('expiringSoon', 'lowStock', 'todaySales', 'monthlySales', 'totalProducts', 'totalCustomers'));
    }
}
