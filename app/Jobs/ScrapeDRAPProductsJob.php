<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\DRAPScraperService;
use App\Models\ScrapingLog;
use Illuminate\Support\Facades\Log;

class ScrapeDRAPProductsJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $searchTerm;
    protected $searchType;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct($searchTerm = null, $searchType = 'brand name', $batchId = null)
    {
        $this->searchTerm = $searchTerm;
        $this->searchType = $searchType;
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(DRAPScraperService $scraper)
    {
        $startTime = microtime(true);

        try {
            // Log start of scraping
            $this->logScrapingStart();

            // If no search term provided, scrape all letters A-Z
            if (!$this->searchTerm) {
                $alphabets = range('A', 'Z');
                $totalProducts = 0;
                $totalStats = [
                    'manufacturers_created' => 0,
                    'products_created' => 0,
                    'products_updated' => 0,
                    'errors' => 0
                ];

                foreach ($alphabets as $letter) {
                    // Dispatch individual job for each letter
                    ScrapeDRAPProductsJob::dispatch($letter, 'brand name', $this->batchId);

                    // Small delay to avoid overwhelming the queue
                    usleep(500000); // 0.5 seconds
                }

                $this->updateBatchLog('completed', 'All letter jobs dispatched');
                return;
            }

            // Perform the actual scraping for a single term
            if ($this->searchType === 'brand name') {
                $products = $scraper->searchByBrandName($this->searchTerm);
            } else {
                $products = $scraper->searchByGenericName($this->searchTerm);
            }

            // Save products to database
            $stats = $scraper->saveToDatabase($products);

            $executionTime = microtime(true) - $startTime;

            // Log completion
            $this->logScrapingComplete($products, $stats, $executionTime);

            // Update batch log if exists
            if ($this->batchId) {
                $this->updateBatchProgress($stats);
            }

        } catch (\Exception $e) {
            $this->logScrapingError($e);
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception)
    {
        Log::error('DRAP Scraping Job Failed', [
            'search_term' => $this->searchTerm,
            'search_type' => $this->searchType,
            'error' => $exception->getMessage()
        ]);

        if ($this->batchId) {
            ScrapingLog::where('batch_id', $this->batchId)
                ->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
        }
    }

    private function logScrapingStart()
    {
        Log::info('DRAP Scraping Job Started', [
            'search_term' => $this->searchTerm,
            'search_type' => $this->searchType,
            'batch_id' => $this->batchId
        ]);
    }

    private function logScrapingComplete($products, $stats, $executionTime)
    {
        Log::info('DRAP Scraping Job Completed', [
            'search_term' => $this->searchTerm,
            'products_found' => count($products),
            'manufacturers_created' => $stats['manufacturers_created'],
            'products_created' => $stats['products_created'],
            'products_updated' => $stats['products_updated'],
            'execution_time' => round($executionTime, 2) . ' seconds'
        ]);
    }

    private function logScrapingError($e)
    {
        Log::error('DRAP Scraping Job Error', [
            'search_term' => $this->searchTerm,
            'error' => $e->getMessage()
        ]);
    }

    private function updateBatchProgress($stats)
    {
        $log = ScrapingLog::where('batch_id', $this->batchId)->first();
        if ($log) {
            $progress = json_decode($log->progress_data ?? '{}', true);
            $progress[$this->searchTerm] = $stats;
            $log->update([
                'progress_data' => json_encode($progress),
                'processed_items' => ($log->processed_items ?? 0) + count($progress)
            ]);
        }
    }

    private function updateBatchLog($status, $message)
    {
        if ($this->batchId) {
            ScrapingLog::where('batch_id', $this->batchId)->update([
                'status' => $status,
                'message' => $message,
                'completed_at' => now()
            ]);
        }
    }
}
