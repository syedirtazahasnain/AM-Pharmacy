<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingLog extends Model
{
    protected $table = 'scraping_logs';

    protected $fillable = [
        'batch_id',
        'search_type',
        'status',
        'total_items',
        'processed_items',
        'progress_data',
        'message',
        'error_message',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'progress_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
}
