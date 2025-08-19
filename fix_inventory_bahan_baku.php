<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIX INVENTORY BAHAN BAKU STOCK CALCULATION ===\n";

try {
    $totalFixed = 0;
    $totalErrors = 0;
    
    // Get all inventory records
    $inventories = App\Models\InventoryBahanBaku::with('bahanBaku')->get();
    
    echo "Processing " . $inventories->count() . " inventory records...\n\n";
    
    foreach ($inventories as $inventory) {
        try {
            $bahanBakuId = $inventory->bahan_baku_id;
            $namaBarang = $inventory->bahanBaku ? $inventory->bahanBaku->nama_barang : 'Unknown';
            
            echo "Processing: {$namaBarang} (ID: {$bahanBakuId})\n";
            
            // Store original values
            $originalStokMasuk = $inventory->stok_masuk;
            $originalTerpakai = $inventory->terpakai;
            $originalLiveStock = $inventory->live_stok_gudang;
            
            // 1. Recalculate stok_masuk from purchases
            $totalStokMasuk = App\Models\Purchase::where('bahan_baku_id', $bahanBakuId)
                ->where('kategori', 'bahan_baku')
                ->sum('total_stok_masuk');
            
            // 2. Recalculate terpakai from catatan produksi
            $totalTerpakai = App\Models\CatatanProduksi::whereJsonContains('sku_induk', (string)$bahanBakuId)
                ->get()
                ->sum(function ($catatan) use ($bahanBakuId) {
                    $bahanBakuIds = $catatan->sku_induk ?? [];
                    $totalTerpakaiArray = $catatan->total_terpakai ?? [];
                    
                    $index = array_search((string)$bahanBakuId, $bahanBakuIds);
                    return $index !== false ? ($totalTerpakaiArray[$index] ?? 0) : 0;
                });
            
            // 3. Update inventory
            $inventory->stok_masuk = $totalStokMasuk;
            $inventory->terpakai = $totalTerpakai;
            $inventory->calculateLiveStock();
            $inventory->save();
            
            // Show changes
            $newLiveStock = $inventory->live_stok_gudang;
            
            echo "  - Stok Masuk: {$originalStokMasuk} → {$totalStokMasuk}\n";
            echo "  - Terpakai: {$originalTerpakai} → {$totalTerpakai}\n";
            echo "  - Live Stock: {$originalLiveStock} → {$newLiveStock}\n";
            
            if ($originalLiveStock != $newLiveStock) {
                echo "  ✅ UPDATED\n";
                $totalFixed++;
            } else {
                echo "  - No change needed\n";
            }
            
            echo "\n";
            
        } catch (Exception $e) {
            echo "  ❌ ERROR: " . $e->getMessage() . "\n\n";
            $totalErrors++;
        }
    }
    
    echo "=== SUMMARY ===\n";
    echo "Total processed: " . $inventories->count() . "\n";
    echo "Total fixed: {$totalFixed}\n";
    echo "Total errors: {$totalErrors}\n";
    
    // Verify results
    echo "\n=== VERIFICATION ===\n";
    $nonZeroStock = App\Models\InventoryBahanBaku::where('live_stok_gudang', '>', 0)->count();
    $zeroStock = App\Models\InventoryBahanBaku::where('live_stok_gudang', '=', 0)->count();
    $negativeStock = App\Models\InventoryBahanBaku::where('live_stok_gudang', '<', 0)->count();
    
    echo "Items with positive stock: {$nonZeroStock}\n";
    echo "Items with zero stock: {$zeroStock}\n";
    echo "Items with negative stock: {$negativeStock}\n";
    
    if ($nonZeroStock > 1) {
        echo "\n🎉 SUCCESS! Multiple items now have stock values!\n";
        echo "Stock opname should now show all items with proper stock values.\n";
    }
    
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== COMPLETE ===\n";
