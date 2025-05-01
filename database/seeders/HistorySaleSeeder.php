<?php

namespace Database\Seeders;

use App\Models\HistorySale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HistorySaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define sample SKU prefixes and their quantities
        $skuPrefixes = [
            'TEA' => [1, 5],    // Tea products (qty 1-5)
            'CUP' => [1, 10],   // Cups and containers (qty 1-10)
            'ACC' => [1, 3],    // Accessories (qty 1-3)
            'PKG' => [1, 2],    // Packaging (qty 1-2)
            'GFT' => [1, 3],    // Gift items (qty 1-3)
        ];

        // Generate 250 records
        for ($i = 0; $i < 250; $i++) {
            // Generate a random date within the last 6 months
            $date = Carbon::now()->subMonths(6)->addSeconds(rand(0, 180 * 24 * 60 * 60));

            // Generate random resi number
            $resiNumber = 'RS' . $date->format('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Generate 1-3 random SKUs for this sale
            $numberOfSKUs = rand(1, 3);
            $skus = [];
            $quantities = [];
            $usedPrefixes = array_rand($skuPrefixes, $numberOfSKUs);

            // Ensure usedPrefixes is always an array
            if (!is_array($usedPrefixes)) {
                $usedPrefixes = [$usedPrefixes];
            }

            foreach ($usedPrefixes as $prefix) {
                // Generate SKU number: PREFIX + YYMM + 3 random digits
                $sku = $prefix . $date->format('ym') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                $skus[] = $sku;

                // Generate random quantity within the defined range for this prefix
                $quantities[] = rand($skuPrefixes[$prefix][0], $skuPrefixes[$prefix][1]);
            }

            // Create the record
            HistorySale::create([
                'no_resi' => $resiNumber,
                'no_sku' => $skus,
                'qty' => $quantities,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
