<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['category_product', 'sku', 'packaging', 'name_product'];

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
     * Get category name based on category integer
     */
    public function getCategoryNameAttribute()
    {
        $categories = self::getCategoryOptions();
        return $categories[$this->category_product] ?? 'Unknown Category';
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
