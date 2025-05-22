<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorySale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_resi',
        'no_sku',
        'qty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'no_sku' => 'array',
        'qty' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the details for this history sale.
     */
    public function details()
    {
        return $this->hasMany(HistorySaleDetail::class, 'history_sale_id');
    }

    /**
     * Get all products associated with this history sale through details.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'history_sale_details', 'history_sale_id', 'product_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Helper method to get the products and quantities in a format compatible with the old system
     */
    public function getProductDetailsArrayAttribute()
    {
        $details = $this->details()->with('product')->get();

        $skus = [];
        $quantities = [];

        foreach ($details as $detail) {
            if ($detail->product) {
                $skus[] = $detail->product->sku;
                $quantities[] = $detail->quantity;
            }
        }

        return [
            'no_sku' => $skus,
            'qty' => $quantities
        ];
    }
}
