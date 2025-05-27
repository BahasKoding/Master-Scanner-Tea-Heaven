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
            // Small packaging P1 (15g) - Label 1
            ['sku' => 'BP15P', 'packaging' => 'P1', 'name_product' => 'BUTTERFLY PEA', 'label' => 1],
            ['sku' => 'BR100P', 'packaging' => 'P1', 'name_product' => 'BORICHA', 'label' => 1],
            ['sku' => 'CM15P', 'packaging' => 'P1', 'name_product' => 'CHAMOMILE', 'label' => 1],
            ['sku' => 'CH15P', 'packaging' => 'P1', 'name_product' => 'CHRYSANTHEMUM', 'label' => 1],
            ['sku' => 'DN15P', 'packaging' => 'P1', 'name_product' => 'DANDELION', 'label' => 1],
            ['sku' => 'FO15P', 'packaging' => 'P1', 'name_product' => 'FORGET ME NOT', 'label' => 1],
            ['sku' => 'GL15P', 'packaging' => 'P1', 'name_product' => 'GLOBE AMARANTH', 'label' => 1],
            ['sku' => 'RS15P', 'packaging' => 'P1', 'name_product' => 'HIBISCUS ROSELLE', 'label' => 1],
            ['sku' => 'LV15P', 'packaging' => 'P1', 'name_product' => 'LAVENDER', 'label' => 1],
            ['sku' => 'LR15P', 'packaging' => 'P1', 'name_product' => 'LEMONGRASS', 'label' => 1],
            ['sku' => 'LS15P', 'packaging' => 'P1', 'name_product' => 'LEMON SLICES', 'label' => 1],
            ['sku' => 'MGD15P', 'packaging' => 'P1', 'name_product' => 'MARIGOLD (CALENDULA)', 'label' => 1],
            ['sku' => 'OR15P', 'packaging' => 'P1', 'name_product' => 'ORANGE PEEL', 'label' => 1],
            ['sku' => 'OS15P', 'packaging' => 'P1', 'name_product' => 'OSMANTHUS', 'label' => 1],
            ['sku' => 'OK100P', 'packaging' => 'P1', 'name_product' => 'OKSUSU CHA', 'label' => 1],
            ['sku' => 'PD15P', 'packaging' => 'P1', 'name_product' => 'PANDAN', 'label' => 1],
            ['sku' => 'MT15P', 'packaging' => 'P1', 'name_product' => 'PEPPERMINT', 'label' => 1],
            ['sku' => 'PBL15P', 'packaging' => 'P1', 'name_product' => 'PEACH BLOSSOM', 'label' => 1],
            ['sku' => 'RU15P', 'packaging' => 'P1', 'name_product' => 'PURPLE ROSELLE', 'label' => 1],
            ['sku' => 'RB15P', 'packaging' => 'P1', 'name_product' => 'ROSE BUDS', 'label' => 1],

            // Medium packaging P2/P3 (50-250g) - Label 2
            ['sku' => 'BP50P', 'packaging' => 'P2', 'name_product' => 'BUTTERFLY PEA', 'label' => 2],
            ['sku' => 'CMB50P', 'packaging' => 'P2', 'name_product' => 'CHAMOMILE INFUSION', 'label' => 2],
            ['sku' => 'CM50P', 'packaging' => 'P2', 'name_product' => 'CHAMOMILE', 'label' => 2],
            ['sku' => 'CH50P', 'packaging' => 'P2', 'name_product' => 'CHRYSANTHEMUM', 'label' => 2],
            ['sku' => 'DN50P', 'packaging' => 'P2', 'name_product' => 'DANDELION', 'label' => 2],
            ['sku' => 'RE50P', 'packaging' => 'P2', 'name_product' => 'EGYPTIAN ROSELLE', 'label' => 2],
            ['sku' => 'FO50P', 'packaging' => 'P2', 'name_product' => 'FORGET ME NOT', 'label' => 2],
            ['sku' => 'GL50P', 'packaging' => 'P2', 'name_product' => 'GLOBE AMARANTH', 'label' => 2],
            ['sku' => 'RS50P', 'packaging' => 'P2', 'name_product' => 'HIBISCUS ROSELLE', 'label' => 2],
            ['sku' => 'JF125P', 'packaging' => 'P3', 'name_product' => 'JASMINE BUDS', 'label' => 2],
            ['sku' => 'LV50P', 'packaging' => 'P2', 'name_product' => 'LAVENDER', 'label' => 2],
            ['sku' => 'LR50P', 'packaging' => 'P2', 'name_product' => 'LEMONGRASS IMPORT', 'label' => 2],
            ['sku' => 'LS50P', 'packaging' => 'P2', 'name_product' => 'LEMON SLICES', 'label' => 2],
            ['sku' => 'MGD50P', 'packaging' => 'P2', 'name_product' => 'MARIGOLD (CALENDULA)', 'label' => 2],
            ['sku' => 'OR50P', 'packaging' => 'P2', 'name_product' => 'ORANGE PEEL', 'label' => 2],
            ['sku' => 'OS50P', 'packaging' => 'P2', 'name_product' => 'OSMANTHUS', 'label' => 2],
            ['sku' => 'PD50P', 'packaging' => 'P3', 'name_product' => 'PANDAN', 'label' => 2],
            ['sku' => 'MT50P', 'packaging' => 'P1', 'name_product' => 'PEPPERMINT', 'label' => 1],
            ['sku' => 'PBL50P', 'packaging' => 'P2', 'name_product' => 'PEACH BLOSSOM', 'label' => 2],
            ['sku' => 'RU50P', 'packaging' => 'P2', 'name_product' => 'PURPLE ROSELLE', 'label' => 2],
            ['sku' => 'RB50P', 'packaging' => 'P1', 'name_product' => 'ROSE BUDS', 'label' => 1],

            // Bulk packaging 500g - Label 10
            ['sku' => 'BP500Z', 'packaging' => 'Z2', 'name_product' => 'BUTTERFLY PEA', 'label' => 10],
            ['sku' => 'CMB500Z', 'packaging' => 'V0', 'name_product' => 'CHAMOMILE INFUSION', 'label' => 10],
            ['sku' => 'CM500Z', 'packaging' => 'V0', 'name_product' => 'CHAMOMILE', 'label' => 10],
            ['sku' => 'CH500Z', 'packaging' => 'V0', 'name_product' => 'CHRYSANTHEMUM', 'label' => 10],
            ['sku' => 'DN500Z', 'packaging' => 'V0', 'name_product' => 'DANDELION', 'label' => 10],
            ['sku' => 'RE500Z', 'packaging' => 'Z2', 'name_product' => 'EGYPTIAN ROSELLE', 'label' => 10],
            ['sku' => 'FO500Z', 'packaging' => '-', 'name_product' => 'FORGET ME NOT', 'label' => 10],
            ['sku' => 'GL500Z', 'packaging' => 'V0', 'name_product' => 'GLOBE AMARANTH', 'label' => 10],
            ['sku' => 'RS500Z', 'packaging' => 'Z2', 'name_product' => 'HIBISCUS ROSELLE', 'label' => 10],
            ['sku' => 'JF500Z', 'packaging' => 'V0', 'name_product' => 'JASMINE BUDS', 'label' => 10],
            ['sku' => 'LV500Z', 'packaging' => '-', 'name_product' => 'LAVENDER', 'label' => 10],
            ['sku' => 'LR500Z', 'packaging' => 'V0', 'name_product' => 'LEMONGRASS IMPORT', 'label' => 10],
            ['sku' => 'LS500Z', 'packaging' => 'Z2', 'name_product' => 'LEMON SLICES', 'label' => 10],
            ['sku' => 'MGD500Z', 'packaging' => '-', 'name_product' => 'MARIGOLD (CALENDULA)', 'label' => 10],
            ['sku' => 'OR500Z', 'packaging' => 'V0', 'name_product' => 'ORANGE PEEL', 'label' => 10],
            ['sku' => 'OS500Z', 'packaging' => 'V0', 'name_product' => 'OSMANTHUS', 'label' => 10],
            ['sku' => 'MT500Z', 'packaging' => '-', 'name_product' => 'PEPPERMINT', 'label' => 10],
            ['sku' => 'PBL500Z', 'packaging' => '-', 'name_product' => 'PEACH BLOSSOM', 'label' => 10],
            ['sku' => 'RU500Z', 'packaging' => 'Z2', 'name_product' => 'PURPLE ROSELLE', 'label' => 10],
            ['sku' => 'RB500Z', 'packaging' => 'V0', 'name_product' => 'ROSE BUDS', 'label' => 10],

            // Bulk packaging 1kg (500g x 2) - Label 10
            ['sku' => 'PD1000Z', 'packaging' => '-', 'name_product' => 'PANDAN', 'label' => 10],
            ['sku' => 'RB1000Z', 'packaging' => 'V0', 'name_product' => 'ROSE BUDS', 'label' => 10],

            // Tisane in Triangular Teabags - Label 8
            ['sku' => 'BP20T', 'packaging' => 'T1', 'name_product' => 'BUTTERFLY PEA', 'label' => 8],
            ['sku' => 'CM25T', 'packaging' => 'T1', 'name_product' => 'CHAMOMILE', 'label' => 8],
            ['sku' => 'CH30T', 'packaging' => 'T1', 'name_product' => 'CHRYSANTHEMUM', 'label' => 8],
            ['sku' => 'LV30T', 'packaging' => 'T1', 'name_product' => 'LAVENDER', 'label' => 8],
            ['sku' => 'LR35T', 'packaging' => 'T1', 'name_product' => 'LEMONGRASS', 'label' => 8],
            ['sku' => 'MT55T', 'packaging' => 'T1', 'name_product' => 'PEPPERMINT', 'label' => 8],
            ['sku' => 'RB50T', 'packaging' => 'T1', 'name_product' => 'ROSE BUDS', 'label' => 8],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($pureTisaneProducts as $product) {
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

        $this->command->info(count($pureTisaneProducts) . ' PURE TISANE products seeded successfully.');
    }
}
