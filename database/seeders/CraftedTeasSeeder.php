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
        $craftedTeasCategory = CategoryProduct::where('name', 'CRAFTED TEAS')->first();

        if (!$craftedTeasCategory) {
            $this->command->error('CRAFTED TEAS category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $craftedTeasCategory->id)->delete();

        $craftedTeasProducts = [
            ['sku' => 'BF01Z', 'packaging' => '-', 'name_product' => 'BLOOMING TEA'],
            ['sku' => 'PB01Z', 'packaging' => '-', 'name_product' => 'PU-ERH TEA BALL'],
            ['sku' => 'CHMC', 'packaging' => '-', 'name_product' => 'CHRYSANTHEMUM MINI CAKE'],
            ['sku' => 'ORMC', 'packaging' => '-', 'name_product' => 'ORANGE PEEL MINI CAKE'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($craftedTeasProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $craftedTeasCategory->id,
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

        $this->command->info(count($craftedTeasProducts) . ' CRAFTED TEAS products seeded successfully.');
    }
}
