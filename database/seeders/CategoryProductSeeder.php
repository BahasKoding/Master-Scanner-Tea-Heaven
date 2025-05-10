<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use App\Models\Label;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data first to prevent duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        CategoryProduct::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Data structure - each label with its corresponding categories
        $data = [
            'EXTRA SMALL PACK (15-100 GRAM)' => [
                'CLASSIC TEA',
                'COLLECTION',
                'PURE TISANE',
                'ARTISAN TEA',
                'JAPANESE TEA',
                'CHINESE TEA',
                'PURE POWDER',
                'SWEET POWDER',
                'LATTE POWDER',
            ],
            'SMALL PACK (50-250 GRAM)' => [
                'CLASSIC TEA',
                'COLLECTION',
                'PURE TISANE',
                'ARTISAN TEA',
                'JAPANESE TEA',
                'CHINESE TEA',
                'PURE POWDER',
            ],
            'MEDIUM PACK (500 GRAM)' => [
                'CLASSIC TEA',
                'COLLECTION',
                'PURE TISANE',
                'ARTISAN TEA',
                'JAPANESE TEA',
                'CHINESE TEA',
                'PURE POWDER',
            ],
            'BIG PACK (1 KILO)' => [
                'CLASSIC TEA',
                'COLLECTION',
                'PURE TISANE',
                'ARTISAN TEA',
                'JAPANESE TEA',
                'CHINESE TEA',
                'PURE POWDER',
                'LATTE POWDER',
            ],
            'TIN CANISTER SERIES' => [
                'PURE TISANE',
                'ARTISAN TEA',
                'JAPANESE TEA',
            ],
            'REFILL PACK, SAMPLE & GIFT' => [
                'CRAFTED TEAS',
            ],
            'JAPANESE TEABAGS' => [
                // Empty for now
            ],
            'TEA WARE' => [
                // Empty for now
            ],
            'HERBATA NON LABEL 500 GR-1000 GR' => [
                'CLASSIC TEA COLLECTION',
                'JAPANESE TEA',
                'CHINESE TEA',
                'ARTISAN TEA',
            ],
        ];

        $countCreated = 0;

        // Create the categories for each label
        foreach ($data as $labelName => $categories) {
            // Find or create the label
            $label = Label::firstOrCreate(['name' => $labelName]);

            // Create the categories for this label
            foreach ($categories as $categoryName) {
                CategoryProduct::create([
                    'name' => $categoryName,
                    'label_id' => $label->id
                ]);
                $countCreated++;
            }
        }

        $this->command->info("Category products seeded successfully! Created $countCreated categories across different labels.");
    }
}
