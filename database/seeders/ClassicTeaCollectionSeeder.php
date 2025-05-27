<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassicTeaCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category ID for Classic Tea Collection (1)
        $categoryId = 1;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $categoryId)->delete();

        $classicTeaProducts = [
            // Label 1: EXTRA SMALL PACK (15-100 GRAM) - P1 packaging
            ['sku' => 'VB40P', 'packaging' => 'P1', 'name_product' => 'BOURBON VANILLA', 'label' => 1],
            ['sku' => 'BN50P', 'packaging' => 'P1', 'name_product' => 'COLD BREW GREEN TEA (BANCHA)', 'label' => 1],
            ['sku' => 'ER40P', 'packaging' => 'P1', 'name_product' => 'EARL GREY', 'label' => 1],
            ['sku' => 'EBT30P', 'packaging' => 'P1', 'name_product' => 'ENGLISH BREAKFAST', 'label' => 1],
            ['sku' => 'EV40P', 'packaging' => 'P1', 'name_product' => 'VANILLA BREAKFAST', 'label' => 1],
            ['sku' => 'GMS30P', 'packaging' => 'P1', 'name_product' => 'GREEN MASTER', 'label' => 1],
            ['sku' => 'IN30P', 'packaging' => 'P1', 'name_product' => 'INDIAN ASSAM', 'label' => 1],
            ['sku' => 'JA40P', 'packaging' => 'P1', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 1],
            ['sku' => 'GTC30P', 'packaging' => 'P1', 'name_product' => 'PURE ORGANIC GREEN TEA', 'label' => 1],
            ['sku' => 'BTC30P', 'packaging' => 'P1', 'name_product' => 'PURE ORGANIC BLACK TEA', 'label' => 1],
            ['sku' => 'GM50P', 'packaging' => 'P1', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 1],
            ['sku' => 'PR50P', 'packaging' => 'P1', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 1],
            ['sku' => 'SN50P', 'packaging' => 'P1', 'name_product' => 'SNAIL DETOX (A+)', 'label' => 1],
            ['sku' => 'SL15P', 'packaging' => 'P1', 'name_product' => 'SILVER NEEDLE', 'label' => 1],
            ['sku' => 'BD10P', 'packaging' => 'P1', 'name_product' => 'WHITE PEONY KING', 'label' => 1],
            ['sku' => 'YL30P', 'packaging' => 'P1', 'name_product' => 'YELLOW TEA', 'label' => 1],

            // Label 2: SMALL PACK (50-250 GRAM) - P2/P3 packaging
            ['sku' => 'BT250P', 'packaging' => 'P3', 'name_product' => 'BLACK TEA', 'label' => 2],
            ['sku' => 'VB250P', 'packaging' => 'P3', 'name_product' => 'BOURBON VANILLA', 'label' => 2],
            ['sku' => 'BN250P', 'packaging' => 'P3', 'name_product' => 'COLD BREW GREEN TEA (BANCHA)', 'label' => 2],
            ['sku' => 'CT250P', 'packaging' => 'P3', 'name_product' => 'CEYLON CTC', 'label' => 2],
            ['sku' => 'DCT250P', 'packaging' => 'P3', 'name_product' => 'DUST CTC', 'label' => 2],
            ['sku' => 'ER250P', 'packaging' => 'P3', 'name_product' => 'EARL GREY', 'label' => 2],
            ['sku' => 'EBT250P', 'packaging' => 'P3', 'name_product' => 'ENGLISH BREAKFAST', 'label' => 2],
            ['sku' => 'GMS125P', 'packaging' => 'P3', 'name_product' => 'GREEN MASTER', 'label' => 2],
            ['sku' => 'IN250P', 'packaging' => 'P3', 'name_product' => 'INDIAN ASSAM', 'label' => 2],
            ['sku' => 'JA250P', 'packaging' => 'P3', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 2],
            ['sku' => 'LC250P', 'packaging' => 'P3', 'name_product' => 'LOW CAFFEINE', 'label' => 2],
            ['sku' => 'GTC125P', 'packaging' => 'P2', 'name_product' => 'PURE ORGANIC GREEN TEA', 'label' => 2],
            ['sku' => 'BTC125P', 'packaging' => 'P2', 'name_product' => 'PURE ORGANIC BLACK TEA', 'label' => 2],
            ['sku' => 'GM250P', 'packaging' => 'P3', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 2],
            ['sku' => 'PR250P', 'packaging' => 'P2', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 2],
            ['sku' => 'SN250P', 'packaging' => 'P2', 'name_product' => 'SNAIL DETOX (A+)', 'label' => 2],
            ['sku' => 'SL125P', 'packaging' => 'P3', 'name_product' => 'SILVER NEEDLE', 'label' => 2],
            ['sku' => 'BD50P', 'packaging' => 'P3', 'name_product' => 'WHITE PEONY KING', 'label' => 2],
            ['sku' => 'YL125P', 'packaging' => 'P2', 'name_product' => 'YELLOW TEA', 'label' => 2],

            // Label 3: MEDIUM PACK (500 GRAM) - P4/P5 packaging
            ['sku' => 'BT500P', 'packaging' => 'P4', 'name_product' => 'BLACK TEA', 'label' => 3],
            ['sku' => 'DCT500P', 'packaging' => 'P4', 'name_product' => 'DUST CTC', 'label' => 3],
            ['sku' => 'GMS500P', 'packaging' => 'P4', 'name_product' => 'GREEN MASTER', 'label' => 3],
            ['sku' => 'JA500P', 'packaging' => 'P4', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 3],
            ['sku' => 'LC500P', 'packaging' => 'P4', 'name_product' => 'LOW CAFFEINE', 'label' => 3],
            ['sku' => 'GTC500P', 'packaging' => 'P4', 'name_product' => 'PURE ORGANIC GREEN TEA', 'label' => 3],
            ['sku' => 'BTC500P', 'packaging' => 'P4', 'name_product' => 'PURE ORGANIC BLACK TEA', 'label' => 3],
            ['sku' => 'GM500P', 'packaging' => 'P4', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 3],
            ['sku' => 'PR500P', 'packaging' => 'P3', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 3],
            ['sku' => 'SN500P', 'packaging' => 'P3', 'name_product' => 'SNAIL DETOX (A+)', 'label' => 3],
            ['sku' => 'SL500P', 'packaging' => 'P5', 'name_product' => 'SILVER NEEDLE', 'label' => 3],
            ['sku' => 'YL500P', 'packaging' => 'P5', 'name_product' => 'YELLOW TEA', 'label' => 3],

            // Label 4: BIG PACK (1 Kg) - P5 packaging
            ['sku' => 'BTD1000F', 'packaging' => 'F3', 'name_product' => 'BLACK TEA DUST', 'label' => 4],
            ['sku' => 'BT1000P', 'packaging' => 'P5', 'name_product' => 'BLACK TEA', 'label' => 4],
            ['sku' => 'VB1000P', 'packaging' => 'P5', 'name_product' => 'BOURBON VANILLA', 'label' => 4],
            ['sku' => 'BN1000P', 'packaging' => 'P5', 'name_product' => 'COLD BREW GREEN TEA (BANCHA)', 'label' => 4],
            ['sku' => 'CT1000P', 'packaging' => 'P5', 'name_product' => 'CEYLON CTC', 'label' => 4],
            ['sku' => 'DCT1000P', 'packaging' => 'P5', 'name_product' => 'DUST CTC', 'label' => 4],
            ['sku' => 'ER1000P', 'packaging' => 'P5', 'name_product' => 'EARL GREY', 'label' => 4],
            ['sku' => 'ERM1000P', 'packaging' => 'P5', 'name_product' => 'EARL GREY MILD', 'label' => 4],
            ['sku' => 'EBT1000P', 'packaging' => 'P3', 'name_product' => 'ENGLISH BREAKFAST', 'label' => 4],
            ['sku' => 'FD1000F', 'packaging' => 'F3', 'name_product' => 'FINE DUST BLACK TEA', 'label' => 4],
            ['sku' => 'GMS1000P', 'packaging' => 'P5', 'name_product' => 'GREEN MASTER', 'label' => 4],
            ['sku' => 'IN1000P', 'packaging' => 'P5', 'name_product' => 'INDIAN ASSAM', 'label' => 4],
            ['sku' => 'JA1000P', 'packaging' => 'P5', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 4],
            ['sku' => 'LC1000P', 'packaging' => 'P5', 'name_product' => 'LOW CAFFEINE', 'label' => 4],
            ['sku' => 'GTC1000F', 'packaging' => '-', 'name_product' => 'PURE ORGANIC GREEN TEA', 'label' => 4],
            ['sku' => 'BTC1000F', 'packaging' => '-', 'name_product' => 'PURE ORGANIC BLACK TEA', 'label' => 4],
            ['sku' => 'GM1000P', 'packaging' => 'P5', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 4],
            ['sku' => 'PR1000P', 'packaging' => 'P4', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 4],
            ['sku' => 'SN1000P', 'packaging' => 'P4', 'name_product' => 'SNAIL DETOX (A+)', 'label' => 4],

            // Label 10: NON LABEL 500 GR-1000 GR - Bulk packaging
            ['sku' => 'BT1000Z', 'packaging' => 'Z1', 'name_product' => 'BLACK TEA', 'label' => 10],
            ['sku' => 'BT1000F', 'packaging' => 'FG2', 'name_product' => 'BLACK TEA', 'label' => 10],
            ['sku' => 'VB1000F', 'packaging' => 'FG2', 'name_product' => 'BOURBON VANILLA', 'label' => 10],
            ['sku' => 'BN1000Z', 'packaging' => 'Z1', 'name_product' => 'COLD BREW GREEN TEA (BANCHA)', 'label' => 10],
            ['sku' => 'CT1000Z', 'packaging' => 'Z1', 'name_product' => 'CEYLON CTC', 'label' => 10],
            ['sku' => 'DCT1000F', 'packaging' => 'FG2', 'name_product' => 'DUST CTC', 'label' => 10],
            ['sku' => 'ER1000F', 'packaging' => 'FG2', 'name_product' => 'EARL GREY', 'label' => 10],
            ['sku' => 'ERM1000F', 'packaging' => 'FG2', 'name_product' => 'EARL GREY MILD', 'label' => 10],
            ['sku' => 'EBT1000F', 'packaging' => 'FG2', 'name_product' => 'ENGLISH BREAKFAST', 'label' => 10],
            ['sku' => 'EBR1000FC', 'packaging' => 'FG2', 'name_product' => 'ENGLISH BREAKFAST CUSTOM (DA JIAO HAO)', 'label' => 10],
            ['sku' => 'IN1000F', 'packaging' => 'FG2', 'name_product' => 'INDIAN ASSAM', 'label' => 10],
            ['sku' => 'JA1000Z', 'packaging' => 'Z1', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 10],
            // ['sku' => 'JG500FX2', 'packaging' => 'F3', 'name_product' => 'JASMINE GREEN TEA', 'label' => 10],
            // ['sku' => 'PG500FX2', 'packaging' => 'F3', 'name_product' => 'PANDAN GREEN TEA', 'label' => 10],
            ['sku' => 'GM1000Z', 'packaging' => 'Z2', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 10],
            ['sku' => 'GM1000F', 'packaging' => 'FG2', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 10],
            ['sku' => 'GP1000F', 'packaging' => 'F3', 'name_product' => 'GREEN TEA POWDER', 'label' => 10],
            ['sku' => 'PR1000F', 'packaging' => 'FG2', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 10],
            ['sku' => 'SN1000F', 'packaging' => 'FG2', 'name_product' => 'SNAIL DETOX (A+)', 'label' => 10],
            ['sku' => 'SL500Z', 'packaging' => 'Z2', 'name_product' => 'SILVER NEEDLE', 'label' => 10],
            ['sku' => 'BD250ZX2', 'packaging' => 'Z2', 'name_product' => 'SILVER NEEDLE', 'label' => 10],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($classicTeaProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $categoryId,
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

        $this->command->info(count($classicTeaProducts) . ' CLASSIC TEA COLLECTION products seeded successfully.');
    }
}
