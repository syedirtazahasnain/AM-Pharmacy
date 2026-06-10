<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'salesperson',
        'remarks',
        'date',
        'subtotal',
        'item_count',
        'gross_total',
        'discount_percent',
        'discount_value',
        'gst_percent',
        'gst_value',
        'total',
        'warranty_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'subtotal' => 'decimal:2',
        'gross_total' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_value' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
