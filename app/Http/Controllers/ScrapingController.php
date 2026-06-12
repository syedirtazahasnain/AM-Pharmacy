<?php

namespace App\Http\Controllers;

use App\Jobs\ScrapeDRAPProductsJob;
use App\Models\ScrapingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScrapingController extends Controller
{
    public function index()
    {
        $logs = ScrapingLog::orderBy('created_at', 'desc')->paginate(20);
        return view('scraping.index', compact('logs'));
    }

    public function start(Request $request)
    {
        $batchId = Str::uuid();

        $letters = $request->get('letters', range('A', 'Z'));

        $log = ScrapingLog::create([
            'batch_id' => $batchId,
            'search_type' => 'brand name',
            'status' => 'processing',
            'total_items' => count($letters),
            'started_at' => now()
        ]);

        foreach ($letters as $letter) {
            ScrapeDRAPProductsJob::dispatch($letter, 'brand name', $batchId);
        }

        return response()->json([
            'message' => 'Scraping started',
            'batch_id' => $batchId,
            'status_url' => route('scraping.status', $batchId)
        ]);
    }

    public function status($batchId)
    {
        $log = ScrapingLog::where('batch_id', $batchId)->firstOrFail();

        return response()->json([
            'batch_id' => $log->batch_id,
            'status' => $log->status,
            'total_items' => $log->total_items,
            'processed_items' => $log->processed_items,
            'progress_data' => $log->progress_data,
            'created_at' => $log->created_at,
            'completed_at' => $log->completed_at
        ]);
    }

    public function retry($batchId)
    {
        $log = ScrapingLog::where('batch_id', $batchId)->first();

        if (!$log) {
            return response()->json(['error' => 'Batch not found'], 404);
        }

        // Retry failed jobs
        $failedItems = [];
        $progress = json_decode($log->progress_data ?? '{}', true);

        $allLetters = range('A', 'Z');
        foreach ($allLetters as $letter) {
            if (!isset($progress[$letter]) || $progress[$letter]['errors'] > 0) {
                $failedItems[] = $letter;
                ScrapeDRAPProductsJob::dispatch($letter, 'brand name', $batchId);
            }
        }

        return response()->json([
            'message' => 'Retry started',
            'retried_items' => $failedItems
        ]);
    }
}
