<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PureTisaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category ID for Pure Tisane (2)
        $categoryId = 2;

        // Clear existing products in this category to avoid duplicates
        Product::where('category_product', $categoryId)->delete();

        $pureTisaneProducts = [
            // Small packaging P1 (15g)
            ['sku' => 'BP15P', 'packaging' => 'P1', 'name_product' => 'BUTTERFLY PEA'],
            ['sku' => 'BR100P', 'packaging' => 'P1', 'name_product' => 'BORICHA'],
            ['sku' => 'CM15P', 'packaging' => 'P1', 'name_product' => 'CHAMOMILE'],
            ['sku' => 'CH15P', 'packaging' => 'P1', 'name_product' => 'CHRYSANTHEMUM'],
            ['sku' => 'DN15P', 'packaging' => 'P1', 'name_product' => 'DANDELION'],
            ['sku' => 'FO15P', 'packaging' => 'P1', 'name_product' => 'FORGET ME NOT'],
            ['sku' => 'GL15P', 'packaging' => 'P1', 'name_product' => 'GLOBE AMARANTH'],
            ['sku' => 'RS15P', 'packaging' => 'P1', 'name_product' => 'HIBISCUS ROSELLE'],
            ['sku' => 'LV15P', 'packaging' => 'P1', 'name_product' => 'LAVENDER'],
            ['sku' => 'LR15P', 'packaging' => 'P1', 'name_product' => 'LEMONGRASS'],
            ['sku' => 'LS15P', 'packaging' => 'P1', 'name_product' => 'LEMON SLICES'],
            ['sku' => 'MGD15P', 'packaging' => 'P1', 'name_product' => 'MARIGOLD (CALENDULA)'],
            ['sku' => 'OR15P', 'packaging' => 'P1', 'name_product' => 'ORANGE PEEL'],
            ['sku' => 'OS15P', 'packaging' => 'P1', 'name_product' => 'OSMANTHUS'],
            ['sku' => 'OK100P', 'packaging' => 'P1', 'name_product' => 'OKSUSU CHA'],
            ['sku' => 'PD15P', 'packaging' => 'P1', 'name_product' => 'PANDAN'],
            ['sku' => 'MT15P', 'packaging' => 'P1', 'name_product' => 'PEPPERMINT'],
            ['sku' => 'PBL15P', 'packaging' => 'P1', 'name_product' => 'PEACH BLOSSOM'],
            ['sku' => 'RU15P', 'packaging' => 'P1', 'name_product' => 'PURPLE ROSELLE'],
            ['sku' => 'RB15P', 'packaging' => 'P1', 'name_product' => 'ROSE BUDS'],

            // Medium packaging P2 (50g)
            ['sku' => 'BP50P', 'packaging' => 'P2', 'name_product' => 'BUTTERFLY PEA'],
            ['sku' => 'CMB50P', 'packaging' => 'P2', 'name_product' => 'CHAMOMILE INFUSION'],
            ['sku' => 'CM50P', 'packaging' => 'P2', 'name_product' => 'CHAMOMILE'],
            ['sku' => 'CH50P', 'packaging' => 'P2', 'name_product' => 'CHRYSANTHEMUM'],
            ['sku' => 'DN50P', 'packaging' => 'P2', 'name_product' => 'DANDELION'],
            ['sku' => 'RE50P', 'packaging' => 'P2', 'name_product' => 'EGYPTIAN ROSELLE'],
            ['sku' => 'FO50P', 'packaging' => 'P2', 'name_product' => 'FORGET ME NOT'],
            ['sku' => 'GL50P', 'packaging' => 'P2', 'name_product' => 'GLOBE AMARANTH'],
            ['sku' => 'RS50P', 'packaging' => 'P2', 'name_product' => 'HIBISCUS ROSELLE'],
            ['sku' => 'JF125P', 'packaging' => 'P3', 'name_product' => 'JASMINE BUDS'],
            ['sku' => 'LV50P', 'packaging' => 'P2', 'name_product' => 'LAVENDER'],
            ['sku' => 'LR50P', 'packaging' => 'P2', 'name_product' => 'LEMONGRASS IMPORT'],
            ['sku' => 'LS50P', 'packaging' => 'P2', 'name_product' => 'LEMON SLICES'],
            ['sku' => 'MGD50P', 'packaging' => 'P2', 'name_product' => 'MARIGOLD (CALENDULA)'],
            ['sku' => 'OR50P', 'packaging' => 'P2', 'name_product' => 'ORANGE PEEL'],
            ['sku' => 'OS50P', 'packaging' => 'P2', 'name_product' => 'OSMANTHUS'],
            ['sku' => 'PD50P', 'packaging' => 'P3', 'name_product' => 'PANDAN'],
            ['sku' => 'MT50P', 'packaging' => 'P1', 'name_product' => 'PEPPERMINT'],
            ['sku' => 'PBL50P', 'packaging' => 'P2', 'name_product' => 'PEACH BLOSSOM'],
            ['sku' => 'RU50P', 'packaging' => 'P2', 'name_product' => 'PURPLE ROSELLE'],
            ['sku' => 'RB50P', 'packaging' => 'P1', 'name_product' => 'ROSE BUDS'],

            // Bulk packaging 500g
            ['sku' => 'BP500Z', 'packaging' => 'Z2', 'name_product' => 'BUTTERFLY PEA'],
            ['sku' => 'CMB500Z', 'packaging' => 'V0', 'name_product' => 'CHAMOMILE INFUSION'],
            ['sku' => 'CM500Z', 'packaging' => 'V0', 'name_product' => 'CHAMOMILE'],
            ['sku' => 'CH500Z', 'packaging' => 'V0', 'name_product' => 'CHRYSANTHEMUM'],
            ['sku' => 'DN500Z', 'packaging' => 'V0', 'name_product' => 'DANDELION'],
            ['sku' => 'RE500Z', 'packaging' => 'Z2', 'name_product' => 'EGYPTIAN ROSELLE'],
            ['sku' => 'FO500Z', 'packaging' => '-', 'name_product' => 'FORGET ME NOT'],
            ['sku' => 'GL500Z', 'packaging' => 'V0', 'name_product' => 'GLOBE AMARANTH'],
            ['sku' => 'RS500Z', 'packaging' => 'Z2', 'name_product' => 'HIBISCUS ROSELLE'],
            ['sku' => 'JF500Z', 'packaging' => 'V0', 'name_product' => 'JASMINE BUDS'],
            ['sku' => 'LV500Z', 'packaging' => '-', 'name_product' => 'LAVENDER'],
            ['sku' => 'LR500Z', 'packaging' => 'V0', 'name_product' => 'LEMONGRASS IMPORT'],
            ['sku' => 'LS500Z', 'packaging' => 'Z2', 'name_product' => 'LEMON SLICES'],
            ['sku' => 'MGD500Z', 'packaging' => '-', 'name_product' => 'MARIGOLD (CALENDULA)'],
            ['sku' => 'OR500Z', 'packaging' => 'V0', 'name_product' => 'ORANGE PEEL'],
            ['sku' => 'OS500Z', 'packaging' => 'V0', 'name_product' => 'OSMANTHUS'],
            ['sku' => 'PD250PX2', 'packaging' => 'V0', 'name_product' => 'PANDAN'],
            ['sku' => 'MT500Z', 'packaging' => '-', 'name_product' => 'PEPPERMINT'],
            ['sku' => 'PBL500Z', 'packaging' => '-', 'name_product' => 'PEACH BLOSSOM'],
            ['sku' => 'RU500Z', 'packaging' => 'Z2', 'name_product' => 'PURPLE ROSELLE'],
            ['sku' => 'RB500Z', 'packaging' => 'V0', 'name_product' => 'ROSE BUDS'],

            // Bulk packaging 1kg (500g x 2)
            ['sku' => 'BP500ZX2', 'packaging' => 'Z2', 'name_product' => 'BUTTERFLY PEA'],
            ['sku' => 'CM500ZX2', 'packaging' => '-', 'name_product' => 'CHAMOMILE'],
            ['sku' => 'CH500ZX2', 'packaging' => 'V0', 'name_product' => 'CHRYSANTHEMUM'],
            ['sku' => 'DN500ZX2', 'packaging' => 'V0', 'name_product' => 'DANDELION'],
            ['sku' => 'RE500ZX2', 'packaging' => 'Z2', 'name_product' => 'EGYPTIAN ROSELLE'],
            ['sku' => 'FO500ZX2', 'packaging' => '-', 'name_product' => 'FORGET ME NOT'],
            ['sku' => 'GL500ZX2', 'packaging' => 'V0', 'name_product' => 'GLOBE AMARANTH'],
            ['sku' => 'JF500ZX2', 'packaging' => 'V0', 'name_product' => 'JASMINE BUDS'],
            ['sku' => 'RS500ZX2', 'packaging' => 'Z2', 'name_product' => 'HIBISCUS ROSELLE'],
            ['sku' => 'JG500ZX2', 'packaging' => 'V0', 'name_product' => 'JASMINE BUDS'],
            ['sku' => 'LV500ZX2', 'packaging' => '', 'name_product' => 'LAVENDER'],
            ['sku' => 'LR500ZX2', 'packaging' => 'V0', 'name_product' => 'LEMONGRASS IMPORT'],
            ['sku' => 'LS500ZX2', 'packaging' => 'Z2', 'name_product' => 'LEMON SLICES'],
            ['sku' => 'MGD500ZX2', 'packaging' => '-', 'name_product' => 'MARIGOLD (CALENDULA)'],
            ['sku' => 'OR500ZX2', 'packaging' => 'V0', 'name_product' => 'ORANGE PEEL'],
            ['sku' => 'OS500ZX2', 'packaging' => 'V0', 'name_product' => 'OSMANTHUS'],
            ['sku' => 'PD1000Z', 'packaging' => '-', 'name_product' => 'PANDAN'],
            ['sku' => 'MT500ZX2', 'packaging' => '-', 'name_product' => 'PEPPERMINT'],
            ['sku' => 'PBL500ZX2', 'packaging' => '-', 'name_product' => 'PEACH BLOSSOM'],
            ['sku' => 'RU500ZX2', 'packaging' => 'Z2', 'name_product' => 'PURPLE ROSELLE'],
            ['sku' => 'RB1000Z', 'packaging' => 'V0', 'name_product' => 'ROSE BUDS'],

            // Tisane in Triangular Teabags
            ['sku' => 'BP20T', 'packaging' => 'T1', 'name_product' => 'BUTTERFLY PEA'],
            ['sku' => 'CM25T', 'packaging' => 'T1', 'name_product' => 'CHAMOMILE'],
            ['sku' => 'CH30T', 'packaging' => 'T1', 'name_product' => 'CHRYSANTHEMUM'],
            ['sku' => 'LV30T', 'packaging' => 'T1', 'name_product' => 'LAVENDER'],
            ['sku' => 'LR35T', 'packaging' => 'T1', 'name_product' => 'LEMONGRASS'],
            ['sku' => 'MT55T', 'packaging' => 'T1', 'name_product' => 'PEPPERMINT'],
            ['sku' => 'RB50T', 'packaging' => 'T1', 'name_product' => 'ROSE BUDS'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($pureTisaneProducts as $product) {
            $productsToInsert[] = [
                'category_product' => $categoryId,
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

        $this->command->info(count($pureTisaneProducts) . ' PURE TISANE products seeded successfully.');
    }
}
