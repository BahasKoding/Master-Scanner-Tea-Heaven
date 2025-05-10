<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use App\Models\ProductList;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if suppliers exist
        $supplierCount = Supplier::count();
        if ($supplierCount == 0) {
            $this->command->info('No suppliers found. Please run SupplierSeeder first.');
            return;
        }

        // Check if categories exist
        $categoryCount = CategoryProduct::count();
        if ($categoryCount == 0) {
            $this->command->info('No category products found. Please run CategoryProductSeeder first.');
            return;
        }

        // Get the first supplier for sample data
        $supplier = Supplier::first();

        // Get categories
        $categories = CategoryProduct::all();

        // Sample product data
        foreach ($categories as $index => $category) {
            // Create 3 sample products for each category
            for ($i = 1; $i <= 3; $i++) {
                ProductList::create([
                    'category_id' => $category->id,
                    'sku' => 'P' . $category->id . $i . rand(1000, 9999),
                    'pack' => 'Pack ' . $i,
                    'product_name' => ucfirst(strtolower($category->name)) . ' Product ' . $i,
                    'supplier_id' => $supplier->id,
                    'gramasi' => (15 * $i) . ' g'
                ]);
            }
        }

        $this->command->info('Product list seeded successfully!');
    }
}
