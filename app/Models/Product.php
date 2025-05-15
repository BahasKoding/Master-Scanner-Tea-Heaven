<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['id_category_product', 'sku', 'packaging', 'name_product'];

    public function categoryProduct()
    {
        return $this->belongsTo(CategoryProduct::class, 'id_category_product');
    }
}
