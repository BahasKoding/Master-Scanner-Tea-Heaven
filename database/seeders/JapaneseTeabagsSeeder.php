<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JapaneseTeabagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Japanese Teabags category
        $japaneseTeabagsCategory = 10;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $japaneseTeabagsCategory)->delete();

        $japaneseTeabagsProducts = [
            // 10 Teabags (FG3)
            ['sku' => 'GC10TF', 'packaging' => 'FG3', 'name_product' => 'GENMAICHA 10 TEABAGS'],
            ['sku' => 'HO10TF', 'packaging' => 'FG3', 'name_product' => 'HOJICHA 10 TEABAGS'],
            ['sku' => 'SC10TF', 'packaging' => 'FG3', 'name_product' => 'KABUSECHA 10 TEABAGS'],

            // 15 Teabags (FG1)
            ['sku' => 'GC15TC', 'packaging' => 'FG1', 'name_product' => 'GENMAICHA 15 TEABAGS'],
            ['sku' => 'HO15TC', 'packaging' => 'FG1', 'name_product' => 'HOJICHA 15 TEABAGS'],
            ['sku' => 'SC15TC', 'packaging' => 'FG1', 'name_product' => 'KABUSECHA 15 TEABAGS'],

            // 100 Teabags (no packaging)
            ['sku' => 'GC100TB', 'packaging' => '-', 'name_product' => 'GENMAICHA 100 TEABAGS'],
            ['sku' => 'HO100TB', 'packaging' => '-', 'name_product' => 'HOJICHA100 TEABAGS'],
            ['sku' => 'SC100TB', 'packaging' => '-', 'name_product' => 'KABUSECHA 100 TEABAGS'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($japaneseTeabagsProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $japaneseTeabagsCategory,
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

        $this->command->info(count($japaneseTeabagsProducts) . ' JAPANESE TEABAGS products seeded successfully.');
    }
}
