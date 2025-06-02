<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TinCanisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Category mapping:
        // Classic Tea Collection = 1
        // Artisan Tea = 3

        // Clear existing TIN CANISTER products to avoid duplicates
        Product::whereIn('packaging', ['T1'])->delete();

        $tinCanisterProducts = [
            // Classic Tea Collection - TIN CANISTER - T1 packaging - Label 5
            ['category_product' => 1, 'sku' => 'VB80T', 'packaging' => 'T1', 'name_product' => 'BOURBON VANILLA', 'label' => 5],
            ['category_product' => 1, 'sku' => 'ER85T', 'packaging' => 'T1', 'name_product' => 'EARL GREY', 'label' => 5],
            ['category_product' => 1, 'sku' => 'EBT80T', 'packaging' => 'T1', 'name_product' => 'ENGLISH BREAKFAST', 'label' => 5],
            ['category_product' => 1, 'sku' => 'JA60T', 'packaging' => 'T1', 'name_product' => 'JAVANESE JASMINE (A)', 'label' => 5],
            ['category_product' => 1, 'sku' => 'GM60T', 'packaging' => 'T1', 'name_product' => 'PEKOE GREEN TEA (A)', 'label' => 5],
            ['category_product' => 1, 'sku' => 'PR100T', 'packaging' => 'T1', 'name_product' => 'ROYAL PEARL (A+)', 'label' => 5],
            ['category_product' => 1, 'sku' => 'SL30T', 'packaging' => 'T1', 'name_product' => 'SILVER NEEDLE', 'label' => 5],

            // Artisan Tea - TIN CANISTER - T1 packaging - Label 5
            ['category_product' => 3, 'sku' => 'BB70T', 'packaging' => 'T1', 'name_product' => 'BLUEBERRY BLACK TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'CC100T', 'packaging' => 'T1', 'name_product' => 'CHOCOLATE OOLONG TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'CO80T', 'packaging' => 'T1', 'name_product' => 'COCONUT OOLONG TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'ERR85T', 'packaging' => 'T1', 'name_product' => 'FRENCH EARL GREY', 'label' => 5],
            ['category_product' => 3, 'sku' => 'GNB40T', 'packaging' => 'T1', 'name_product' => 'GOLDEN & BLOOM', 'label' => 5],
            ['category_product' => 3, 'sku' => 'GS35T', 'packaging' => 'T1', 'name_product' => 'GOODNIGHT SLEEP', 'label' => 5],
            ['category_product' => 3, 'sku' => 'JG50T', 'packaging' => 'T1', 'name_product' => 'JASMINE GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'JB85T', 'packaging' => 'T1', 'name_product' => 'JASMINE PEARL', 'label' => 5],
            ['category_product' => 3, 'sku' => 'LG70T', 'packaging' => 'T1', 'name_product' => 'LEMON GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'LY70T', 'packaging' => 'T1', 'name_product' => 'LYCHEE GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'ORB30T', 'packaging' => 'T1', 'name_product' => 'MANDARIN ORANGE BLACK TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'MG70T', 'packaging' => 'T1', 'name_product' => 'MANGO GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'MSC90T', 'packaging' => 'T1', 'name_product' => 'MASALA CHAI', 'label' => 5],
            ['category_product' => 3, 'sku' => 'PS70T', 'packaging' => 'T1', 'name_product' => 'PASSIONFRUIT GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'PG35T', 'packaging' => 'T1', 'name_product' => 'PANDAN GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'PH70T', 'packaging' => 'T1', 'name_product' => 'PEACH GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'ST70T', 'packaging' => 'T1', 'name_product' => 'STRAWBERRY GREEN TEA', 'label' => 5],
            ['category_product' => 3, 'sku' => 'RM40T', 'packaging' => 'T1', 'name_product' => 'ROSA MYSTICA', 'label' => 5],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($tinCanisterProducts as $product) {
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

        $this->command->info(count($tinCanisterProducts) . ' TIN CANISTER products seeded successfully.');
    }
}
