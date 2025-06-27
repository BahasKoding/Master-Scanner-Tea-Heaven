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
            1 => 'CRAFTED TEAS',
            2 => 'LOOSE LEAF TEA',
            3 => 'PURE TISANE',
            4 => 'DRIED FRUIT & SPICES',
            5 => 'PURE POWDER',
            6 => 'SWEET POWDER',
            7 => 'LATTE POWDER',
            8 => 'JAPANESE TEA BAGS',
            9 => 'TEAWARE',
            10 => 'ESSENCE',
            11 => 'PACKAGING- TEA HEAVEN POUCH',
            12 => 'PACKAGING- FOIL FLAT BOTTOM',
            13 => 'PACKAGING- FOIL GUSSET / SACHET',
            14 => 'PACKAGING- TRANSMETZ ZIPPER',
            15 => 'PACKAGING- VACCUM',
            16 => 'PACKAGING- TIN CANISTER',
            17 => 'BOX',
            18 => 'PRINTING & LABELLING',
            19 => 'OUTER PACKAGING',
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
