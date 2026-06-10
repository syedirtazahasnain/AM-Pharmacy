<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name',
    'generic_name',
    'strength',
    'dosage_form',
    'registration_no',
    'manufacturer_id',
    'pack_size',
    'mrp',
    'purchase_price',
    'category',
    'drap_details'];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function batches()
    {
        return $this->hasMany(StockBatch::class);
    }

    public function getAvailableStockAttribute()
    {
        return $this->batches()
            ->where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->sum('quantity');
    }

    public function getActiveBatchesAttribute()
    {
        return $this->batches()
            ->where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();
    }
}
