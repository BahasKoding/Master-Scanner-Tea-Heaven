<?php

namespace Database\Seeders;

use App\Models\CategorySupplier;
use Illuminate\Database\Seeder;

class CategorySupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'CRAFTED TEAS',
            'LOOSE LEAF TEA',
            'PURE TISANE',
            'DRIED FRUIT & SPICES',
            'PURE POWDER',
            'SWEET POWDER',
            'LATTE POWDER',
            'JAPANESE TEA BAGS',
            'TEAWARE',
            'ESSENCE',
            'PACKAGING- TEA HEAVEN POUCH',
            'PACKAGING- FOIL FLAT BOTTOM',
            'PACKAGING- FOIL GUSSET / SACHET',
            'PACKAGING- TRANSMETZ ZIPPER',
            'PACKAGING- VACCUM',
            'PACKAGING- TIN CANISTER',
            'BOX',
            'PRINTING & LABELLING',
            'OUTER PACKAGING'
        ];

        foreach ($categories as $category) {
            CategorySupplier::updateOrCreate(
                ['name' => $category],
                ['name' => $category]
            );
        }
    }
}
