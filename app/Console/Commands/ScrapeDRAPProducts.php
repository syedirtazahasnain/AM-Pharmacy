<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScrapeDRAPProductsJob;
use App\Models\ScrapingLog;
use Illuminate\Support\Str;

class ScrapeDRAPProducts extends Command
{
    // Updated signature to support prefixes instead of single letters
    protected $signature = 'scrape:drap-products
                            {--queue : Use queue for processing}
                            {--prefix= : Scrape specific 3-char prefix (e.g. PAN)}
                            {--batch-id= : Specific batch ID for resuming}
                            {--all : Scrape all products using 3-char combinations}
                            {--test : Fetch only 100 products quickly}';

    protected $description = 'Scrape products from DRAP (Requires 3 chars min)';

    public function handle()
    {
        if ($this->option('queue')) {
            $this->handleWithQueue();
        } else {
            $this->handleDirect();
        }
    }

    private function handleWithQueue()
    {
        $this->info('🚀 Starting DRAP scraping with QUEUE...');
        $batchId = $this->option('batch-id') ?? Str::uuid();

        // 1. Determine Search Terms (Prefixes)
        if ($this->option('prefix')) {
            $prefixes = [strtoupper($this->option('prefix'))];
        } elseif ($this->option('all')) {
            // This generates AAA to ZZZ (Advanced: Use a subset for efficiency)
            $prefixes = $this->generateSearchPrefixes();
        } else {
            // Default: Common medical prefixes to get high-quality data quickly
            $prefixes = ['PAN', 'AUG', 'AMO', 'CEF', 'CIP', 'DIC', 'MET', 'ROS', 'LIN', 'SUL'];
        }

        // Validate 3 char minimum
        foreach ($prefixes as $p) {
            if (strlen($p) < 3) {
                $this->error("Search term '$p' is too short. DRAP requires 3 characters.");
                return;
            }
        }

        ScrapingLog::create([
            'batch_id' => $batchId,
            'search_type' => 'brand name',
            'status' => 'processing',
            'total_items' => count($prefixes),
            'started_at' => now()
        ]);

        $this->info("📋 Batch ID: {$batchId}");
        $this->info("🔍 Total Prefixes to search: " . count($prefixes));

        foreach ($prefixes as $prefix) {
            ScrapeDRAPProductsJob::dispatch($prefix, 'brand name', $batchId);
            $this->info("✓ Queued prefix: {$prefix}");
        }

        $this->info("✅ All jobs dispatched to queue.");
    }

    private function handleDirect()
    {
        $scraper = app(\App\Services\DRAPScraperService::class);

        if ($this->option('test')) {
            $this->info('🧪 TEST MODE: Fetching initial 100 products...');
            $products = $scraper->getInitialProducts(100);
        } elseif ($this->option('prefix')) {
            $prefix = strtoupper($this->option('prefix'));
            $this->info("🔍 Searching prefix: {$prefix}");
            $products = $scraper->searchByBrandName($prefix);
        } else {
            $this->warn('No specific prefix provided. Fetching 100 sample products...');
            $products = $scraper->getInitialProducts(100);
        }

        $this->info('📊 Found: ' . count($products));
        $this->info('💾 Saving to database...');

        $stats = $scraper->saveToDatabase($products);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Products Created', $stats['products_created']],
                ['Products Updated', $stats['products_updated']],
                ['Errors', $stats['errors']],
            ]
        );

        $this->displaySample($products);
    }

    /**
     * Generates a "Smart Search" list of prefixes.
     * Blindly doing AAA-ZZZ takes 17,576 requests.
     * Using Vowel combinations is much faster.
     */
    private function generateSearchPrefixes()
    {
        $vowels = ['A', 'E', 'I', 'O', 'U'];
        $alphabet = range('A', 'Z');
        $prefixes = [];

        foreach ($alphabet as $first) {
            foreach ($vowels as $second) {
                foreach ($alphabet as $third) {
                    $prefixes[] = $first . $second . $third;
                }
            }
        }
        return $prefixes; // ~3,380 requests (much better than 17k)
    }

    private function displaySample($products)
    {
        $this->newLine();
        $sampleData = array_map(fn($p) => [
            $p['brand_name'] ?? 'N/A',
            $p['drap_id'] ?? 'N/A'
        ], array_slice($products, 0, 10));

        $this->table(['Brand Name', 'DRAP ID'], $sampleData);
    }
}
