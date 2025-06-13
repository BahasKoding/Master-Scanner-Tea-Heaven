<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Don't truncate to avoid foreign key issues, use updateOrCreate instead
        // DB::table('stickers')->truncate();

        // Debug timezone information
        $this->command->info('Current PHP timezone: ' . date_default_timezone_get());
        $this->command->info('Laravel app timezone: ' . config('app.timezone'));
        $this->command->info('Current timestamp: ' . Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s'));
        $this->command->info('System timestamp: ' . date('Y-m-d H:i:s'));

        // Data stickers - generated from sticker.md data
        $stickers = $this->generateStickerData();

        // Use updateOrCreate to avoid duplicates and foreign key issues
        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($stickers as $stickerData) {
            if ($stickerData['product_id']) {
                $result = DB::table('stickers')->updateOrInsert(
                    ['product_id' => $stickerData['product_id']], // Find by product_id
                    $stickerData // Update/Insert with this data
                );

                if ($result) {
                    // Check if it was an insert (new record) or update
                    $existing = DB::table('stickers')->where('product_id', $stickerData['product_id'])->first();
                    if ($existing->created_at === $existing->updated_at) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                }
            } else {
                $skippedCount++;
            }
        }

        $this->command->info("Sticker seeder completed. Created: {$createdCount}, Updated: {$updatedCount}, Skipped: {$skippedCount}");
    }

    /**
     * Get product ID by SKU
     */
    private function getProductIdBySku($sku)
    {
        $product = DB::table('products')->where('sku', $sku)->first();
        if (!$product) {
            $this->command->warn("Product with SKU {$sku} not found!");
            return null;
        }
        return $product->id;
    }

    /**
     * Generate all sticker data based on sticker.md file
     */
    private function generateStickerData()
    {
        $stickerData = [];

        // Set consistent timestamp for all records
        $currentTimestamp = date('Y-m-d H:i:s');

        // EXTRA SMALL PACK (15-100 GRAM) - P1 packaging - 5 X 17 size - 14 per A3
        $extraSmallSkus = [
            'AS100P',
            'BB40P',
            'BN50P',
            'BNL100P',
            'BP15P',
            'BR100P',
            'BSL100P',
            'BTC30P',
            'CC40P',
            'CCH100P',
            'CF30P',
            'CH15P',
            'CL100P',
            'CM15P',
            'CO30P',
            'CR100P',
            'DN15P',
            'EBT30P',
            'ER40P',
            'ERL100P',
            'ERR40P',
            'EV40P',
            'FO15P',
            'GC40P',
            'GCL100P',
            'GI30P',
            'GIL100P',
            'GL15P',
            'GM50P',
            'GMS30P',
            'GNB15P',
            'GO30P',
            'GS15P',
            'GSP15P',
            'GTC30P',
            'GYA30P',
            'HL100P',
            'HO40P',
            'HP100P',
            'HPS100P',
            'IN30P',
            'JA40P',
            'JG30P',
            'LG40P',
            'LL40P',
            'LR15P',
            'LS15P',
            'LV15P',
            'LY40P',
            'MA100P',
            'MB100P',
            'MC100P',
            'MG40P',
            'MGD15P',
            'MGI100P',
            'MGL100P',
            'MJ30P',
            'ML100P',
            'MM40P',
            'MO40P',
            'MP40P',
            'MS100P',
            'MSC50P',
            'MT15P',
            'MU100P',
            'OC50P',
            'OK100P',
            'OR15P',
            'ORB40P',
            'OS15P',
            'OSB30P',
            'PBL15P',
            'PC100P',
            'PD15P',
            'PDL100P',
            'PER100P',
            'PG30P',
            'PH40P',
            'PR50P',
            'PS40P',
            'PTR100P',
            'RB15P',
            'RD40P',
            'RMT100P',
            'RS15P',
            'RU15P',
            'RVL100P',
            'SA100P',
            'SC30P',
            'SL15P',
            'SN50P',
            'ST40P',
            'STB40P',
            'STL100P',
            'SW100P',
            'TG40P',
            'TR100P',
            'TT100P',
            'TUL100P',
            'VB40P',
            'BD10P',
            'WS15P',
            'YL30P'
        ];

        foreach ($extraSmallSkus as $sku) {
            $productId = $this->getProductIdBySku($sku);
            $stickerData[] = [
                'product_id' => $productId, // Will be null if product not found
                'ukuran' => '5 X 17',
                'jumlah' => '14',
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active',
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ];
        }

        // TIN CANISTER SERIES - T1 packaging - 11.5 X 5.7 size - 17 per A3
        $tinCanisterT1Skus = [
            'VB80T',
            'ER85T',
            'EBT80T',
            'JA60T',
            'GM60T',
            'PR100T',
            'SL30T',
            'BP20T',
            'CM25T',
            'CH30T',
            'LV30T',
            'LR35T',
            'MT55T',
            'RB50T',
            'BB70T',
            'CC100T',
            'CO80T',
            'ERR85T',
            'GNB40T',
            'GS35T',
            'JG50T',
            'JB85T',
            'LG70T',
            'LY70T',
            'ORB30T',
            'MG70T',
            'MSC90T',
            'PS70T',
            'PG35T',
            'PH70T',
            'ST70T',
            'GC85T',
            'GYA60T',
            'OC75T',
            'TG80T',
            'RM40T'
        ];

        foreach ($tinCanisterT1Skus as $sku) {
            $productId = $this->getProductIdBySku($sku);
            $stickerData[] = [
                'product_id' => $productId, // Will be null if product not found
                'ukuran' => '11.5 X 5.7',
                'jumlah' => '17',
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active',
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ];
        }

        // TIN CANISTER SERIES - T2 packaging - 13 X 5 size - 16 per A3
        $tinCanisterT2Skus = [
            'DM25TC',
            'IDB35TC',
            'LOF60TC',
            'LBF30TC',
            'MGR45TC',
            'STN25TC',
            'GC15TC',
            'HO15TC',
            'SC15TC'
        ];

        foreach ($tinCanisterT2Skus as $sku) {
            $productId = $this->getProductIdBySku($sku);
            $stickerData[] = [
                'product_id' => $productId, // Will be null if product not found
                'ukuran' => '13 X 5',
                'jumlah' => '16',
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active',
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ];
        }

        // CEREMONIAL MATCHA - No packaging - 10 X 3 size - 42 per A3
        $ceremorialMatchaSkus = ['MC30T', 'MCF30T'];

        foreach ($ceremorialMatchaSkus as $sku) {
            $productId = $this->getProductIdBySku($sku);
            $stickerData[] = [
                'product_id' => $productId, // Will be null if product not found
                'ukuran' => '10 X 3',
                'jumlah' => '42',
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active',
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ];
        }

        // JAPANESE TEABAGS - No packaging - 10 X 3 size - 42 per A3
        $japaneseTeabagSkus = ['GC10TF', 'HO10TF', 'SC10TF'];

        foreach ($japaneseTeabagSkus as $sku) {
            $productId = $this->getProductIdBySku($sku);
            $stickerData[] = [
                'product_id' => $productId, // Will be null if product not found
                'ukuran' => '10 X 3',
                'jumlah' => '42',
                'stok_awal' => 0,
                'stok_masuk' => 0,
                'produksi' => 0,
                'defect' => 0,
                'sisa' => 0,
                'status' => 'active',
                'created_at' => $currentTimestamp,
                'updated_at' => $currentTimestamp
            ];
        }

        return $stickerData;
    }
}
