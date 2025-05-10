<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Common label values
        $labels = [
            'EXTRA SMALL PACK (15-100 GRAM)',
            'SMALL PACK (50-250 GRAM)',
            'MEDIUM PACK (500 GRAM)',
            'BIG PACK (1 KILO)',
            'TIN CANISTER SERIES',
            'REFILL PACK, SAMPLE & GIFT',
            'JAPANESE TEABAGS',
            'TEA WARE',
            'HERBATA NON LABEL 500 GR-1000 GR',
        ];

        // Create each label
        foreach ($labels as $name) {
            Label::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Labels seeded successfully!');
    }
}
