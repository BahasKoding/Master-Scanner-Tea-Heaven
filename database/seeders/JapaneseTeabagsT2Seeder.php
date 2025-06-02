<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JapaneseTeabagsT2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category mapping:
        // Japanese Tea = 5
        // Crafted Teas = 9

        // Clear existing T2 packaging products to avoid duplicates
        Product::whereIn('packaging', ['T2'])->delete();

        $t2Products = [
            // Special T2 Tin Canister Series - Label 5 
            ['category_product' => 9, 'sku' => 'DM25TC', 'packaging' => 'T2', 'name_product' => 'DIVINE MERCY', 'label' => 5],
            ['category_product' => 9, 'sku' => 'IDB35TC', 'packaging' => 'T2', 'name_product' => 'INDONESIAN BREAKFAST', 'label' => 5],
            ['category_product' => 9, 'sku' => 'LOF60TC', 'packaging' => 'T2', 'name_product' => 'LADY OF FATIMA', 'label' => 5],
            ['category_product' => 9, 'sku' => 'LBF30TC', 'packaging' => 'T2', 'name_product' => 'LITTLE BIRTHDAY FLOWER', 'label' => 5],
            ['category_product' => 9, 'sku' => 'MGR45TC', 'packaging' => 'T2', 'name_product' => 'MARY\'S GARDEN', 'label' => 5],
            ['category_product' => 9, 'sku' => 'STN25TC', 'packaging' => 'T2', 'name_product' => 'STARRY NIGHT', 'label' => 5],

            // Japanese Teabags T2 Packaging - Label 5
            ['category_product' => 5, 'sku' => 'GC15TC', 'packaging' => 'T2', 'name_product' => 'GENMAICHA 15 TEABAGS', 'label' => 5],
            ['category_product' => 5, 'sku' => 'HO15TC', 'packaging' => 'T2', 'name_product' => 'HOJICHA 15 TEABAGS', 'label' => 5],
            ['category_product' => 5, 'sku' => 'SC15TC', 'packaging' => 'T2', 'name_product' => 'KABUSECHA 15 TEABAGS', 'label' => 5],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($t2Products as $product) {
            $productsToInsert[] = [
                'category_product' => $product['category_product'],
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

        $this->command->info(count($t2Products) . ' T2 CANISTER and JAPANESE TEABAGS products seeded successfully.');
    }
}
