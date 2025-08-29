<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'bahan_baku_id',
        'qty_pembelian',
        'tanggal_kedatangan_barang',
        'qty_barang_masuk',
        'barang_defect_tanpa_retur',
        'barang_diretur_ke_supplier',
        'total_stok_masuk',
        'checker_penerima_barang'
    ];

    protected $casts = [
        'tanggal_kedatangan_barang' => 'date:Y-m-d',
        'qty_pembelian' => 'integer',
        'qty_barang_masuk' => 'integer',
        'barang_defect_tanpa_retur' => 'integer',
        'barang_diretur_ke_supplier' => 'integer',
        'total_stok_masuk' => 'integer'
    ];

    /**
     * Relationship to BahanBaku (when kategori = bahan_baku)
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    /**
     * Relationship to Product (when kategori = finished_goods)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'bahan_baku_id');
    }

    /**
     * Get the related item based on kategori (polymorphic-like behavior)
     */
    public function getRelatedItemAttribute()
    {
        if ($this->kategori === 'finished_goods') {
            return $this->product;
        }
        return $this->bahanBaku;
    }

    /**
     * Get the item based on kategori
     */
    public function getItemAttribute()
    {
        if ($this->kategori === 'finished_goods') {
            return $this->product;
        }
        return $this->bahanBaku;
    }

    /**
     * Get item name based on kategori
     */
    public function getItemNameAttribute()
    {
        if ($this->kategori === 'finished_goods') {
            return $this->product ? $this->product->name_product : '-';
        }
        return $this->bahanBaku ? $this->bahanBaku->nama_barang : '-';
    }

    /**
     * Get item SKU based on kategori
     */
    public function getItemSkuAttribute()
    {
        if ($this->kategori === 'finished_goods') {
            return $this->product ? $this->product->sku : '-';
        }
        return $this->bahanBaku ? $this->bahanBaku->sku_induk : '-';
    }

    /**
     * Accessor for calculated total_stok_masuk
     */
    public function getTotalStokMasukCalculatedAttribute()
    {
        return $this->qty_barang_masuk - $this->barang_defect_tanpa_retur + $this->barang_diretur_ke_supplier;
    }

    /**
     * Accessor for percentage calculations
     */
    public function getDefectPercentageAttribute()
    {
        if ($this->qty_barang_masuk <= 0) return 0;
        return round(($this->barang_defect_tanpa_retur / $this->qty_barang_masuk) * 100, 2);
    }

    public function getReturPercentageAttribute()
    {
        if ($this->qty_pembelian <= 0) return 0;
        return round(($this->barang_diretur_ke_supplier / $this->qty_pembelian) * 100, 2);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_kedatangan_barang', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by bahan baku
     */
    public function scopeByBahanBaku($query, $bahanBakuId)
    {
        return $query->where('bahan_baku_id', $bahanBakuId);
    }

    /**
     * Boot method to auto calculate total_stok_masuk and update inventory
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($purchase) {
            $purchase->total_stok_masuk = $purchase->qty_barang_masuk - $purchase->barang_defect_tanpa_retur + $purchase->barang_diretur_ke_supplier;
        });

        static::saved(function ($purchase) {
            // Update inventory for bahan baku purchases
            if ($purchase->kategori === 'bahan_baku' && $purchase->bahan_baku_id) {
                $inventory = InventoryBahanBaku::firstOrCreate(
                    ['bahan_baku_id' => $purchase->bahan_baku_id],
                    [
                        'stok_awal' => 0,
                        'stok_masuk' => 0,
                        'terpakai' => 0,
                        'defect' => 0,
                        'live_stok_gudang' => 0,
                        'satuan' => $purchase->bahanBaku ? $purchase->bahanBaku->satuan : 'pcs'
                    ]
                );
                
                $inventory->updateStokMasukFromPurchases();
                $inventory->save();
            }
        });

        static::deleted(function ($purchase) {
            // Update inventory when purchase is deleted
            if ($purchase->kategori === 'bahan_baku' && $purchase->bahan_baku_id) {
                $inventory = InventoryBahanBaku::where('bahan_baku_id', $purchase->bahan_baku_id)->first();
                if ($inventory) {
                    $inventory->updateStokMasukFromPurchases();
                    $inventory->save();
                }
            }
        });
    }
}
