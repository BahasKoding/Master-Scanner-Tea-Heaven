<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeaWareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Tea Ware category
        $teaWareCategory = CategoryProduct::where('name', 'TEA WARE')->first();

        if (!$teaWareCategory) {
            $this->command->error('TEA WARE category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $teaWareCategory->id)->delete();

        $teaWareProducts = [
            ['sku' => 'TA00', 'packaging' => '-', 'name_product' => 'TEA BAG'],
            ['sku' => 'TA01', 'packaging' => '-', 'name_product' => 'INFUSER BOTTLE 420 ML SILVER'],
            ['sku' => 'TA02', 'packaging' => '-', 'name_product' => 'STRAINER STAINLESS'],
            ['sku' => 'TA03', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 600 ML'],
            ['sku' => 'TA04', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 800 ML'],
            ['sku' => 'TA05', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 1,2 LITER'],
            ['sku' => 'TA06', 'packaging' => '-', 'name_product' => 'MATCHA WHISK ELECTRIC'],
            ['sku' => 'TA07', 'packaging' => '-', 'name_product' => 'STAINLESS STRAW'],
            ['sku' => 'TA08', 'packaging' => '-', 'name_product' => 'MATCHA WHISK 100 PRONGS'],
            ['sku' => 'TA09', 'packaging' => '-', 'name_product' => 'TEKO PITCHER 1 L'],
            ['sku' => 'TA10', 'packaging' => '-', 'name_product' => 'TOPLES KACA 280 ML'],
            ['sku' => 'TA11', 'packaging' => '-', 'name_product' => 'GELAS 3IN1 320 ML'],
            ['sku' => 'TA12', 'packaging' => '-', 'name_product' => 'MATCHA SPOON CHASAKU'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($teaWareProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $teaWareCategory->id,
                'sku' => $product['sku'],
                'packaging' => $product['packaging'],
                'name_product' => $product['name_product'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($productsToInsert, 100) as $chunk) {
            DB::table('products')->insert($chunk);
        }

        $this->command->info(count($teaWareProducts) . ' TEA WARE products seeded successfully.');
    }
}
