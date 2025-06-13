<?php

namespace App\Models;

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
        'live_stok_gudang',
        'satuan'
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_masuk' => 'integer',
        'terpakai' => 'integer',
        'defect' => 'integer',
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
     * Update stok masuk dari purchase
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
     * Update terpakai dari catatan produksi
     */
    public static function updateTerpakaiFromProduksi($bahanBakuId, $totalTerpakai)
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

            $inventory->terpakai = $totalTerpakai;
            $inventory->calculateLiveStock();
            $inventory->save();

            DB::commit();

            Log::info('Terpakai updated from catatan produksi', [
                'bahan_baku_id' => $bahanBakuId,
                'total_terpakai' => $totalTerpakai,
                'live_stok_gudang' => $inventory->live_stok_gudang
            ]);

            return $inventory;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update terpakai from catatan produksi', [
                'bahan_baku_id' => $bahanBakuId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate stok masuk from all purchases
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
     * Recalculate terpakai from all catatan produksi
     */
    public static function recalculateTerpakaiFromProduksi($bahanBakuId)
    {
        try {
            // Calculate total from all catatan produksi for this bahan baku
            $totalTerpakai = CatatanProduksi::whereJsonContains('sku_induk', (string)$bahanBakuId)
                ->get()
                ->sum(function ($catatan) use ($bahanBakuId) {
                    $bahanBakuIds = $catatan->sku_induk ?? [];
                    $totalTerpakai = $catatan->total_terpakai ?? [];

                    $index = array_search((string)$bahanBakuId, $bahanBakuIds);
                    return $index !== false ? ($totalTerpakai[$index] ?? 0) : 0;
                });

            return self::updateTerpakaiFromProduksi($bahanBakuId, $totalTerpakai);
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
