<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_bakus';

    protected $fillable = [
        'kategori',
        'sku_induk',
        'nama_barang',
        'satuan'
    ];

    /**
     * Relationship dengan InventoryBahanBaku
     */
    public function inventory()
    {
        return $this->hasOne(InventoryBahanBaku::class, 'bahan_baku_id');
    }

    /**
     * Relationship dengan Purchase (bahan baku purchases)
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'bahan_baku_id')
                    ->where('kategori', 'bahan_baku');
    }

    /**
     * Get category options for dropdown
     */
    public static function getCategoryOptions()
    {
        return [
            1 => 'Tea Leaves & Herbs',
            2 => 'Powder & Blends',
            3 => 'Flavoring & Additives',
            4 => 'Packaging Materials',
            5 => 'Sweeteners',
            6 => 'Others'
        ];
    }

    /**
     * Get the catatan produksi records that use this bahan baku.
     * This returns a collection of CatatanProduksi models where this bahan baku is used.
     */
    public function catatanProduksi()
    {
        return CatatanProduksi::whereJsonContains('sku_induk', $this->id)->get();
    }

    /**
     * Get the full name of this bahan baku (SKU + Name)
     */
    public function getFullNameAttribute()
    {
        return $this->sku_induk . ' - ' . $this->nama_barang;
    }

    /**
     * Get category name for this bahan baku
     */
    public function getCategoryNameAttribute()
    {
        $categories = self::getCategoryOptions();
        return $categories[$this->kategori] ?? 'Unknown Category';
    }

    /**
     * Get current inventory stock
     */
    public function getCurrentStock()
    {
        return $this->inventory ? $this->inventory->live_stok_gudang : 0;
    }

    /**
     * Check if stock is low (<=10)
     */
    public function isLowStock($threshold = 10)
    {
        return $this->getCurrentStock() <= $threshold;
    }
}
