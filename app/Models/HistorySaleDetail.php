<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorySaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_sale_id',
        'product_id',
        'quantity'
    ];

    /**
     * Get the history sale that owns the detail.
     */
    public function historySale()
    {
        return $this->belongsTo(HistorySale::class, 'history_sale_id');
    }

    /**
     * Get the product associated with this detail.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
