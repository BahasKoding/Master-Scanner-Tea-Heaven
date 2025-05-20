<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CatatanProduksi extends Model
{
    use HasFactory;
    protected $table = 'catatan_produksis';

    protected $fillable = [
        'product_id',
        'packaging',
        'quantity',
        'sku_induk',
        'gramasi',
        'total_terpakai'
    ];

    protected $casts = [
        'sku_induk' => 'array',
        'gramasi' => 'array',
        'total_terpakai' => 'array',
        'quantity' => 'integer'
    ];

    /**
     * Get the product that owns the catatan produksi.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the bahan baku items for this catatan produksi.
     */
    public function bahanBaku()
    {
        // Here we return a collection of BahanBaku based on the IDs stored in sku_induk array
        return BahanBaku::whereIn('id', $this->sku_induk ?? [])->get();
    }

    /**
     * Get details of bahan baku with gramasi and total_terpakai
     */
    public function getBahanBakuDetailsAttribute()
    {
        $result = [];
        if (is_array($this->sku_induk)) {
            $bahanBakuItems = BahanBaku::whereIn('id', $this->sku_induk)->get()->keyBy('id');

            foreach ($this->sku_induk as $index => $bahanId) {
                if (isset($bahanBakuItems[$bahanId])) {
                    $bahan = $bahanBakuItems[$bahanId];
                    $result[] = [
                        'id' => $bahan->id,
                        'sku_induk' => $bahan->sku_induk,
                        'nama_barang' => $bahan->nama_barang,
                        'satuan' => $bahan->satuan,
                        'gramasi' => $this->gramasi[$index] ?? 0,
                        'total_terpakai' => $this->total_terpakai[$index] ?? 0
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get details of bahan baku with gramasi and total_terpakai as a collection
     * This provides access to collection methods like sum(), avg(), etc.
     */
    public function getBahanBakuCollectionAttribute()
    {
        return new Collection($this->bahan_baku_details);
    }

    /**
     * Get an array of bahan baku names based on the sku_induk IDs
     */
    public function getBahanBakuNamesAttribute()
    {
        $bahanBakuItems = $this->bahanBaku();
        return $bahanBakuItems->pluck('nama_barang')->toArray();
    }

    /**
     * Get an array of bahan baku SKUs based on the sku_induk IDs
     */
    public function getBahanBakuSkusAttribute()
    {
        $bahanBakuItems = $this->bahanBaku();
        return $bahanBakuItems->pluck('sku_induk')->toArray();
    }

    /**
     * Get an array of bahan baku full names (SKU + Name)
     */
    public function getBahanBakuFullNamesAttribute()
    {
        $bahanBakuItems = $this->bahanBaku();
        return $bahanBakuItems->map(function ($item) {
            return $item->sku_induk . ' - ' . $item->nama_barang;
        })->toArray();
    }

    /**
     * Get sku attribute from related product
     */
    public function getSkuProductAttribute()
    {
        return $this->product ? $this->product->sku : null;
    }

    /**
     * Get name_product attribute from related product
     */
    public function getNamaProductAttribute()
    {
        return $this->product ? $this->product->name_product : null;
    }

    /**
     * Get total of all terpakai values
     */
    public function getTotalTerpakaiSumAttribute()
    {
        if (!is_array($this->total_terpakai)) {
            return $this->total_terpakai;
        }

        return array_sum($this->total_terpakai);
    }

    /**
     * Get total of all gramasi values
     */
    public function getTotalGramasiSumAttribute()
    {
        if (!is_array($this->gramasi)) {
            return $this->gramasi;
        }

        return array_sum($this->gramasi);
    }

    /**
     * Validate if arrays have matching lengths
     * Returns true if all arrays (sku_induk, gramasi, total_terpakai) have the same length
     */
    public function validateArrayLengths()
    {
        if (!is_array($this->sku_induk) || !is_array($this->gramasi) || !is_array($this->total_terpakai)) {
            return false;
        }

        $skuLength = count($this->sku_induk);
        return $skuLength === count($this->gramasi) && $skuLength === count($this->total_terpakai);
    }

    /**
     * Calculate if total_terpakai values are correct based on gramasi and quantity
     * Returns true if all total_terpakai values match the expected calculation
     */
    public function validateTotalTerpakaiCalculations()
    {
        if (!$this->validateArrayLengths()) {
            return false;
        }

        for ($i = 0; $i < count($this->sku_induk); $i++) {
            $expectedTotal = $this->gramasi[$i] * $this->quantity;
            $actualTotal = $this->total_terpakai[$i];

            // Allow a small difference for floating point precision
            if (abs($expectedTotal - $actualTotal) > 0.01) {
                return false;
            }
        }

        return true;
    }
}
