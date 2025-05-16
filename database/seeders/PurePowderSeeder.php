<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurePowderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Pure Powder category
        $purePowderCategory = CategoryProduct::where('name', 'PURE POWDER')->first();

        if (!$purePowderCategory) {
            $this->command->error('PURE POWDER category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $purePowderCategory->id)->delete();

        $purePowderProducts = [
            // P1 packaging products
            ['sku' => 'MA100P', 'packaging' => 'P1', 'name_product' => 'MATCHA A'],
            ['sku' => 'MA100PC', 'packaging' => 'PC1', 'name_product' => 'MATCHA A'],
            ['sku' => 'MB100P', 'packaging' => 'P1', 'name_product' => 'MATCHA B'],
            ['sku' => 'MC100P', 'packaging' => 'P1', 'name_product' => 'PURE UJI CEREMONIAL MATCHA'],
            ['sku' => 'MU100P', 'packaging' => 'P1', 'name_product' => 'PURE UJI CULINARY MATCHA'],
            ['sku' => 'PC100P', 'packaging' => 'P1', 'name_product' => 'PURE CHAI POWDER'],
            ['sku' => 'PER100P', 'packaging' => 'P1', 'name_product' => 'PURE EARL GREY POWDER'],
            ['sku' => 'HP100P', 'packaging' => 'P1', 'name_product' => 'PURE HOJICHA POWDER'],
            ['sku' => 'PTR100P', 'packaging' => 'P1', 'name_product' => 'PURE TARO POWDER'],
            ['sku' => 'PTT100P', 'packaging' => 'P1', 'name_product' => 'PURE THAI TEA'],
            ['sku' => 'SW100P', 'packaging' => 'P1', 'name_product' => 'SWISS CHOCOLATE'],

            // F0 & F1 packaging products (250F)
            ['sku' => 'MA250F', 'packaging' => 'F0', 'name_product' => 'MATCHA A'],
            ['sku' => 'MB250F', 'packaging' => 'F0', 'name_product' => 'MATCHA B'],
            ['sku' => 'MC250F', 'packaging' => 'F1', 'name_product' => 'PURE UJI CEREMONIAL MATCHA'],
            ['sku' => 'MU250F', 'packaging' => 'F1', 'name_product' => 'PURE UJI CULINARY MATCHA'],
            ['sku' => 'PTH250F', 'packaging' => 'F1', 'name_product' => 'PURE THAI TEA'],

            // F0 & F1 packaging products (250FX2)
            ['sku' => 'MA250FX2', 'packaging' => 'F0', 'name_product' => 'MATCHA A'],
            ['sku' => 'MB250FX2', 'packaging' => 'F0', 'name_product' => 'MATCHA B'],
            ['sku' => 'MC250FX2', 'packaging' => 'F1', 'name_product' => 'PURE UJI CEREMONIAL MATCHA'],
            ['sku' => 'MU250FX2', 'packaging' => 'F1', 'name_product' => 'PURE UJI CULINARY MATCHA'],
            ['sku' => 'PTH250FX2', 'packaging' => 'F1', 'name_product' => 'PURE THAI TEA'],

            // Bulk products (1000F)
            ['sku' => 'MA1000F', 'packaging' => '-', 'name_product' => 'MATCHA A'],
            ['sku' => 'MB1000F', 'packaging' => '-', 'name_product' => 'MATCHA B'],
            ['sku' => 'MC1000F', 'packaging' => '-', 'name_product' => 'PURE UJI CEREMONIAL MATCHA'],
            ['sku' => 'MU1000F', 'packaging' => '-', 'name_product' => 'PURE UJI CULINARY MATCHA'],
            ['sku' => 'PC1000F', 'packaging' => '-', 'name_product' => 'PURE CHAI POWDER'],
            ['sku' => 'PER1000P', 'packaging' => '-', 'name_product' => 'PURE EARL GREY POWDER'],
            ['sku' => 'HP1000F', 'packaging' => '-', 'name_product' => 'PURE HOJICHA POWDER'],
            ['sku' => 'PTR1000P', 'packaging' => '-', 'name_product' => 'PURE TARO POWDER'],
            ['sku' => 'PTT1000F', 'packaging' => '-', 'name_product' => 'PURE THAI TEA'],
            ['sku' => 'SW1000F', 'packaging' => 'F3', 'name_product' => 'SWISS CHOCOLATE'],
            ['sku' => 'HPS1000P', 'packaging' => '-', 'name_product' => 'SWEET HOJICHA'],
            ['sku' => 'MGIL1000P', 'packaging' => '-', 'name_product' => 'SWEET MATCHA GINGER'],
            ['sku' => 'MS1000P', 'packaging' => '-', 'name_product' => 'SWEET UJI MATCHA'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($purePowderProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $purePowderCategory->id,
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

        $this->command->info(count($purePowderProducts) . ' PURE POWDER products seeded successfully.');
    }
}
