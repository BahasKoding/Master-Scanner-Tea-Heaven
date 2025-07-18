<?php

namespace App\Services;

use App\Models\InventoryBahanBaku;
use App\Models\BahanBaku;
use App\Models\Purchase;
use App\Models\CatatanProduksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InventoryBahanBakuService
{
    /**
     * Create or update inventory bahan baku
     *
     * @param array $data
     * @return InventoryBahanBaku
     */
    public function createOrUpdateInventory(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Get bahan baku for satuan
                $bahanBaku = BahanBaku::findOrFail($data['bahan_baku_id']);

                // Use updateOrCreate to update existing record or create new one
                $inventory = InventoryBahanBaku::updateOrCreate(
                    ['bahan_baku_id' => $data['bahan_baku_id']],
                    [
                        'stok_awal' => $data['stok_awal'],
                        'defect' => $data['defect'],
                        'satuan' => $bahanBaku->satuan
                    ]
                );

                // Recalculate related data
                $this->recalculateInventoryData($data['bahan_baku_id']);

                Log::info('Inventory bahan baku created/updated via service', [
                    'bahan_baku_id' => $data['bahan_baku_id'],
                    'nama_barang' => $bahanBaku->nama_barang,
                    'inventory_id' => $inventory->id,
                    'was_recently_created' => $inventory->wasRecentlyCreated
                ]);

                return $inventory;
            } catch (Exception $e) {
                Log::error('Error creating/updating inventory bahan baku', [
                    'data' => $data,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Update inventory bahan baku
     *
     * @param int $bahanBakuId
     * @param array $data
     * @return InventoryBahanBaku
     */
    public function updateInventory($bahanBakuId, array $data)
    {
        return DB::transaction(function () use ($bahanBakuId, $data) {
            try {
                // Validate that the bahan baku exists
                $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

                // Use updateOrCreate to update existing record or create new one
                $inventory = InventoryBahanBaku::updateOrCreate(
                    ['bahan_baku_id' => $bahanBakuId],
                    [
                        'stok_awal' => $data['stok_awal'],
                        'defect' => $data['defect'],
                        'satuan' => $bahanBaku->satuan
                    ]
                );

                // Recalculate related data
                $this->recalculateInventoryData($bahanBakuId);

                Log::info('Inventory bahan baku updated via service', [
                    'bahan_baku_id' => $bahanBakuId,
                    'nama_barang' => $bahanBaku->nama_barang,
                    'inventory_id' => $inventory->id,
                    'data' => $data
                ]);

                return $inventory;
            } catch (Exception $e) {
                Log::error('Error updating inventory bahan baku', [
                    'bahan_baku_id' => $bahanBakuId,
                    'data' => $data,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Reset inventory bahan baku to default values
     *
     * @param int $bahanBakuId
     * @return InventoryBahanBaku
     */
    public function resetInventory($bahanBakuId)
    {
        return DB::transaction(function () use ($bahanBakuId) {
            try {
                $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

                // Reset to default values
                $inventory = InventoryBahanBaku::updateOrCreate(
                    ['bahan_baku_id' => $bahanBakuId],
                    [
                        'stok_awal' => 0,
                        'defect' => 0,
                        'satuan' => $bahanBaku->satuan
                    ]
                );

                // Recalculate related data
                $this->recalculateInventoryData($bahanBakuId);

                Log::info('Inventory bahan baku reset via service', [
                    'bahan_baku_id' => $bahanBakuId,
                    'nama_barang' => $bahanBaku->nama_barang,
                    'inventory_id' => $inventory->id
                ]);

                return $inventory;
            } catch (Exception $e) {
                Log::error('Error resetting inventory bahan baku', [
                    'bahan_baku_id' => $bahanBakuId,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get inventory for specific bahan baku
     *
     * @param int $bahanBakuId
     * @return InventoryBahanBaku
     */
    public function getInventoryForBahanBaku($bahanBakuId)
    {
        try {
            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            // Find or create default inventory record
            $inventory = InventoryBahanBaku::firstOrNew(['bahan_baku_id' => $bahanBakuId]);

            // If it's a new record, set default values
            if (!$inventory->exists) {
                $inventory->stok_awal = 0;
                $inventory->stok_masuk = 0;
                $inventory->terpakai = 0;
                $inventory->defect = 0;
                $inventory->live_stok_gudang = 0;
                $inventory->satuan = $bahanBaku->satuan;
            }

            // Add bahan baku relationship
            $inventory->bahan_baku = $bahanBaku;

            return $inventory;
        } catch (Exception $e) {
            Log::error('Error getting inventory for bahan baku', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate inventory data from related sources
     *
     * @param int $bahanBakuId
     * @return void
     */
    private function recalculateInventoryData($bahanBakuId)
    {
        try {
            // Recalculate stok_masuk from purchases
            InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBakuId);

            // Recalculate terpakai from catatan produksi
            InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

            Log::info('Inventory data recalculated', [
                'bahan_baku_id' => $bahanBakuId
            ]);
        } catch (Exception $e) {
            Log::error('Error recalculating inventory data', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync all inventory data
     *
     * @return array
     */
    public function syncAllInventory()
    {
        return DB::transaction(function () {
            try {
                $bahanBakus = BahanBaku::all();
                $syncedCount = 0;
                $results = [];

                foreach ($bahanBakus as $bahanBaku) {
                    try {
                        // Recalculate both stok_masuk and terpakai
                        InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBaku->id);
                        InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBaku->id);

                        $results[] = [
                            'bahan_baku_id' => $bahanBaku->id,
                            'nama_barang' => $bahanBaku->nama_barang,
                            'status' => 'success'
                        ];

                        $syncedCount++;
                    } catch (Exception $e) {
                        $results[] = [
                            'bahan_baku_id' => $bahanBaku->id,
                            'nama_barang' => $bahanBaku->nama_barang,
                            'status' => 'error',
                            'error' => $e->getMessage()
                        ];

                        Log::error('Error syncing individual bahan baku', [
                            'bahan_baku_id' => $bahanBaku->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Sync all inventory completed via service', [
                    'total_items' => $bahanBakus->count(),
                    'synced_count' => $syncedCount,
                    'error_count' => $bahanBakus->count() - $syncedCount
                ]);

                return [
                    'synced_count' => $syncedCount,
                    'total_count' => $bahanBakus->count(),
                    'results' => $results
                ];
            } catch (Exception $e) {
                Log::error('Error syncing all inventory', [
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Force sync specific bahan baku inventory
     *
     * @param array $bahanBakuIds
     * @return array
     */
    public function forceSyncInventory(array $bahanBakuIds = [])
    {
        return DB::transaction(function () use ($bahanBakuIds) {
            try {
                // If no specific IDs provided, sync all
                if (empty($bahanBakuIds)) {
                    return $this->syncAllInventory();
                }

                $syncedCount = 0;
                $results = [];

                foreach ($bahanBakuIds as $bahanBakuId) {
                    try {
                        // Validate bahan baku exists
                        $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

                        // Recalculate inventory
                        InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBakuId);
                        InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

                        $results[] = [
                            'bahan_baku_id' => $bahanBakuId,
                            'nama_barang' => $bahanBaku->nama_barang,
                            'status' => 'success'
                        ];

                        $syncedCount++;
                    } catch (Exception $e) {
                        $results[] = [
                            'bahan_baku_id' => $bahanBakuId,
                            'status' => 'error',
                            'error' => $e->getMessage()
                        ];

                        Log::error('Error force syncing specific bahan baku', [
                            'bahan_baku_id' => $bahanBakuId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Force sync inventory completed via service', [
                    'requested_ids' => $bahanBakuIds,
                    'synced_count' => $syncedCount,
                    'error_count' => count($bahanBakuIds) - $syncedCount
                ]);

                return [
                    'synced_count' => $syncedCount,
                    'total_count' => count($bahanBakuIds),
                    'results' => $results
                ];
            } catch (Exception $e) {
                Log::error('Error force syncing inventory', [
                    'bahan_baku_ids' => $bahanBakuIds,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        });
    }

    /**
     * Get low stock items
     *
     * @param int $threshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockItems($threshold = 10)
    {
        try {
            return InventoryBahanBaku::getLowStockItems($threshold);
        } catch (Exception $e) {
            Log::error('Error getting low stock items', [
                'threshold' => $threshold,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get real-time inventory status for specific bahan baku
     *
     * @param int $bahanBakuId
     * @return array
     */
    public function getInventoryStatus($bahanBakuId)
    {
        try {
            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            // Force recalculate to get real-time data
            InventoryBahanBaku::recalculateStokMasukFromPurchases($bahanBakuId);
            InventoryBahanBaku::recalculateTerpakaiFromProduksi($bahanBakuId);

            // Get fresh data
            $inventory = InventoryBahanBaku::where('bahan_baku_id', $bahanBakuId)->first();

            return [
                'bahan_baku' => [
                    'id' => $bahanBaku->id,
                    'sku_induk' => $bahanBaku->sku_induk,
                    'nama_barang' => $bahanBaku->nama_barang,
                    'satuan' => $bahanBaku->satuan,
                    'kategori' => $bahanBaku->kategori
                ],
                'inventory' => [
                    'stok_awal' => $inventory->stok_awal ?? 0,
                    'stok_masuk' => $inventory->stok_masuk ?? 0,
                    'terpakai' => $inventory->terpakai ?? 0,
                    'defect' => $inventory->defect ?? 0,
                    'live_stok_gudang' => $inventory->live_stok_gudang ?? 0
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error getting inventory status', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Bulk update inventory bahan baku
     *
     * @param array $updates
     * @return array
     */
    public function bulkUpdateInventory(array $updates)
    {
        return DB::transaction(function () use ($updates) {
            try {
                $successCount = 0;
                $errorCount = 0;
                $errors = [];
                $updatedItems = [];

                foreach ($updates as $update) {
                    try {
                        // Validate that bahan baku exists
                        $bahanBaku = BahanBaku::findOrFail($update['bahan_baku_id']);

                        // Use the existing updateInventory method for consistency
                        $inventoryBahanBaku = $this->updateInventory(
                            $update['bahan_baku_id'],
                            [
                                'stok_awal' => $update['stok_awal'],
                                'defect' => $update['defect']
                            ]
                        );

                        $successCount++;
                        $updatedItems[] = [
                            'bahan_baku_id' => $update['bahan_baku_id'],
                            'inventory_id' => $inventoryBahanBaku->id,
                            'stok_awal' => $update['stok_awal'],
                            'defect' => $update['defect'],
                            'nama_barang' => $bahanBaku->nama_barang
                        ];

                    } catch (Exception $e) {
                        $errorCount++;
                        $errors[] = [
                            'bahan_baku_id' => $update['bahan_baku_id'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'nama_barang' => isset($update['bahan_baku_id']) ? 
                                (BahanBaku::find($update['bahan_baku_id'])->nama_barang ?? 'Unknown') : 'Unknown'
                        ];

                        Log::error('Error in bulk update for individual item', [
                            'bahan_baku_id' => $update['bahan_baku_id'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'update_data' => $update
                        ]);
                    }
                }

                Log::info('Bulk update inventory completed via service', [
                    'total_items' => count($updates),
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ]);

                return [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'updated_items' => $updatedItems,
                    'errors' => $errors,
                    'total_count' => count($updates)
                ];

            } catch (Exception $e) {
                Log::error('Error in bulk update inventory service', [
                    'error' => $e->getMessage(),
                    'updates' => $updates
                ]);
                throw $e;
            }
        });
    }

    /**
     * Verify inventory consistency
     *
     * @param int|null $bahanBakuId
     * @return array
     */
    public function verifyInventoryConsistency($bahanBakuId = null)
    {
        try {
            $query = $bahanBakuId ?
                BahanBaku::where('id', $bahanBakuId) :
                BahanBaku::all();

            $results = [];
            $inconsistencies = 0;

            foreach ($query->get() as $bahanBaku) {
                // Calculate expected values
                $expectedStokMasuk = Purchase::where('bahan_baku_id', $bahanBaku->id)
                    ->where('kategori', 'bahan_baku')
                    ->sum('total_stok_masuk');

                $expectedTerpakai = CatatanProduksi::whereJsonContains('sku_induk', (string)$bahanBaku->id)
                    ->get()
                    ->sum(function ($catatan) use ($bahanBaku) {
                        $bahanBakuIds = $catatan->sku_induk ?? [];
                        $totalTerpakai = $catatan->total_terpakai ?? [];
                        $index = array_search((string)$bahanBaku->id, $bahanBakuIds);
                        return $index !== false ? ($totalTerpakai[$index] ?? 0) : 0;
                    });

                // Get current inventory
                $inventory = InventoryBahanBaku::where('bahan_baku_id', $bahanBaku->id)->first();

                $currentStokMasuk = $inventory->stok_masuk ?? 0;
                $currentTerpakai = $inventory->terpakai ?? 0;

                $isConsistent = ($currentStokMasuk == $expectedStokMasuk) &&
                    ($currentTerpakai == $expectedTerpakai);

                if (!$isConsistent) {
                    $inconsistencies++;
                }

                $results[] = [
                    'bahan_baku_id' => $bahanBaku->id,
                    'nama_barang' => $bahanBaku->nama_barang,
                    'is_consistent' => $isConsistent,
                    'current_stok_masuk' => $currentStokMasuk,
                    'expected_stok_masuk' => $expectedStokMasuk,
                    'current_terpakai' => $currentTerpakai,
                    'expected_terpakai' => $expectedTerpakai,
                    'stok_masuk_diff' => $currentStokMasuk - $expectedStokMasuk,
                    'terpakai_diff' => $currentTerpakai - $expectedTerpakai
                ];
            }

            Log::info('Inventory consistency check completed', [
                'bahan_baku_id' => $bahanBakuId,
                'total_checked' => count($results),
                'inconsistencies' => $inconsistencies
            ]);

            return [
                'total_checked' => count($results),
                'inconsistencies' => $inconsistencies,
                'is_all_consistent' => $inconsistencies === 0,
                'results' => $results
            ];
        } catch (Exception $e) {
            Log::error('Error verifying inventory consistency', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
