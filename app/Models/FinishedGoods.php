<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGoods extends Model
{
    use HasFactory;
    protected $table = 'finished_goods';
    protected $fillable = [
        'id_product',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'defective',
        'live_stock'
    ];

    /**
     * Get the product that owns the finished goods
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
