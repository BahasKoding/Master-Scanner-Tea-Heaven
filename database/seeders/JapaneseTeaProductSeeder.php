<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JapaneseTeaProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Japanese Tea category
        $japaneseTeaCategory = CategoryProduct::where('name', 'JAPANESE TEA')->first();

        if (!$japaneseTeaCategory) {
            $this->command->error('JAPANESE TEA category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $japaneseTeaCategory->id)->delete();

        $japaneseTeaProducts = [
            // P1 packaging products
            ['sku' => 'GC40P', 'packaging' => 'P1', 'name_product' => 'GENMAICHA'],
            ['sku' => 'GYA30P', 'packaging' => 'P1', 'name_product' => 'HANPICKED GYOKURO'],
            ['sku' => 'HO40P', 'packaging' => 'P1', 'name_product' => 'HOJICHA'],
            ['sku' => 'OC50P', 'packaging' => 'P1', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'SC30P', 'packaging' => 'P1', 'name_product' => 'SENCHA'],

            // P2 packaging products
            ['sku' => 'GC125P', 'packaging' => 'P2', 'name_product' => 'GENMAICHA'],
            ['sku' => 'GYB125P', 'packaging' => 'P2', 'name_product' => 'GYOKURO (A)'],
            ['sku' => 'OC125P', 'packaging' => 'P2', 'name_product' => 'JAPANESE OCHA'],

            // P3 packaging products
            ['sku' => 'HO250P', 'packaging' => 'P3', 'name_product' => 'HOJICHA'],
            ['sku' => 'SC125P', 'packaging' => 'P3', 'name_product' => 'SENCHA'],
            ['sku' => 'GYB500P', 'packaging' => 'P3', 'name_product' => 'GYOKURO (B)'],

            // P4 packaging products
            ['sku' => 'GC500P', 'packaging' => 'P4', 'name_product' => 'GENMAICHA'],
            ['sku' => 'HO500P', 'packaging' => 'P4', 'name_product' => 'HOJICHA'],
            ['sku' => 'OC500P', 'packaging' => 'P4', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'GYB1000P', 'packaging' => 'P4', 'name_product' => 'GYOKURO (B)'],
            ['sku' => 'HO500PX2', 'packaging' => 'P4', 'name_product' => 'HOJICHA'],

            // P5 packaging products
            ['sku' => 'SC500P', 'packaging' => 'P5', 'name_product' => 'SENCHA'],
            ['sku' => 'GC1000P', 'packaging' => 'P5', 'name_product' => 'GENMAICHA'],
            ['sku' => 'OC1000P', 'packaging' => 'P5', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'SC500PX2', 'packaging' => 'P5', 'name_product' => 'SENCHA'],

            // T1 packaging products
            ['sku' => 'GC85T', 'packaging' => 'T1', 'name_product' => 'GENMAICHA'],
            ['sku' => 'GYA60T', 'packaging' => 'T1', 'name_product' => 'HANDPICKED GYOKURO (A)'],
            ['sku' => 'OC75T', 'packaging' => 'T1', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'TG80T', 'packaging' => 'T1', 'name_product' => 'TIE GUAN YIN'],

            // Other packagings
            ['sku' => 'MC30T', 'packaging' => '-', 'name_product' => 'CEREMONIAL MATCHA'],
            ['sku' => 'MCF30T', 'packaging' => '-', 'name_product' => 'CEREMONIAL MATCHA FINEST'],
            ['sku' => 'GC1000F', 'packaging' => 'FG2', 'name_product' => 'GENMAICHA'],
            ['sku' => 'HO1000Z', 'packaging' => 'Z2', 'name_product' => 'HOJICHA'],
            ['sku' => 'OC1000F', 'packaging' => 'F3', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'OC500FX2', 'packaging' => 'F3', 'name_product' => 'JAPANESE OCHA'],
            ['sku' => 'OC500F', 'packaging' => 'F3', 'name_product' => 'JAPANESE OCHA'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($japaneseTeaProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $japaneseTeaCategory->id,
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

        $this->command->info(count($japaneseTeaProducts) . ' JAPANESE TEA products seeded successfully.');
    }
}
