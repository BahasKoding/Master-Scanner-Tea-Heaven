<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;

    protected $table = 'category_products';
    protected $fillable = ['name', 'label_id'];

    /**
     * Get the label associated with this category product.
     */
    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }

    /**
     * Get the products that belong to this category.
     */
    public function products()
    {
        return $this->hasMany(ProductList::class, 'category_id');
    }
}
