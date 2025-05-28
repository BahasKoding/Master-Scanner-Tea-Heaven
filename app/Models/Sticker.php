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
     * Get size options for stickers
     */
    public static function getSizeOptions()
    {
        return [
            'small' => 'Small',
            'medium' => 'Medium',
            'large' => 'Large'
        ];
    }

    /**
     * Get products that are eligible for stickers (specific labels only)
     */
    public static function getEligibleProducts()
    {
        // Labels: 1 = EXTRA SMALL PACK, 2 = SMALL PACK, 5 = TIN CANISTER SERIES
        return Product::whereIn('label', [1, 2, 5])->get();
    }
}
