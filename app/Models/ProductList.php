<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductList extends Model
{
    use HasFactory;

    protected $table = 'product_list';
    protected $fillable = [
        'category_id',
        'sku',
        'pack',
        'product_name',
        'supplier_id',
        'gramasi'
    ];

    /**
     * Get the category associated with this product.
     */
    public function category()
    {
        return $this->belongsTo(CategoryProduct::class, 'category_id');
    }

    /**
     * Get the supplier associated with this product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
