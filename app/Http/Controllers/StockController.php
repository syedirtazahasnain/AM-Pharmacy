<?php

namespace App\Http\Controllers;

use App\Models\StockBatch;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $batches = StockBatch::with('product.manufacturer')
            ->orderBy('expiry_date')
            ->get();

        $expiredBatches = $batches->filter(fn($b) => $b->isExpired());
        $expiringBatches = $batches->filter(fn($b) => $b->isExpiringSoon());
        $availableBatches = $batches->filter(fn($b) => !$b->isExpired() && $b->quantity > 0);

        return view('stock.index', compact('batches', 'expiredBatches', 'expiringBatches', 'availableBatches'));
    }
}
