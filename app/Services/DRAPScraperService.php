<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Manufacturer;

class DRAPScraperService
{
    private $baseUrl = 'https://eapp.dra.gov.pk/productView.php';
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36';

    /**
     * Search products by brand name (minimum 3 characters)
     */
    public function searchByBrandName($searchTerm)
    {
        if (strlen($searchTerm) < 3) return [];

        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json, text/html, */*',
                'X-Requested-With' => 'XMLHttpRequest',
            ])->get($this->baseUrl, [
                'search' => $searchTerm,
                '_type' => 'brand name'
            ]);

            Log::info('DRAP API Response', [
                'search_term' => $searchTerm,
                'status' => $response->status(),
                'content_type' => $response->header('content-type'),
                'body_length' => strlen($response->body())
            ]);

            if ($response->successful()) {
                $body = $response->body();
                $contentType = $response->header('content-type');

                // Check if response is JSON or HTML
                if (strpos($contentType, 'application/json') !== false || $this->isJson($body)) {
                    return $this->parseJsonResponse($body, $searchTerm);
                } else {
                    // It's HTML - parse the HTML table
                    return $this->parseHtmlResponse($body, $searchTerm);
                }
            }
        } catch (\Exception $e) {
            Log::error("Search error for $searchTerm: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Check if string is valid JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Parse JSON response (for successful API calls)
     */
    private function parseJsonResponse($responseBody, $searchTerm)
    {
        $products = [];
        $data = json_decode($responseBody, true);

        if ($data && isset($data['results'])) {
            foreach ($data['results'] as $item) {
                $rawName = $item['text'] ?? '';
                if (!empty($rawName)) {
                    $product = [
                        'brand_name' => trim($rawName),
                        'drap_id' => $item['id'] ?? null,
                        'search_term' => $searchTerm
                    ];

                    $this->extractDetails($product);
                    $products[] = $product;
                }
            }
        } else {
            Log::warning('Unexpected JSON structure', [
                'search_term' => $searchTerm,
                'response_preview' => substr($responseBody, 0, 200)
            ]);
        }

        return $products;
    }

    /**
     * Parse HTML response (for when API returns HTML fallback)
     */
    private function parseHtmlResponse($responseBody, $searchTerm)
    {
        $products = [];

        // Load HTML into DOMDocument
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Suppress HTML parsing warnings
        $dom->loadHTML($responseBody);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Try to find product tables or search results
        // Common patterns in DRAP HTML responses

        // Method 1: Look for table rows
        $rows = $xpath->query("//table//tr");

        if ($rows->length > 0) {
            foreach ($rows as $row) {
                $cells = $xpath->query(".//td", $row);
                if ($cells->length >= 2) {
                    // Try to extract product name and ID
                    $productName = trim($cells->item(0)->nodeValue ?? '');
                    $productId = trim($cells->item(1)->nodeValue ?? '');

                    if (!empty($productName) && strlen($productName) > 3) {
                        $products[] = [
                            'brand_name' => $productName,
                            'drap_id' => $productId,
                            'search_term' => $searchTerm
                        ];
                    }
                }
            }
        }

        // Method 2: Look for select2 results format in HTML
        if (empty($products)) {
            // Try to extract JSON embedded in HTML
            if (preg_match('/{"results":\s*\[.*?\]}/s', $responseBody, $matches)) {
                return $this->parseJsonResponse($matches[0], $searchTerm);
            }

            // Method 3: Look for product links or specific patterns
            $productElements = $xpath->query("//a[contains(@href, 'product')] | //div[contains(@class, 'product')] | //span[contains(@class, 'drug-name')]");

            foreach ($productElements as $element) {
                $text = trim($element->nodeValue);
                if (!empty($text) && strlen($text) > 3) {
                    $products[] = [
                        'brand_name' => $text,
                        'drap_id' => null,
                        'search_term' => $searchTerm
                    ];
                }
            }
        }

        Log::info('HTML Parsing Results', [
            'search_term' => $searchTerm,
            'products_found' => count($products),
            'html_length' => strlen($responseBody)
        ]);

        return $products;
    }

    /**
     * Extract Strength and Form from brand name
     */
    private function extractDetails(&$product)
    {
        $name = $product['brand_name'];

        // Extract strength (e.g. 500mg, 10ml)
        if (preg_match('/(\d+(?:\.\d+)?)\s*(mg|g|ml|IU|%|mcg)/i', $name, $matches)) {
            $product['strength'] = $matches[1] . $matches[2];
        }

        // Extract dosage form
        $forms = ['Tablet', 'Capsule', 'Injection', 'Syrup', 'Suspension', 'Cream', 'Ointment', 'Drops', 'Liquid', 'Powder'];
        foreach ($forms as $form) {
            if (stripos($name, $form) !== false) {
                $product['dosage_form'] = $form;
                break;
            }
        }
    }

    /**
     * Save products to database
     */
    public function saveToDatabase($products)
    {
        $stats = [
            'manufacturers_created' => 0,
            'products_created' => 0,
            'products_updated' => 0,
            'errors' => 0
        ];

        // Create default manufacturer
        $manufacturer = Manufacturer::firstOrCreate(
            ['name' => 'DRAP Registered Manufacturer'],
            ['address' => 'Pakistan']
        );

        foreach ($products as $productData) {
            try {
                if (empty($productData['brand_name'])) {
                    continue;
                }

                // Use updateOrCreate to prevent duplicates
                $product = Product::updateOrCreate(
                    ['name' => $productData['brand_name']],
                    [
                        'strength' => $productData['strength'] ?? null,
                        'dosage_form' => $productData['dosage_form'] ?? null,
                        'manufacturer_id' => $manufacturer->id,
                        'registration_no' => $productData['drap_id'] ?? null,
                        'drap_details' => json_encode(['search_term' => $productData['search_term'] ?? null])
                    ]
                );

                if ($product->wasRecentlyCreated) {
                    $stats['products_created']++;
                } else {
                    $stats['products_updated']++;
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Failed to save product: " . ($productData['brand_name'] ?? 'unknown') . " - " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Alternative: Direct API call that always returns JSON by setting proper headers
     */
    public function searchWithJsonOnly($searchTerm)
    {
        if (strlen($searchTerm) < 3) return [];

        try {
            // Try with more specific headers to force JSON response
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false, // Sometimes SSL issues
                'timeout' => 30,
            ])->get($this->baseUrl, [
                'search' => $searchTerm,
                '_type' => 'brand name'
            ]);

            if ($response->successful() && $this->isJson($response->body())) {
                return $this->parseJsonResponse($response->body(), $searchTerm);
            } else {
                // Log the actual response for debugging
                Log::warning('Non-JSON response received', [
                    'search_term' => $searchTerm,
                    'status' => $response->status(),
                    'response_preview' => substr($response->body(), 0, 500),
                    'headers' => $response->headers()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("JSON search error for $searchTerm: " . $e->getMessage());
        }

        return [];
    }
}
