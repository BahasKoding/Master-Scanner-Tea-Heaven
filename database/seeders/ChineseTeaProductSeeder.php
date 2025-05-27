<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChineseTeaProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Chinese Tea category
        $chineseTeaCategory = 5;


        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $chineseTeaCategory)->delete();

        $chineseTeaProducts = [
            // Label 1: EXTRA SMALL PACK (15-100 GRAM) - P1 packaging
            ['sku' => 'MJ30P', 'packaging' => 'P1', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)', 'label' => 1],
            ['sku' => 'GO30P', 'packaging' => 'P1', 'name_product' => 'GREEN GINSENG OOLONG', 'label' => 1],
            ['sku' => 'MO40P', 'packaging' => 'P1', 'name_product' => 'MILKY OOLONG', 'label' => 1],
            ['sku' => 'MP40P', 'packaging' => 'P1', 'name_product' => 'MILKY PU-ERH', 'label' => 1],
            ['sku' => 'LL40P', 'packaging' => 'P1', 'name_product' => 'PURE PU-ERH', 'label' => 1],
            ['sku' => 'RD40P', 'packaging' => 'P1', 'name_product' => 'RED OOLONG', 'label' => 1],
            ['sku' => 'TG40P', 'packaging' => 'P1', 'name_product' => 'TIE GUAN YIN', 'label' => 1],
            ['sku' => 'WS15P', 'packaging' => 'P1', 'name_product' => 'WILD SHOU MEI', 'label' => 1],

            // Label 2: SMALL PACK (50-250 GRAM) - P2/P3 packaging
            ['sku' => 'MJ125P', 'packaging' => 'P2', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)', 'label' => 2],
            ['sku' => 'GO125P', 'packaging' => 'P2', 'name_product' => 'GREEN GINSENG OOLONG', 'label' => 2],
            ['sku' => 'MO125P', 'packaging' => 'P2', 'name_product' => 'MILKY OOLONG', 'label' => 2],
            ['sku' => 'MP250P', 'packaging' => 'P3', 'name_product' => 'MILKY PU-ERH', 'label' => 2],
            ['sku' => 'LL250P', 'packaging' => 'P3', 'name_product' => 'PURE PU-ERH', 'label' => 2],
            ['sku' => 'RD250P', 'packaging' => 'P3', 'name_product' => 'RED OOLONG', 'label' => 2],
            ['sku' => 'TG125P', 'packaging' => 'P2', 'name_product' => 'TIE GUAN YIN', 'label' => 2],
            ['sku' => 'WS50P', 'packaging' => 'P3', 'name_product' => 'WILD SHOU MEI', 'label' => 2],

            // Label 3: MEDIUM PACK (500 GRAM) - P3/P4/Z2 packaging
            ['sku' => 'MJ500P', 'packaging' => 'P4', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)', 'label' => 3],
            ['sku' => 'GO500P', 'packaging' => 'P3', 'name_product' => 'GREEN GINSENG OOLONG', 'label' => 3],
            ['sku' => 'MO500P', 'packaging' => 'P4', 'name_product' => 'MILKY OOLONG', 'label' => 3],
            ['sku' => 'MP500P', 'packaging' => 'P4', 'name_product' => 'MILKY PU-ERH', 'label' => 3],
            ['sku' => 'LL500P', 'packaging' => 'P4', 'name_product' => 'PURE PU-ERH', 'label' => 3],
            ['sku' => 'TG500P', 'packaging' => 'P4', 'name_product' => 'TIE GUAN YIN', 'label' => 3],
            ['sku' => 'WS500P', 'packaging' => 'Z2', 'name_product' => 'WILD SHOU MEI', 'label' => 3],

            // Label 4: BIG PACK (1 Kg) - P4/P5/Z2 packaging
            ['sku' => 'GO1000P', 'packaging' => 'P4', 'name_product' => 'GREEN GINSENG OOLONG', 'label' => 4],
            ['sku' => 'MP1000P', 'packaging' => 'P5', 'name_product' => 'MILKY PU-ERH', 'label' => 4],
            ['sku' => 'LL1000P', 'packaging' => 'P5', 'name_product' => 'PURE PU-ERH', 'label' => 4],
            ['sku' => 'RD1000P', 'packaging' => 'P5', 'name_product' => 'RED OOLONG', 'label' => 4],
            ['sku' => 'TG1000P', 'packaging' => 'P5', 'name_product' => 'TIE GUAN YIN', 'label' => 4],

            // Label 10: NON LABEL 500 GR-1000 GR - Special packaging
            ['sku' => 'MJ1000F', 'packaging' => '-', 'name_product' => 'MAO JIAN', 'label' => 10],
            ['sku' => 'MO1000F', 'packaging' => 'FG2', 'name_product' => 'MILKY OOLONG', 'label' => 10],
            ['sku' => 'RD1000Z', 'packaging' => 'Z2', 'name_product' => 'RED OOLONG', 'label' => 10],
            ['sku' => 'TG1000F', 'packaging' => '-', 'name_product' => 'TIE GUAN YIN', 'label' => 10],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($chineseTeaProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $chineseTeaCategory,
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

        $this->command->info(count($chineseTeaProducts) . ' CHINESE TEA products seeded successfully.');
    }
}
