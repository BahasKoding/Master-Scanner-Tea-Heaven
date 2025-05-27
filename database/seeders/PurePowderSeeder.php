<?php

namespace Database\Seeders;

use App\Models\Product;
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
        $purePowderCategory = 6;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $purePowderCategory)->delete();
        $purePowderProducts = [
            // P1 packaging products - Label 1
            ['sku' => 'MA100P', 'packaging' => 'P1', 'name_product' => 'MATCHA A', 'label' => 1],
            ['sku' => 'MA100PC', 'packaging' => 'PC1', 'name_product' => 'MATCHA A', 'label' => 1],
            ['sku' => 'MB100P', 'packaging' => 'P1', 'name_product' => 'MATCHA B', 'label' => 1],
            ['sku' => 'MC100P', 'packaging' => 'P1', 'name_product' => 'PURE UJI CEREMONIAL MATCHA', 'label' => 1],
            ['sku' => 'MU100P', 'packaging' => 'P1', 'name_product' => 'PURE UJI CULINARY MATCHA', 'label' => 1],
            ['sku' => 'PC100P', 'packaging' => 'P1', 'name_product' => 'PURE CHAI POWDER', 'label' => 1],
            ['sku' => 'PER100P', 'packaging' => 'P1', 'name_product' => 'PURE EARL GREY POWDER', 'label' => 1],
            ['sku' => 'HP100P', 'packaging' => 'P1', 'name_product' => 'PURE HOJICHA POWDER', 'label' => 1],
            ['sku' => 'PTR100P', 'packaging' => 'P1', 'name_product' => 'PURE TARO POWDER', 'label' => 1],
            ['sku' => 'PTT100P', 'packaging' => 'P1', 'name_product' => 'PURE THAI TEA', 'label' => 1],
            ['sku' => 'SW100P', 'packaging' => 'P1', 'name_product' => 'SWISS CHOCOLATE', 'label' => 1],

            // F0 & F1 packaging products (250F) - Label 2
            ['sku' => 'MA250F', 'packaging' => 'F0', 'name_product' => 'MATCHA A', 'label' => 2],
            ['sku' => 'MB250F', 'packaging' => 'F0', 'name_product' => 'MATCHA B', 'label' => 2],
            ['sku' => 'MC250F', 'packaging' => 'F1', 'name_product' => 'PURE UJI CEREMONIAL MATCHA', 'label' => 2],
            ['sku' => 'MU250F', 'packaging' => 'F1', 'name_product' => 'PURE UJI CULINARY MATCHA', 'label' => 2],
            ['sku' => 'PTH250F', 'packaging' => 'F1', 'name_product' => 'PURE THAI TEA', 'label' => 2],

            // Bulk products (1000F) - Label 4
            ['sku' => 'MA1000F', 'packaging' => '-', 'name_product' => 'MATCHA A', 'label' => 4],
            ['sku' => 'MB1000F', 'packaging' => '-', 'name_product' => 'MATCHA B', 'label' => 4],
            ['sku' => 'MC1000F', 'packaging' => '-', 'name_product' => 'PURE UJI CEREMONIAL MATCHA', 'label' => 4],
            ['sku' => 'MU1000F', 'packaging' => '-', 'name_product' => 'PURE UJI CULINARY MATCHA', 'label' => 4],
            ['sku' => 'PC1000F', 'packaging' => '-', 'name_product' => 'PURE CHAI POWDER', 'label' => 4],
            ['sku' => 'PER1000P', 'packaging' => '-', 'name_product' => 'PURE EARL GREY POWDER', 'label' => 4],
            ['sku' => 'HP1000F', 'packaging' => '-', 'name_product' => 'PURE HOJICHA POWDER', 'label' => 4],
            ['sku' => 'PTR1000P', 'packaging' => '-', 'name_product' => 'PURE TARO POWDER', 'label' => 4],
            ['sku' => 'PTT1000F', 'packaging' => '-', 'name_product' => 'PURE THAI TEA', 'label' => 4],
            ['sku' => 'SW1000F', 'packaging' => 'F3', 'name_product' => 'SWISS CHOCOLATE', 'label' => 4],
            ['sku' => 'HPS1000P', 'packaging' => '-', 'name_product' => 'SWEET HOJICHA', 'label' => 4],
            ['sku' => 'MGIL1000P', 'packaging' => '-', 'name_product' => 'SWEET MATCHA GINGER', 'label' => 4],
            ['sku' => 'MS1000P', 'packaging' => '-', 'name_product' => 'SWEET UJI MATCHA', 'label' => 4],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($purePowderProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $purePowderCategory,
                'sku' => $product['sku'],
                'packaging' => $product['packaging'],
                'name_product' => $product['name_product'],
                'label' => $product['label'] ?? null,
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
