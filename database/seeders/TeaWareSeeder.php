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
        $teaWareCategory = 11;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $teaWareCategory)->delete();

        $teaWareProducts = [
            ['sku' => 'TA00', 'packaging' => '-', 'name_product' => 'TEA BAG', 'label' => 9],
            ['sku' => 'TA01', 'packaging' => '-', 'name_product' => 'INFUSER BOTTLE 420 ML SILVER', 'label' => 9],
            ['sku' => 'TA02', 'packaging' => '-', 'name_product' => 'STRAINER STAINLESS', 'label' => 9],
            ['sku' => 'TA03', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 600 ML', 'label' => 9],
            ['sku' => 'TA04', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 800 ML', 'label' => 9],
            ['sku' => 'TA05', 'packaging' => '-', 'name_product' => 'IRON TEAPOT 1,2 LITER', 'label' => 9],
            ['sku' => 'TA06', 'packaging' => '-', 'name_product' => 'MATCHA WHISK ELECTRIC', 'label' => 9],
            ['sku' => 'TA07', 'packaging' => '-', 'name_product' => 'STAINLESS STRAW', 'label' => 9],
            ['sku' => 'TA08', 'packaging' => '-', 'name_product' => 'MATCHA WHISK 100 PRONGS', 'label' => 9],
            ['sku' => 'TA09', 'packaging' => '-', 'name_product' => 'TEKO PITCHER 1 L', 'label' => 9],
            ['sku' => 'TA10', 'packaging' => '-', 'name_product' => 'TOPLES KACA 280 ML', 'label' => 9],
            ['sku' => 'TA11', 'packaging' => '-', 'name_product' => 'GELAS 3IN1 320 ML', 'label' => 9],
            ['sku' => 'TA12', 'packaging' => '-', 'name_product' => 'MATCHA SPOON CHASAKU', 'label' => 9],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($teaWareProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $teaWareCategory,
                'sku' => $product['sku'],
                'packaging' => $product['packaging'],
                'name_product' => $product['name_product'],
                'label' => $product['label'],
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
