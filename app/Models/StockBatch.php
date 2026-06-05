<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBatch extends Model
{
    protected $table = 'stock_batches';

    protected $fillable = ['product_id', 'batch_number', 'expiry_date', 'quantity', 'cost_price'];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    public function isExpiringSoon($months = 3)
    {
        return !$this->isExpired() && $this->expiry_date <= now()->addMonths($months);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'stock_batch_id');
    }
}
