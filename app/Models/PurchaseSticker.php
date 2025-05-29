<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ukuran_stiker',
        'jumlah_stiker',
        'jumlah_order',
        'stok_masuk',
        'total_order',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the percentage of stok masuk vs total order
     */
    public function getStokMasukPercentageAttribute()
    {
        if ($this->total_order == 0) {
            return 0;
        }
        return round(($this->stok_masuk / $this->total_order) * 100, 2);
    }

    /**
     * Get the percentage of jumlah order vs jumlah stiker
     */
    public function getOrderPercentageAttribute()
    {
        if ($this->jumlah_stiker == 0) {
            return 0;
        }
        return round(($this->jumlah_order / $this->jumlah_stiker) * 100, 2);
    }

    /**
     * Accessor for calculating total_order automatically
     * Formula: jumlah_order (if not manually set)
     */
    public function getTotalOrderCalculatedAttribute()
    {
        return $this->total_order ?: $this->jumlah_order;
    }

    /**
     * Scope for filtering by product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope for filtering by ukuran stiker
     */
    public function scopeByUkuran($query, $ukuran)
    {
        return $query->where('ukuran_stiker', 'like', "%{$ukuran}%");
    }
}
