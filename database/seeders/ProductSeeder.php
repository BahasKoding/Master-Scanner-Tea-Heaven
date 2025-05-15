<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = CategoryProduct::all();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategoryProductSeeder first.');
            return;
        }

        // Sample products data with different categories
        $products = [
            [
                'sku' => 'TEA-001',
                'name_product' => 'Earl Grey Tea',
                'packaging' => 'Box 50g',
                'category' => 'BLACK TEA'
            ],
            [
                'sku' => 'TEA-002',
                'name_product' => 'English Breakfast Tea',
                'packaging' => 'Box 100g',
                'category' => 'BLACK TEA'
            ],
            [
                'sku' => 'TEA-003',
                'name_product' => 'Jasmine Green Tea',
                'packaging' => 'Pouch 50g',
                'category' => 'GREEN TEA'
            ],
            [
                'sku' => 'TEA-004',
                'name_product' => 'Sencha Green Tea',
                'packaging' => 'Box 100g',
                'category' => 'GREEN TEA'
            ],
            [
                'sku' => 'TEA-005',
                'name_product' => 'Chamomile Tea',
                'packaging' => 'Box 20 sachets',
                'category' => 'HERBAL TEA'
            ],
            [
                'sku' => 'TEA-006',
                'name_product' => 'Lemongrass Tea',
                'packaging' => 'Box 20 sachets',
                'category' => 'HERBAL TEA'
            ],
            [
                'sku' => 'TEA-007',
                'name_product' => 'Chai Tea Latte',
                'packaging' => 'Can 250g',
                'category' => 'TEA BLENDS'
            ],
            [
                'sku' => 'TEA-008',
                'name_product' => 'Matcha Green Tea Powder',
                'packaging' => 'Can 50g',
                'category' => 'GREEN TEA'
            ],
            [
                'sku' => 'TEA-009',
                'name_product' => 'Darjeeling Tea',
                'packaging' => 'Box 100g',
                'category' => 'BLACK TEA'
            ],
            [
                'sku' => 'TEA-010',
                'name_product' => 'Rooibos Tea',
                'packaging' => 'Box 20 sachets',
                'category' => 'HERBAL TEA'
            ],
        ];

        // Create default categories if they don't exist
        $defaultCategories = ['BLACK TEA', 'GREEN TEA', 'HERBAL TEA', 'TEA BLENDS'];
        $categoryMap = [];

        foreach ($defaultCategories as $catName) {
            $category = CategoryProduct::firstOrCreate(['name' => $catName]);
            $categoryMap[$catName] = $category->id;
        }

        // Process product data
        foreach ($products as $productData) {
            $categoryName = $productData['category'];

            // Use the mapped category ID if available, otherwise use the first category
            $categoryId = $categoryMap[$categoryName] ?? $categories->first()->id;

            Product::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'id_category_product' => $categoryId,
                    'name_product' => $productData['name_product'],
                    'packaging' => $productData['packaging'],
                ]
            );
        }

        $this->command->info('Products seeded successfully.');
    }
}
