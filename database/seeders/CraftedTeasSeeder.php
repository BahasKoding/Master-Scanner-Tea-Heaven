<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CraftedTeasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Crafted Teas category
        $craftedTeasCategory = 9;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $craftedTeasCategory)->delete();

        $craftedTeasProducts = [
            ['sku' => 'BF01Z', 'packaging' => '-', 'name_product' => 'BLOOMING TEA', 'label' => 7],
            ['sku' => 'PB01Z', 'packaging' => '-', 'name_product' => 'PU-ERH TEA BALL', 'label' => 7],
            ['sku' => 'CHMC', 'packaging' => '-', 'name_product' => 'CHRYSANTHEMUM MINI CAKE', 'label' => 7],
            ['sku' => 'ORMC', 'packaging' => '-', 'name_product' => 'ORANGE PEEL MINI CAKE', 'label' => 7],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($craftedTeasProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $craftedTeasCategory,
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

        $this->command->info(count($craftedTeasProducts) . ' CRAFTED TEAS products seeded successfully.');
    }
}
