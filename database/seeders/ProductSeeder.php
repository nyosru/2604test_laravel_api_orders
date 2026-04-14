<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Brake Pad Set Front', 'sku' => 'BRK-10001', 'price' => 79.90, 'stock_quantity' => 40, 'category' => 'brakes'],
            ['name' => 'Brake Disc Rear', 'sku' => 'BRK-10002', 'price' => 124.50, 'stock_quantity' => 24, 'category' => 'brakes'],
            ['name' => 'Oil Filter Premium', 'sku' => 'FLT-20001', 'price' => 14.90, 'stock_quantity' => 120, 'category' => 'filters'],
            ['name' => 'Cabin Air Filter', 'sku' => 'FLT-20002', 'price' => 19.50, 'stock_quantity' => 90, 'category' => 'filters'],
            ['name' => 'Shock Absorber Front', 'sku' => 'SUS-30001', 'price' => 159.00, 'stock_quantity' => 18, 'category' => 'suspension'],
            ['name' => 'Control Arm Left', 'sku' => 'SUS-30002', 'price' => 109.90, 'stock_quantity' => 16, 'category' => 'suspension'],
            ['name' => 'Spark Plug Iridium', 'sku' => 'ENG-40001', 'price' => 12.40, 'stock_quantity' => 200, 'category' => 'engine'],
            ['name' => 'Timing Belt Kit', 'sku' => 'ENG-40002', 'price' => 219.90, 'stock_quantity' => 15, 'category' => 'engine'],
            ['name' => 'Oxygen Sensor', 'sku' => 'ELC-50001', 'price' => 89.00, 'stock_quantity' => 28, 'category' => 'electronics'],
            ['name' => 'Ignition Coil', 'sku' => 'ELC-50002', 'price' => 67.80, 'stock_quantity' => 32, 'category' => 'electronics'],
            ['name' => 'Transmission Mount', 'sku' => 'TRN-60001', 'price' => 144.70, 'stock_quantity' => 14, 'category' => 'transmission'],
            ['name' => 'CV Joint Kit', 'sku' => 'TRN-60002', 'price' => 98.30, 'stock_quantity' => 22, 'category' => 'transmission'],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                $product,
            );
        }

        Product::factory(18)->create();
    }
}
