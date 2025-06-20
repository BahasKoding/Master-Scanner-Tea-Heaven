<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'category',
        'action',
        'action_id',
        'note',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed this activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date
     */
    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Get recent activities (untuk dashboard)
     */
    public static function getRecent($limit = 10)
    {
        return self::with(['user:id,name'])
            ->select(['id', 'category', 'action', 'note', 'user_id', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
