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
            // P1 packaging products (100P) - Label 1
            ['sku' => 'BNL100P', 'packaging' => 'P1', 'name_product' => 'BANANA LATTE', 'label' => 1],
            ['sku' => 'BSL100P', 'packaging' => 'P1', 'name_product' => 'BLACK SESAME LATTE', 'label' => 1],
            ['sku' => 'CL100P', 'packaging' => 'P1', 'name_product' => 'CHAI LATTE', 'label' => 1],
            ['sku' => 'CR100P', 'packaging' => 'P1', 'name_product' => 'CHARCOAL LATTE', 'label' => 1],
            ['sku' => 'CCH100P', 'packaging' => 'P1', 'name_product' => 'CREAM CHEESE', 'label' => 1],
            ['sku' => 'ERL100P', 'packaging' => 'P1', 'name_product' => 'EARL GREY LATTE', 'label' => 1],
            ['sku' => 'GCL100P', 'packaging' => 'P1', 'name_product' => 'GENMAICHA LATTE', 'label' => 1],
            ['sku' => 'GIL100P', 'packaging' => 'P1', 'name_product' => 'GINGER LATTE', 'label' => 1],
            ['sku' => 'TUL100P', 'packaging' => 'P1', 'name_product' => 'GOLDEN TURMERIC LATTE', 'label' => 1],
            ['sku' => 'HL100P', 'packaging' => 'P1', 'name_product' => 'HOJICHA LATTE', 'label' => 1],
            ['sku' => 'ML100P', 'packaging' => 'P1', 'name_product' => 'MATCHA LATTE', 'label' => 1],
            ['sku' => 'MGL100P', 'packaging' => 'P1', 'name_product' => 'MANGO LATTE', 'label' => 1],
            ['sku' => 'PDL100P', 'packaging' => 'P1', 'name_product' => 'PANDAN LATTE', 'label' => 1],
            ['sku' => 'RVL100P', 'packaging' => 'P1', 'name_product' => 'RED VELVET LATTE', 'label' => 1],
            ['sku' => 'RMT100P', 'packaging' => 'P1', 'name_product' => 'ROYAL MILK TEA', 'label' => 1],
            ['sku' => 'SA100P', 'packaging' => 'P1', 'name_product' => 'SAKURA LATTE', 'label' => 1],
            ['sku' => 'STL100P', 'packaging' => 'P1', 'name_product' => 'STRAWBERRY LATTE', 'label' => 1],
            ['sku' => 'TR100P', 'packaging' => 'P1', 'name_product' => 'TARO LATTE', 'label' => 1],
            ['sku' => 'TT100P', 'packaging' => 'P1', 'name_product' => 'THAI TEA LATTE', 'label' => 1],

            // Bulk packaging (1000P) products - Label 4
            ['sku' => 'BNL1000P', 'packaging' => '-', 'name_product' => 'BANANA LATTE', 'label' => 4],
            ['sku' => 'BSL1000P', 'packaging' => '-', 'name_product' => 'BLACK SESAME LATTE', 'label' => 4],
            ['sku' => 'CL1000P', 'packaging' => '-', 'name_product' => 'CHAI LATTE', 'label' => 4],
            ['sku' => 'CR1000P', 'packaging' => '-', 'name_product' => 'CHARCOAL LATTE', 'label' => 4],
            ['sku' => 'CCH1000F', 'packaging' => '-', 'name_product' => 'CREAM CHEESE', 'label' => 4],
            ['sku' => 'ERL1000P', 'packaging' => '-', 'name_product' => 'EARL GREY LATTE', 'label' => 4],
            ['sku' => 'GCL1000F', 'packaging' => 'F2', 'name_product' => 'GENMAICHA LATTE', 'label' => 4],
            ['sku' => 'GIL1000P', 'packaging' => '-', 'name_product' => 'GINGER LATTE', 'label' => 4],
            ['sku' => 'TUL1000P', 'packaging' => '-', 'name_product' => 'GOLDEN TURMERIC LATTE', 'label' => 4],
            ['sku' => 'HL1000F', 'packaging' => 'F2', 'name_product' => 'HOJICHA LATTE', 'label' => 4],
            ['sku' => 'ML1000F', 'packaging' => 'F2', 'name_product' => 'MATCHA LATTE', 'label' => 4],
            ['sku' => 'MGL1000P', 'packaging' => '-', 'name_product' => 'MANGO LATTE', 'label' => 4],
            ['sku' => 'PDL1000P', 'packaging' => '-', 'name_product' => 'PANDAN LATTE', 'label' => 4],
            ['sku' => 'RVL1000P', 'packaging' => '-', 'name_product' => 'RED VELVET LATTE', 'label' => 4],
            ['sku' => 'RMT1000P', 'packaging' => '-', 'name_product' => 'ROYAL MILK TEA', 'label' => 4],
            ['sku' => 'SAL1000P', 'packaging' => '-', 'name_product' => 'SAKURA LATTE', 'label' => 4],
            ['sku' => 'STL1000P', 'packaging' => '-', 'name_product' => 'STRAWBERRY LATTE', 'label' => 4],
            ['sku' => 'TR1000P', 'packaging' => '-', 'name_product' => 'TARO LATTE', 'label' => 4],
            ['sku' => 'TT1000P', 'packaging' => '-', 'name_product' => 'THAI TEA LATTE', 'label' => 4],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($lattePowderProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $lattePowderCategory,
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

        $this->command->info(count($lattePowderProducts) . ' LATTE POWDER products seeded successfully.');
    }
}
