<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'suppliers';
    protected $fillable = [
        'category_supplier_id',
        'code',
        'product_name',
        'unit'
    ];

    /**
     * Get the category supplier that owns the supplier.
     */
    public function categorySupplier()
    {
        return $this->belongsTo(CategorySupplier::class, 'category_supplier_id');
    }
}
