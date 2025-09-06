<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryBahanBaku extends Model
{
    use HasFactory;

    protected $fillable = [
        'bahan_baku_id',
        'stok_awal',
        'stok_masuk',
        'terpakai',
        'defect',
        'stok_sisa',
        'live_stok_gudang',
        'satuan'
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_masuk' => 'integer',
        'terpakai' => 'integer',
        'defect' => 'integer',
        'stok_sisa' => 'integer',
        'live_stok_gudang' => 'integer'
    ];

    /**
     * Relationship dengan BahanBaku
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    /**
     * Calculate live stock gudang
     */
    public function calculateLiveStock()
    {
        $this->live_stok_gudang = $this->stok_awal + $this->stok_masuk - $this->terpakai - $this->defect;
        return $this->live_stok_gudang;
    }

    /**
     * Update stok masuk dari purchase (static method)
     */
    public static function updateStokMasukFromPurchase($bahanBakuId, $totalStokMasuk)
    {
        try {
            DB::beginTransaction();

            $bahanBaku = BahanBaku::findOrFail($bahanBakuId);

            $inventory = self::firstOrCreate(
                ['bahan_baku_id' => $bahanBakuId],
                [
                    'stok_awal' => 0,
                    'stok_masuk' => 0,
                    'terpakai' => 0,
                    'defect' => 0,
                    'live_stok_gudang' => 0,
                    'satuan' => $bahanBaku->satuan
                ]
            );

            $inventory->stok_masuk = $totalStokMasuk;
            $inventory->calculateLiveStock();
            $inventory->save();

            DB::commit();

            Log::info('Stok masuk updated from purchase', [
                'bahan_baku_id' => $bahanBakuId,
                'total_stok_masuk' => $totalStokMasuk,
                'live_stok_gudang' => $inventory->live_stok_gudang
            ]);

            return $inventory;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update stok masuk from purchase', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate stok masuk from all purchases (static method)
     */
    public static function recalculateStokMasukFromPurchases($bahanBakuId)
    {
        try {
            // Calculate total from all purchases for this bahan baku
            $totalStokMasuk = Purchase::where('bahan_baku_id', $bahanBakuId)
                ->where('kategori', 'bahan_baku')
                ->sum('total_stok_masuk');

            return self::updateStokMasukFromPurchase($bahanBakuId, $totalStokMasuk);
        } catch (\Exception $e) {
            Log::error('Failed to recalculate stok masuk from purchases', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate terpakai from all catatan produksi (static method)
     */
    public static function recalculateTerpakaiFromProduksi($bahanBakuId)
    {
        try {
            $inventory = self::where('bahan_baku_id', $bahanBakuId)->first();
            if ($inventory) {
                $inventory->updateTerpakaiFromCatatanProduksi();
                $inventory->save();
                return $inventory;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to recalculate terpakai from catatan produksi', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get inventory with low stock warning
     */
    public static function getLowStockItems($threshold = 10)
    {
        return self::with('bahanBaku')
            ->where('live_stok_gudang', '<=', $threshold)
            ->orderBy('live_stok_gudang', 'asc')
            ->get();
    }

    /**
     * Auto-update stok_masuk from all purchases (instance method)
     */
    public function updateStokMasukFromPurchases()
    {
        try {
            $totalStokMasuk = Purchase::where('bahan_baku_id', $this->bahan_baku_id)
                ->where('kategori', 'bahan_baku')
                ->sum('total_stok_masuk');

            $this->stok_masuk = $totalStokMasuk;
            $this->calculateLiveStock();

            Log::info("Auto-updated stok_masuk for bahan_baku_id {$this->bahan_baku_id}: {$totalStokMasuk}");

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update stok_masuk from purchases for bahan_baku_id {$this->bahan_baku_id}: " . $e->getMessage());
            return $this;
        }
    }

    /**
     * Auto-update terpakai from catatan produksi (instance method)
     */
    public function updateTerpakaiFromCatatanProduksi()
    {
        try {
            $totalTerpakai = CatatanProduksi::whereJsonContains('sku_induk', (string)$this->bahan_baku_id)
                ->get()
                ->sum(function ($catatan) {
                    $bahanBakuIds = $catatan->sku_induk ?? [];
                    $totalTerpakaiArray = $catatan->total_terpakai ?? [];

                    $index = array_search((string)$this->bahan_baku_id, $bahanBakuIds);
                    return $index !== false ? ($totalTerpakaiArray[$index] ?? 0) : 0;
                });

            $this->terpakai = $totalTerpakai;
            $this->calculateLiveStock();

            Log::info("Auto-updated terpakai for bahan_baku_id {$this->bahan_baku_id}: {$totalTerpakai}");

            return $this;
        } catch (\Exception $e) {
            Log::error("Failed to update terpakai from produksi for bahan_baku_id {$this->bahan_baku_id}: " . $e->getMessage());
            return $this;
        }
    }

    public static function recalculateStokSisaFromOpname($bahanBakuId)
    {
        try {
            $inventory = self::where('bahan_baku_id', $bahanBakuId)->first();
            if ($inventory) {
                $inventory->updateStokSisaFromOpname();
                $inventory->save();
                return $inventory;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to recalculate stok_sisa from opname', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getStokSisaFromLastMonthOpname(string $filterMonthYear): int
    {
        try {
            // Tentukan bulan lalu berdasarkan filter
            $datePrev = Carbon::createFromFormat('Y-m', $filterMonthYear)->subMonth();
            
            $latestOpname = StockOpname::whereYear('tanggal_opname', $datePrev->year)
                ->whereMonth('tanggal_opname', $datePrev->month)
                ->where('status', 'selesai')
                ->where('type', 'bahan_baku')
                ->orderBy('tanggal_opname', 'desc')
                ->first();

            $relatedItem = $latestOpname
                ? $latestOpname->items()->where('item_id', $this->bahan_baku_id)->first()
                : null;

            if ($latestOpname) {
                return $this->stok_sisa = $relatedItem ? $relatedItem->stok_fisik : 0;
            }
            return 0;
        } catch (\Exception $e) {
            Log::error("Error getStokSisaFromLastMonthOpname for bahan_baku_id {$this->bahan_baku_id}: " . $e->getMessage());
            return 0;
        }
    }

    public function updateStokSisaFromOpname()
    {
        try {
            // TODO: Replace with actual stock opname table/model when available
            // For now, we'll set a default value or calculate from existing data
            
            // Example calculation - you can modify this based on your opname data structure
            // $opnameTotal = StockOpname::where('product_id', $this->product_id)
            //     ->sum('sisa_stock'); // or whatever field represents remaining stock
            $latestLiveStock = $this->getStokSisaFromLastMonthOpname(now()->format('Y-m'));

            // Temporary: Set to 0 until opname system is implemented
            $this->stok_sisa = $latestLiveStock;
            
            Log::info('Updated stok_sisa from opname data', [
                'product_id' => $this->product_id,
                'stok_sisa' => $this->stok_sisa
            ]);
            
            return $this;
        } catch (\Exception $e) {
            Log::error('Error updating stok_sisa from opname', [
                'product_id' => $this->product_id,
                'error' => $e->getMessage()
            ]);
            
            // Set default value on error
            $this->stok_sisa = 0;
            return $this;
        }
    }

    /**
     * Update from all sources (purchases + production)
     */
    public function updateFromAllSources()
    {
        $this->updateStokMasukFromPurchases();
        $this->updateTerpakaiFromCatatanProduksi();
        return $this;
    }

    /**
     * Boot method to handle events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Always recalculate live stock before saving
            $model->calculateLiveStock();
        });
    }
}
