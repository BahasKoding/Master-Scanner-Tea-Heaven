<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeaWareProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // TeaWare products data - These are physical products, not tea
        // Category: TEA WARE (Usually category 9 or 10 depending on your system)

        $teaware_products = [
            // Tea Bags
            [
                'category_product' => 9, // TEA WARE category
                'sku' => 'TA00',
                'name_product' => 'TEA BAG',
                'packaging' => 'PCS',
                'label' => 0, // No specific label for teaware
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Bottles & Infusers
            [
                'category_product' => 9,
                'sku' => 'TA01',
                'name_product' => 'INFUSER BOTTLE 420 ML SILVER',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Strainers
            [
                'category_product' => 9,
                'sku' => 'TA02',
                'name_product' => 'STRAINER STAINLESS',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Teapots
            [
                'category_product' => 9,
                'sku' => 'TA03',
                'name_product' => 'IRON TEAPOT 600 ML',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA04',
                'name_product' => 'IRON TEAPOT 800 ML',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA05',
                'name_product' => 'IRON TEAPOT 1.2 LITER',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Matcha Equipment
            [
                'category_product' => 9,
                'sku' => 'TA06',
                'name_product' => 'MATCHA WHISK ELECTRIC',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA08',
                'name_product' => 'MATCHA WHISK 100 PRONGS',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA12',
                'name_product' => 'MATCHA SPOON CHASAKU',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Accessories
            [
                'category_product' => 9,
                'sku' => 'TA07',
                'name_product' => 'STAINLESS STRAW',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Containers & Pitchers
            [
                'category_product' => 9,
                'sku' => 'TA09',
                'name_product' => 'TEKO PITCHER 1 L',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA10',
                'name_product' => 'TOPLES KACA 280 ML',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_product' => 9,
                'sku' => 'TA11',
                'name_product' => 'GELAS 3IN1 320 ML',
                'packaging' => 'PCS',
                'label' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert TeaWare products
        foreach ($teaware_products as $product) {
            Product::create($product);
        }

        echo "✅ TeaWare products seeded successfully!\n";
        echo "📦 Total products created: " . count($teaware_products) . "\n";
        echo "🔧 Category: TEA WARE (ID: 9)\n";
        echo "📋 Products include: Teapots, Matcha tools, Strainers, Bottles, and Accessories\n";
    }
}
