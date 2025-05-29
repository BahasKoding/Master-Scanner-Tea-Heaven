<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'tanggal_kedatangan_barang' => 'date',
        'qty_pembelian' => 'integer',
        'qty_barang_masuk' => 'integer',
        'barang_defect_tanpa_retur' => 'integer',
        'barang_diretur_ke_supplier' => 'integer',
        'total_stok_masuk' => 'integer'
    ];

    /**
     * Relationship to BahanBaku
     */
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
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
     * Boot method to auto calculate total_stok_masuk
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($purchase) {
            $purchase->total_stok_masuk = $purchase->qty_barang_masuk - $purchase->barang_defect_tanpa_retur + $purchase->barang_diretur_ke_supplier;
        });
    }
}
