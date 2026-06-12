<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestDRAPScraper extends Command
{
    protected $signature = 'test:drap {search=panadol} {type=brand name}';
    protected $description = 'Test DRAP scraper to debug issues';

    public function handle()
    {
        $search = $this->argument('search');
        $type = $this->argument('type');

        $this->info("Testing DRAP API with search: '{$search}', type: '{$type}'");
        $this->newLine();

        $url = 'https://eapp.dra.gov.pk/productView.php';
        $params = [
            'search' => $search,
            '_type' => $type
        ];

        $this->info("URL: {$url}");
        $this->info("Params: " . json_encode($params));
        $this->newLine();

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9',
            ])->get($url, $params);

            $this->info("Response Status: " . $response->status());
            $this->newLine();

            if ($response->successful()) {
                $html = $response->body();

                // Save HTML for inspection
                $filename = storage_path("app/drap_test_{$search}.html");
                file_put_contents($filename, $html);
                $this->info("HTML saved to: {$filename}");

                // Check if any product data exists
                $hasTable = strpos($html, '<table') !== false;
                $hasProduct = strpos($html, 'product') !== false || strpos($html, 'drug') !== false;

                $this->info("Contains table: " . ($hasTable ? 'Yes' : 'No'));
                $this->info("Contains product/drug text: " . ($hasProduct ? 'Yes' : 'No'));
                $this->newLine();

                // Show first 1000 characters of HTML
                $this->info("HTML Preview (first 1000 chars):");
                $this->line(substr($html, 0, 1000));
                $this->newLine();

                // Check if there's a search result message
                if (preg_match('/(\d+)\s+records? found/i', $html, $matches)) {
                    $this->info("Found {$matches[1]} records in search result");
                }

                if (preg_match('/no\s+result/i', $html)) {
                    $this->warn("No results found message detected");
                }
            } else {
                $this->error("Failed to fetch: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
