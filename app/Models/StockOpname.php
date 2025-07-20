<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'tanggal_opname',
        'status',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class, 'opname_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        $types = [
            'bahan_baku' => 'Bahan Baku',
            'finished_goods' => 'Finished Goods',
            'sticker' => 'Sticker'
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // Helper methods
    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getTotalVarianceAttribute()
    {
        return $this->items()->sum('selisih');
    }
}
