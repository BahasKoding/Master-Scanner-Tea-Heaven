<?php

namespace App\Helpers;

use App\Models\Backend\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log activity dengan cara yang sederhana
     */
    public static function log($category, $action, $note = null, $userId = null)
    {
        try {
            Activity::create([
                'category' => $category,
                'action' => $action,
                'note' => $note,
                'user_id' => $userId ?? Auth::id(),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai error logging mengganggu aplikasi utama
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Log authentication activities
     */
    public static function auth($action, $note = null)
    {
        self::log('auth', $action, $note);
    }

    /**
     * Log user activities
     */
    public static function user($action, $note = null)
    {
        self::log('user', $action, $note);
    }

    /**
     * Log product activities
     */
    public static function product($action, $note = null)
    {
        self::log('product', $action, $note);
    }

    /**
     * Log sale activities
     */
    public static function sale($action, $note = null)
    {
        self::log('sale', $action, $note);
    }

    /**
     * Log system activities
     */
    public static function system($action, $note = null)
    {
        self::log('system', $action, $note);
    }
}
