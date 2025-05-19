<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanProduksi extends Model
{
    use HasFactory;
    protected $table = 'catatan_produksis';

    protected $fillable = [
        'sku_product',
        'nama_product',
        'packaging',
        'quantity',
        'sku_induk',
        'gramasi',
        'total_terpakai'
    ];

    protected $casts = [
        'sku_induk' => 'array',
        'gramasi' => 'array',
        'quantity' => 'integer'
    ];
}
