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
        $japaneseTeaCategory = 4;

        // Don't delete existing products to avoid foreign key constraint issues
        // Product::where('category_product', $japaneseTeaCategory)->delete();

        $japaneseTeaProducts = [
            // P1 packaging products
            ['sku' => 'GC40P', 'packaging' => 'P1', 'name_product' => 'GENMAICHA', 'label' => 1],
            ['sku' => 'GYA30P', 'packaging' => 'P1', 'name_product' => 'HANPICKED GYOKURO', 'label' => 1],
            ['sku' => 'HO40P', 'packaging' => 'P1', 'name_product' => 'HOJICHA', 'label' => 1],
            ['sku' => 'OC50P', 'packaging' => 'P1', 'name_product' => 'JAPANESE OCHA', 'label' => 1],
            ['sku' => 'SC30P', 'packaging' => 'P1', 'name_product' => 'SENCHA', 'label' => 1],

            // P2 packaging products
            ['sku' => 'GC125P', 'packaging' => 'P2', 'name_product' => 'GENMAICHA', 'label' => 2],
            ['sku' => 'GYB125P', 'packaging' => 'P2', 'name_product' => 'GYOKURO (A)', 'label' => 2],
            ['sku' => 'OC125P', 'packaging' => 'P2', 'name_product' => 'JAPANESE OCHA', 'label' => 2],

            // P3 packaging products
            ['sku' => 'HO250P', 'packaging' => 'P3', 'name_product' => 'HOJICHA', 'label' => 2],
            ['sku' => 'SC125P', 'packaging' => 'P3', 'name_product' => 'SENCHA', 'label' => 2],
            ['sku' => 'GYB500P', 'packaging' => 'P3', 'name_product' => 'GYOKURO (B)', 'label' => 3],

            // P4 packaging products
            ['sku' => 'GC500P', 'packaging' => 'P4', 'name_product' => 'GENMAICHA', 'label' => 3],
            ['sku' => 'HO500P', 'packaging' => 'P4', 'name_product' => 'HOJICHA', 'label' => 3],
            ['sku' => 'OC500P', 'packaging' => 'P4', 'name_product' => 'JAPANESE OCHA', 'label' => 3],
            ['sku' => 'GYB1000P', 'packaging' => 'P4', 'name_product' => 'GYOKURO (B)', 'label' => 4],

            // P5 packaging products
            ['sku' => 'SC500P', 'packaging' => 'P5', 'name_product' => 'SENCHA', 'label' => 3],
            ['sku' => 'GC1000P', 'packaging' => 'P5', 'name_product' => 'GENMAICHA', 'label' => 4],
            ['sku' => 'OC1000P', 'packaging' => 'P5', 'name_product' => 'JAPANESE OCHA', 'label' => 4],

            // T1 packaging products - Label 5
            ['sku' => 'GC85T', 'packaging' => 'T1', 'name_product' => 'GENMAICHA', 'label' => 5],
            ['sku' => 'GYA60T', 'packaging' => 'T1', 'name_product' => 'HANDPICKED GYOKURO (A)', 'label' => 5],
            ['sku' => 'OC75T', 'packaging' => 'T1', 'name_product' => 'JAPANESE OCHA', 'label' => 5],
            ['sku' => 'TG80T', 'packaging' => 'T1', 'name_product' => 'TIE GUAN YIN', 'label' => 5],

            // Other packagings
            ['sku' => 'MC30T', 'packaging' => '-', 'name_product' => 'CEREMONIAL MATCHA', 'label' => 5],
            ['sku' => 'MCF30T', 'packaging' => '-', 'name_product' => 'CEREMONIAL MATCHA FINEST', 'label' => 5],
            // ['sku' => 'GC1000F', 'packaging' => 'FG2', 'name_product' => 'GENMAICHA'],
            // ['sku' => 'HO1000Z', 'packaging' => 'Z2', 'name_product' => 'HOJICHA'],
            // ['sku' => 'OC1000F', 'packaging' => 'F3', 'name_product' => 'JAPANESE OCHA'],
            // ['sku' => 'OC500F', 'packaging' => 'F3', 'name_product' => 'JAPANESE OCHA'],
        ];

        // Create products using updateOrCreate to avoid duplicates
        $createdCount = 0;
        $updatedCount = 0;

        foreach ($japaneseTeaProducts as $product) {
            $result = Product::updateOrCreate(
                ['sku' => $product['sku']], // Find by SKU
                [
                    'category_product' => $japaneseTeaCategory,
                    'packaging' => $product['packaging'],
                    'name_product' => $product['name_product'],
                    'label' => $product['label'] ?? null,
                ]
            );

            if ($result->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        $this->command->info("JAPANESE TEA products seeded successfully. Created: {$createdCount}, Updated: {$updatedCount}");
    }
}
