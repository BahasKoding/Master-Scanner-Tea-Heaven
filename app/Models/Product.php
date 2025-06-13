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
        return $this->hasOne(FinishedGoods::class, 'product_id');
    }

    /**
     * Get the stickers records for this product.
     */
    public function stickers()
    {
        return $this->hasMany(Sticker::class, 'product_id');
    }

    /**
     * Get the main sticker record for this product (usually there's only one per product).
     */
    public function sticker()
    {
        return $this->hasOne(Sticker::class, 'product_id');
    }

    /**
     * Get all history sales that include this product (based on JSON SKU data)
     */
    public function getHistorySales()
    {
        return HistorySale::whereRaw("JSON_CONTAINS(no_sku, ?)", [json_encode($this->sku)])
            ->get();
    }

    /**
     * Get sales history with quantities for this product
     */
    public function getSalesHistory()
    {
        $historySales = HistorySale::all();
        $salesHistory = collect();

        foreach ($historySales as $sale) {
            $skuArray = is_string($sale->no_sku) ? json_decode($sale->no_sku, true) : $sale->no_sku;
            $qtyArray = is_string($sale->qty) ? json_decode($sale->qty, true) : $sale->qty;

            if (is_array($skuArray) && is_array($qtyArray)) {
                foreach ($skuArray as $index => $sku) {
                    if (trim($sku) === $this->sku) {
                        $salesHistory->push([
                            'history_sale_id' => $sale->id,
                            'no_resi' => $sale->no_resi,
                            'quantity' => $qtyArray[$index] ?? 1,
                            'created_at' => $sale->created_at,
                        ]);
                    }
                }
            }
        }

        return $salesHistory;
    }
}
