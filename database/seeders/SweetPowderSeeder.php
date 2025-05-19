<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SweetPowderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the Sweet Powder category
        $sweetPowderCategory = CategoryProduct::where('name', 'SWEET POWDER')->first();

        if (!$sweetPowderCategory) {
            $this->command->error('SWEET POWDER category not found. Please run CategoryProductSeeder first.');
            return;
        }

        // Clear existing products in this category to avoid duplicates
        Product::where('id_category_product', $sweetPowderCategory->id)->delete();

        $sweetPowderProducts = [
            // Small packaging P1
            ['sku' => 'AS100P', 'packaging' => 'P1', 'name_product' => 'ARENGA SUGAR'],
            ['sku' => 'HPS100P', 'packaging' => 'P1', 'name_product' => 'SWEET HOJICHA'],
            ['sku' => 'MGI100P', 'packaging' => 'P1', 'name_product' => 'SWEET MATCHA GINGER'],
            ['sku' => 'MS100P', 'packaging' => 'P1', 'name_product' => 'SWEET UJI MATCHA'],
        ];

        // Create products in batch
        $productsToInsert = [];

        foreach ($sweetPowderProducts as $product) {
            $productsToInsert[] = [
                'id_category_product' => $sweetPowderCategory->id,
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

        $this->command->info(count($sweetPowderProducts) . ' SWEET POWDER products seeded successfully.');
    }
}
