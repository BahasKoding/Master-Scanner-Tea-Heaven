<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ukuran',
        'jumlah',
        'stok_awal',
        'stok_masuk',
        'produksi',
        'defect',
        'sisa',
        'status'
    ];

    /**
     * Get the product that owns the sticker.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get status options for stickers
     */
    public static function getStatusOptions()
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'out_of_stock' => 'Out of Stock'
        ];
    }

    /**
     * Get the ukuran sticker options
     */
    public static function getUkuranSticker()
    {
        return [
            1 => "5 X 17",
            2 => "11.5 X 5.7",
            3 => "13 X 5",
            4 => "10 X 3"
        ];
    }

    /**
     * Get products that are eligible for stickers (specific labels only)
     */
    public static function getEligibleProducts()
    {
        // Labels: 1 = EXTRA SMALL PACK, 5 = TIN CANISTER SERIES
        return Product::whereIn('label', [1, 5])->get();
    }
}
