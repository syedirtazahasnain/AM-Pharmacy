<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'stock_batch_id',
        'product_name',
        'manufacturer_name',
        'batch_number',
        'expiry_date',
        'pack_size',
        'quantity',
        'rate',
        'amount',
        'discount_percent',
        'discount_value',
        'total',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(StockBatch::class, 'stock_batch_id');
    }

    public function getDisplayProductNameAttribute(): string
    {
        return $this->product_name ?? $this->product?->name ?? 'N/A';
    }

    public function getDisplayManufacturerAttribute(): string
    {
        return $this->manufacturer_name ?? $this->product?->manufacturer?->name ?? 'N/A';
    }

    public function getDisplayBatchNumberAttribute(): string
    {
        return $this->batch_number ?? $this->batch?->batch_number ?? 'N/A';
    }

    public function getDisplayExpiryAttribute(): ?\Carbon\Carbon
    {
        return $this->expiry_date ?? $this->batch?->expiry_date;
    }

    public function getDisplayPackSizeAttribute(): string
    {
        return $this->pack_size ?? $this->product?->pack_size ?? '-';
    }
}
