<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySupplier extends Model
{
    use HasFactory;
    protected $table = 'category_suppliers';
    protected $fillable = ['name'];

    /**
     * Get the suppliers for the category.
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'category_supplier_id');
    }
}
