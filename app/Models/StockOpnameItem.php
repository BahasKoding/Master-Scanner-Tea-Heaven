<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_id',
        'item_id',
        'item_name',
        'item_sku',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'satuan',
        'notes'
    ];

    protected $casts = [
        'stok_sistem' => 'integer',
        'stok_fisik' => 'integer',
        'selisih' => 'integer',
    ];

    // Relationships
    public function opname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class, 'opname_id');
    }

    // Dynamic relationships based on opname type
    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(BahanBaku::class, 'item_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'item_id');
    }


    // Mutators
    public function setStokFisikAttribute($value)
    {
        $this->attributes['stok_fisik'] = $value;
        // Auto calculate selisih when stok_fisik is set
        if ($value !== null && isset($this->attributes['stok_sistem'])) {
            $this->attributes['selisih'] = $this->calculateCorrectVariance($value, $this->attributes['stok_sistem']);
        }
    }

    // Accessors
    public function getMasterItemNameAttribute()
    {
        if (!$this->opname) {
            return $this->item_name; // fallback to stored name
        }

        try {
            switch ($this->opname->type) {
                case 'bahan_baku':
                    // Try to get from BahanBaku table using item_id
                    $bahanBaku = BahanBaku::find($this->item_id);
                    return $bahanBaku->nama_barang ?? $this->item_name;
                    
                case 'finished_goods':
                    // Try to get from Product table using item_id
                    $product = Product::find($this->item_id);
                    return $product->name_product ?? $this->item_name;
                    
                default:
                    return $this->item_name;
            }
        } catch (\Exception $e) {
            // Return fallback on any error
            return $this->item_name . ' (Error: ' . $e->getMessage() . ')';
        }
    }

    public function getMasterItemSkuAttribute()
    {
        // Return stored SKU if available
        if ($this->item_sku) {
            return $this->item_sku;
        }

        if (!$this->opname) {
            return '-'; // fallback
        }

        try {
            switch ($this->opname->type) {
                case 'bahan_baku':
                    // Try to get from BahanBaku table using item_id
                    $bahanBaku = BahanBaku::find($this->item_id);
                    return $bahanBaku->sku_induk ?? '-';
                    
                case 'finished_goods':
                    // Try to get from Product table using item_id
                    $product = Product::find($this->item_id);
                    return $product->sku ?? '-';
                    
                default:
                    return '-';
            }
        } catch (\Exception $e) {
            // Return fallback on any error
            return '-';
        }
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->stok_sistem == 0) {
            return $this->stok_fisik > 0 ? 100 : 0;
        }
        
        return round(($this->selisih / $this->stok_sistem) * 100, 2);
    }

    public function getVarianceStatusAttribute()
    {
        if ($this->selisih > 0) {
            return 'surplus';
        } elseif ($this->selisih < 0) {
            return 'shortage';
        } else {
            return 'match';
        }
    }

    public function getVarianceStatusColorAttribute()
    {
        switch ($this->variance_status) {
            case 'surplus':
                return 'success';
            case 'shortage':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    // Helper methods
    public function calculateSelisih()
    {
        if ($this->stok_fisik !== null) {
            $this->selisih = $this->calculateCorrectVariance($this->stok_fisik, $this->stok_sistem);
            return $this->selisih;
        }
        return 0;
    }

    /**
     * Calculate variance using standard formula: Physical Stock - System Stock
     * 
     * This provides consistent and intuitive variance calculation:
     * - Positive variance = Surplus (physical > system)
     * - Negative variance = Shortage (physical < system)
     * - Zero variance = Match (physical = system)
     * 
     * @param int $physicalStock
     * @param int $systemStock
     * @return int
     */
    private function calculateCorrectVariance($physicalStock, $systemStock)
    {
        // Convert to integers to ensure proper calculation
        $physicalStock = (int) $physicalStock;
        $systemStock = (int) $systemStock;
        
        // Standard variance calculation: Physical - System
        return $physicalStock - $systemStock;
    }
}
