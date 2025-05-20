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
}
