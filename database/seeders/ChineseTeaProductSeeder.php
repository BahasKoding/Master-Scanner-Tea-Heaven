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
        $chineseTeaCategory = CategoryProduct::where('name', 'CHINESE TEA')->first();

        if (!$chineseTeaCategory) {
            $this->command->error('CHINESE TEA category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $chineseTeaCategory->id)->delete();

        $chineseTeaProducts = [
            // Small packaging P1
            ['sku' => 'MJ30P', 'packaging' => 'P1', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)'],
            ['sku' => 'GO30P', 'packaging' => 'P1', 'name_product' => 'GREEN GINSENG OOLONG'],
            ['sku' => 'MO40P', 'packaging' => 'P1', 'name_product' => 'MILKY OOLONG'],
            ['sku' => 'MP40P', 'packaging' => 'P1', 'name_product' => 'MILKY PU-ERH'],
            ['sku' => 'LL40P', 'packaging' => 'P1', 'name_product' => 'PURE PU-ERH'],
            ['sku' => 'RD40P', 'packaging' => 'P1', 'name_product' => 'RED OOLONG'],
            ['sku' => 'TG40P', 'packaging' => 'P1', 'name_product' => 'TIE GUAN YIN'],
            ['sku' => 'WS15P', 'packaging' => 'P1', 'name_product' => 'WILD SHOU MEI'],

            // Medium packaging P2/P3
            ['sku' => 'MJ125P', 'packaging' => 'P2', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)'],
            ['sku' => 'GO125P', 'packaging' => 'P2', 'name_product' => 'GREEN GINSENG OOLONG'],
            ['sku' => 'MO125P', 'packaging' => 'P2', 'name_product' => 'MILKY OOLONG'],
            ['sku' => 'MP250P', 'packaging' => 'P3', 'name_product' => 'MILKY PU-ERH'],
            ['sku' => 'LL250P', 'packaging' => 'P3', 'name_product' => 'PURE PU-ERH'],
            ['sku' => 'RD250P', 'packaging' => 'P3', 'name_product' => 'RED OOLONG'],
            ['sku' => 'TG125P', 'packaging' => 'P2', 'name_product' => 'TIE GUAN YIN'],
            ['sku' => 'WS50P', 'packaging' => 'P3', 'name_product' => 'WILD SHOU MEI'],

            // Large packaging P3/P4/Z2
            ['sku' => 'MJ500P', 'packaging' => 'P4', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)'],
            ['sku' => 'GO500P', 'packaging' => 'P3', 'name_product' => 'GREEN GINSENG OOLONG'],
            ['sku' => 'MO500P', 'packaging' => 'P4', 'name_product' => 'MILKY OOLONG'],
            ['sku' => 'MP500P', 'packaging' => 'P4', 'name_product' => 'MILKY PU-ERH'],
            ['sku' => 'LL500P', 'packaging' => 'P4', 'name_product' => 'PURE PU-ERH'],
            ['sku' => 'RD250PX2', 'packaging' => 'P3', 'name_product' => 'RED OOLONG'],
            ['sku' => 'TG500P', 'packaging' => 'P4', 'name_product' => 'TIE GUAN YIN'],
            ['sku' => 'WS500P', 'packaging' => 'Z2', 'name_product' => 'WILD SHOU MEI'],

            // Extra large packaging P4/P5/Z2
            ['sku' => 'MJ500PX2', 'packaging' => 'P4', 'name_product' => 'FRAGRANT ROLL (MAO JIAN)'],
            ['sku' => 'GO1000P', 'packaging' => 'P4', 'name_product' => 'GREEN GINSENG OOLONG'],
            ['sku' => 'MO500PX2', 'packaging' => 'P4', 'name_product' => 'MILKY OOLONG'],
            ['sku' => 'MP1000P', 'packaging' => 'P5', 'name_product' => 'MILKY PU-ERH'],
            ['sku' => 'LL1000P', 'packaging' => 'P5', 'name_product' => 'PURE PU-ERH'],
            ['sku' => 'RD1000P', 'packaging' => 'P5', 'name_product' => 'RED OOLONG'],
            ['sku' => 'TG500PX2', 'packaging' => 'P4', 'name_product' => 'TIE GUAN YIN'],
            ['sku' => 'TG1000P', 'packaging' => 'P5', 'name_product' => 'TIE GUAN YIN'],
            ['sku' => 'WS500ZX2', 'packaging' => 'Z2', 'name_product' => 'WILD SHOU MEI'],

            // Special packaging
            ['sku' => 'MJ1000F', 'packaging' => '-', 'name_product' => 'MAO JIAN'],
            ['sku' => 'MO1000F', 'packaging' => 'FG2', 'name_product' => 'MILKY OOLONG'],
            ['sku' => 'RD1000Z', 'packaging' => 'Z2', 'name_product' => 'RED OOLONG'],
            ['sku' => 'TG1000F', 'packaging' => '-', 'name_product' => 'TIE GUAN YIN'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($chineseTeaProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $chineseTeaCategory->id,
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

        $this->command->info(count($chineseTeaProducts) . ' CHINESE TEA products seeded successfully.');
    }
}
