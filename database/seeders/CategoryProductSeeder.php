<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'CLASSIC TEA COLLECTION',
            'PURE TISANE',
            'ARTISAN TEA',
            'JAPANESE TEA',
            'CHINESE TEA',
            'PURE POWDER',
            'SWEET POWDER',
            'LATTE POWDER',
            'CRAFTED TEAS',
            'JAPANESE TEABAGS',
            'TEA WARE',
        ];

        foreach ($categories as $category) {
            CategoryProduct::updateOrCreate(
                ['name' => $category],
                ['name' => $category]
            );
        }
    }
}
