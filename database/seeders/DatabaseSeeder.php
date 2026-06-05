<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockBatch;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@pharmacy.com',
            'password' => bcrypt('password')
        ]);

        // Create manufacturers
        $manufacturers = [
            ['name' => 'PIRAMAL CRITICAL CARE', 'address' => 'Mumbai, India'],
            ['name' => 'B BRAUN SURGICAL KARACHI', 'address' => 'Karachi, Pakistan'],
            ['name' => 'CHANGZHOU TONGDA MEDICAL', 'address' => 'China'],
            ['name' => 'MEDIPAK LIMITED PAKISTAN', 'address' => 'Islamabad, Pakistan'],
            ['name' => 'GLAXO SMITHKLINE PAKISTAN', 'address' => 'Karachi, Pakistan'],
            ['name' => 'HIGHNOON LABORATORIES', 'address' => 'Lahore, Pakistan'],
            ['name' => 'THE SEARLE COMPANY LTD', 'address' => 'Karachi, Pakistan'],
        ];

        foreach ($manufacturers as $man) {
            Manufacturer::create($man);
        }

        // Create products with batches
        $products = [
            [
                'name' => 'RESTAINE (ISOFURANE) 100ML',
                'manufacturer_id' => 1,
                'pack_size' => '1*1',
                'mrp' => 3300,
                'purchase_price' => 2800,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => '149M22A', 'expiry_date' => '2027-11-30', 'quantity' => 100, 'cost_price' => 2800],
                    ['batch_number' => '150M23B', 'expiry_date' => '2028-01-15', 'quantity' => 50, 'cost_price' => 2750],
                ]
            ],
            [
                'name' => 'GELOFUSINE 500ML INFUSION',
                'manufacturer_id' => 2,
                'pack_size' => '1*1',
                'mrp' => 466.6,
                'purchase_price' => 400,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => '21405371', 'expiry_date' => '2024-09-30', 'quantity' => 50, 'cost_price' => 400],
                    ['batch_number' => '21405372', 'expiry_date' => '2025-03-15', 'quantity' => 30, 'cost_price' => 395],
                ]
            ],
            [
                'name' => 'TOCS SYRINGE SHIFA',
                'manufacturer_id' => 3,
                'pack_size' => '1X100',
                'mrp' => 45,
                'purchase_price' => 35,
                'category' => 'Surgical',
                'batches' => [
                    ['batch_number' => '20221218', 'expiry_date' => '2027-12-17', 'quantity' => 5000, 'cost_price' => 35],
                ]
            ],
            [
                'name' => 'MANNITOL 20% 500ML',
                'manufacturer_id' => 4,
                'pack_size' => '1X1',
                'mrp' => 204.9,
                'purchase_price' => 180,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => '212789', 'expiry_date' => '2026-12-30', 'quantity' => 200, 'cost_price' => 180],
                ]
            ],
            [
                'name' => 'SPASFON INJECTION',
                'manufacturer_id' => 5,
                'pack_size' => '1*1',
                'mrp' => 150,
                'purchase_price' => 120,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => '0032', 'expiry_date' => '2026-08-30', 'quantity' => 3000, 'cost_price' => 120],
                ]
            ],
            [
                'name' => 'TIOVAR 18MCG CAP',
                'manufacturer_id' => 6,
                'pack_size' => '15 capsules',
                'mrp' => 26.8,
                'purchase_price' => 22,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => 'HUY8', 'expiry_date' => '2028-12-30', 'quantity' => 500, 'cost_price' => 22],
                ]
            ],
            [
                'name' => 'NUBEROL FORTE TAB',
                'manufacturer_id' => 7,
                'pack_size' => '1*10',
                'mrp' => 15,
                'purchase_price' => 12,
                'category' => 'Medicine',
                'batches' => [
                    ['batch_number' => 'DR', 'expiry_date' => '2028-12-30', 'quantity' => 1000, 'cost_price' => 12],
                ]
            ],
        ];

        foreach ($products as $productData) {
            $batches = $productData['batches'];
            unset($productData['batches']);

            $product = Product::create($productData);

            foreach ($batches as $batch) {
                $batch['product_id'] = $product->id;
                StockBatch::create($batch);
            }
        }

        // Create customers
        $customers = [
            ['customer_number' => '582', 'name' => 'THQ HOSPITAL KALLAR SYEDAN', 'district' => 'RAWALPINDI', 'phone' => '051-1234567', 'address' => 'Kallar Syedan, Rawalpindi'],
            ['customer_number' => '636', 'name' => 'THQ HOSPITAL TAXILA', 'district' => 'RAWALPINDI', 'phone' => '051-9315474', 'address' => 'Taxila, Rawalpindi'],
            ['customer_number' => '637', 'name' => 'DISTRICT HEADQUARTERS HOSPITAL', 'district' => 'RAWALPINDI', 'phone' => '051-9271550', 'address' => 'Rawalpindi'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
