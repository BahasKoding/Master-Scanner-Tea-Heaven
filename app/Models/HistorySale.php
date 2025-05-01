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
}
