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
        // Data kategori produk
        $categories = [
            'Minuman',
            'Makanan',
            'Snack',
            'Alat Minum',
            'Merchandise',
        ];

        // Masukkan data ke database
        foreach ($categories as $category) {
            CategoryProduct::firstOrCreate([
                'name' => $category
            ]);
        }
    }
}
