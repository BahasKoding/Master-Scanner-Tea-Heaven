<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LattePowderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Latte Powder category
        $lattePowderCategory = 8;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $lattePowderCategory)->delete();

        $lattePowderProducts = [
            // P1 packaging products (100P)
            ['sku' => 'BNL100P', 'packaging' => 'P1', 'name_product' => 'BANANA LATTE'],
            ['sku' => 'BSL100P', 'packaging' => 'P1', 'name_product' => 'BLACK SESAME LATTE'],
            ['sku' => 'CL100P', 'packaging' => 'P1', 'name_product' => 'CHAI LATTE'],
            ['sku' => 'CR100P', 'packaging' => 'P1', 'name_product' => 'CHARCOAL LATTE'],
            ['sku' => 'CCH100P', 'packaging' => 'P1', 'name_product' => 'CREAM CHEESE'],
            ['sku' => 'ERL100P', 'packaging' => 'P1', 'name_product' => 'EARL GREY LATTE'],
            ['sku' => 'GCL100P', 'packaging' => 'P1', 'name_product' => 'GENMAICHA LATTE'],
            ['sku' => 'GIL100P', 'packaging' => 'P1', 'name_product' => 'GINGER LATTE'],
            ['sku' => 'TUL100P', 'packaging' => 'P1', 'name_product' => 'GOLDEN TURMERIC LATTE'],
            ['sku' => 'HL100P', 'packaging' => 'P1', 'name_product' => 'HOJICHA LATTE'],
            ['sku' => 'ML100P', 'packaging' => 'P1', 'name_product' => 'MATCHA LATTE'],
            ['sku' => 'MGL100P', 'packaging' => 'P1', 'name_product' => 'MANGO LATTE'],
            ['sku' => 'PDL100P', 'packaging' => 'P1', 'name_product' => 'PANDAN LATTE'],
            ['sku' => 'RVL100P', 'packaging' => 'P1', 'name_product' => 'RED VELVET LATTE'],
            ['sku' => 'RMT100P', 'packaging' => 'P1', 'name_product' => 'ROYAL MILK TEA'],
            ['sku' => 'SA100P', 'packaging' => 'P1', 'name_product' => 'SAKURA LATTE'],
            ['sku' => 'STL100P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY LATTE'],
            ['sku' => 'TR100P', 'packaging' => 'P1', 'name_product' => 'TARO LATTE'],
            ['sku' => 'TT100P', 'packaging' => 'P1', 'name_product' => 'THAI TEA LATTE'],

            // Bulk packaging (1000P) products
            ['sku' => 'BNL1000P', 'packaging' => '-', 'name_product' => 'BANANA LATTE'],
            ['sku' => 'BSL1000P', 'packaging' => '-', 'name_product' => 'BLACK SESAME LATTE'],
            ['sku' => 'CL1000P', 'packaging' => '-', 'name_product' => 'CHAI LATTE'],
            ['sku' => 'CR1000P', 'packaging' => '-', 'name_product' => 'CHARCOAL LATTE'],
            ['sku' => 'CCH1000F', 'packaging' => '-', 'name_product' => 'CREAM CHEESE'],
            ['sku' => 'ERL1000P', 'packaging' => '-', 'name_product' => 'EARL GREY LATTE'],
            ['sku' => 'GCL1000F', 'packaging' => 'F2', 'name_product' => 'GENMAICHA LATTE'],
            ['sku' => 'GIL1000P', 'packaging' => '-', 'name_product' => 'GINGER LATTE'],
            ['sku' => 'TUL1000P', 'packaging' => '-', 'name_product' => 'GOLDEN TURMERIC LATTE'],
            ['sku' => 'HL1000F', 'packaging' => 'F2', 'name_product' => 'HOJICHA LATTE'],
            ['sku' => 'ML1000F', 'packaging' => 'F2', 'name_product' => 'MATCHA LATTE'],
            ['sku' => 'MGL1000P', 'packaging' => '-', 'name_product' => 'MANGO LATTE'],
            ['sku' => 'PDL1000P', 'packaging' => '-', 'name_product' => 'PANDAN LATTE'],
            ['sku' => 'RVL1000P', 'packaging' => '-', 'name_product' => 'RED VELVET LATTE'],
            ['sku' => 'RMT1000P', 'packaging' => '-', 'name_product' => 'ROYAL MILK TEA'],
            ['sku' => 'SAL1000P', 'packaging' => '-', 'name_product' => 'SAKURA LATTE'],
            ['sku' => 'STL1000P', 'packaging' => '-', 'name_product' => 'STRAWBERRY LATTE'],
            ['sku' => 'TR1000P', 'packaging' => '-', 'name_product' => 'TARO LATTE'],
            ['sku' => 'TT1000P', 'packaging' => '-', 'name_product' => 'THAI TEA LATTE'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($lattePowderProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $lattePowderCategory,
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

        $this->command->info(count($lattePowderProducts) . ' LATTE POWDER products seeded successfully.');
    }
}
