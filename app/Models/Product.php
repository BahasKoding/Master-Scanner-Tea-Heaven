<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['category_product', 'sku', 'packaging', 'name_product', 'label'];

    /**
     * Mapping of category integers to category names
     */
    public static function getCategoryOptions()
    {
        return [
            1 => 'CLASSIC TEA COLLECTION',
            2 => 'PURE TISANE',
            3 => 'ARTISAN TEA',
            4 => 'JAPANESE TEA',
            5 => 'CHINESE TEA',
            6 => 'PURE POWDER',
            7 => 'SWEET POWDER',
            8 => 'LATTE POWDER',
            9 => 'CRAFTED TEAS',
            10 => 'JAPANESE TEABAGS',
            11 => 'TEA WARE',
        ];
    }

    /**
     * Get label name based on label integer
     */
    public static function getLabelOptions()
    {
        return [
            0 => '-',
            1 => 'EXTRA SMALL PACK (15-100 GRAM)',
            2 => 'SMALL PACK (50-250 GRAM)',
            3 => 'MEDIUM PACK (500 GRAM)',
            4 => 'BIG PACK (1 Kg)',
            5 => 'TIN CANISTER SERIES',
            6 => 'REFILL PACK, SAMPLE & GIFT',
            7 => 'CRAFTED TEAS',
            8 => 'JAPANESE TEABAGS',
            9 => 'TEA WARE',
            10 => 'NON LABEL 500 GR-1000 GR',
        ];
    }
    /**
     * Get category name based on category integer
     */
    public function getCategoryNameAttribute()
    {
        $categories = self::getCategoryOptions();
        return $categories[$this->category_product] ?? 'Unknown Category';
    }

    /**
     * Get label name based on label integer
     */
    public function getLabelNameAttribute()
    {
        $labels = self::getLabelOptions();
        return $labels[$this->label] ?? '-';
    }

    /**
     * Get the catatan produksi records for this product.
     */
    public function catatanProduksi()
    {
        return $this->hasMany(CatatanProduksi::class, 'product_id');
    }

    /**
     * Get the finished goods records for this product.
     */
    public function finishedGoods()
    {
        return $this->hasOne(FinishedGoods::class, 'id_product');
    }

    /**
     * Get all history sales that include this product.
     */
    public function historySales()
    {
        return $this->belongsToMany(HistorySale::class, 'history_sale_details', 'product_id', 'history_sale_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get all history sale details for this product.
     */
    public function historySaleDetails()
    {
        return $this->hasMany(HistorySaleDetail::class, 'product_id');
    }
}
