<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassicTeaCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Classic Tea Collection category
        $classicTeaCategory = CategoryProduct::where('name', 'CLASSIC TEA COLLECTION')->first();

        if (!$classicTeaCategory) {
            $this->command->error('CLASSIC TEA COLLECTION category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $classicTeaCategory->id)->delete();

        $classicTeaProducts = [
            ['sku' => 'BT1000Z', 'packaging' => 'Z1', 'name_product' => 'BLACK TEA'],
            ['sku' => 'BT1000F', 'packaging' => 'FG2', 'name_product' => 'BLACK TEA'],
            ['sku' => 'VB1000F', 'packaging' => 'FG2', 'name_product' => 'BOURBON VANILLA'],
            ['sku' => 'BN1000Z', 'packaging' => 'Z1', 'name_product' => 'COLD BREW GREEN TEA (BANCHA)'],
            ['sku' => 'CT1000Z', 'packaging' => 'Z1', 'name_product' => 'CEYLON CTC'],
            ['sku' => 'DCT1000F', 'packaging' => 'FG2', 'name_product' => 'DUST CTC'],
            ['sku' => 'ER1000F', 'packaging' => 'FG2', 'name_product' => 'EARL GREY'],
            ['sku' => 'ERM1000F', 'packaging' => 'FG2', 'name_product' => 'EARL GREY MILD'],
            ['sku' => 'EBT1000F', 'packaging' => 'FG2', 'name_product' => 'ENGLISH BREAKFAST'],
            ['sku' => 'EBR1000FC', 'packaging' => 'FG2', 'name_product' => 'ENGLISH BREAKFAST CUSTOM (DA JIAO HAO)'],
            ['sku' => 'IN1000F', 'packaging' => 'FG2', 'name_product' => 'INDIAN ASSAM'],
            ['sku' => 'JA1000Z', 'packaging' => 'Z1', 'name_product' => 'JAVANESE JASMINE (A)'],
            ['sku' => 'JG500FX2', 'packaging' => 'F3', 'name_product' => 'JASMINE GREEN TEA'],
            ['sku' => 'PG500FX2', 'packaging' => 'F3', 'name_product' => 'PANDAN GREEN TEA'],
            ['sku' => 'GM1000Z', 'packaging' => 'Z2', 'name_product' => 'PEKOE GREEN TEA (A)'],
            ['sku' => 'GM1000F', 'packaging' => 'FG2', 'name_product' => 'PEKOE GREEN TEA (A)'],
            ['sku' => 'GP1000F', 'packaging' => 'F3', 'name_product' => 'GREEN TEA POWDER'],
            ['sku' => 'PR1000F', 'packaging' => 'FG2', 'name_product' => 'ROYAL PEARL (A+)'],
            ['sku' => 'SN1000F', 'packaging' => 'FG2', 'name_product' => 'SNAIL DETOX (A+)'],
            ['sku' => 'SL500ZX2', 'packaging' => 'Z2', 'name_product' => 'SILVER NEEDLE'],
            ['sku' => 'SL500Z', 'packaging' => 'Z2', 'name_product' => 'SILVER NEEDLE'],
            ['sku' => 'BD250ZX2', 'packaging' => 'Z2', 'name_product' => 'SILVER NEEDLE'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($classicTeaProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $classicTeaCategory->id,
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

        $this->command->info(count($classicTeaProducts) . ' CLASSIC TEA COLLECTION products seeded successfully.');
    }
}
