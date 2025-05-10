<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $table = 'labels';
    protected $fillable = ['name'];

    /**
     * Get the category products that have this label.
     */
    public function categoryProducts()
    {
        return $this->hasMany(CategoryProduct::class, 'label_id');
    }
}
