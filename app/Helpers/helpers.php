<?php

use App\Models\Backend\Activity;
use Illuminate\Support\Facades\Auth;

/**
 * Add an activity record to the database
 *
 * @param string $category Activity category (e.g., 'user', 'auth', 'system')
 * @param string $action The action performed (e.g., 'create', 'update', 'delete', 'login', 'logout')
 * @param string $note Additional details about the activity
 * @param int|null $actionId The ID of the affected record (optional)
 * @return \App\Models\Backend\Activity|null
 */
if (!function_exists('addActivity')) {
    function addActivity($category, $action, $note = '', $actionId = null)
    {
        try {
            return Activity::create([
                'category' => $category,
                'action' => $action,
                'action_id' => $actionId,
                'note' => $note,
                'user_id' => Auth::id()
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail app execution
            \Illuminate\Support\Facades\Log::error('Failed to add activity: ' . $e->getMessage());
            return null;
        }
    }
}

function getCurrentUserId()
{
    return Auth::check() ? Auth::user()->id : 1;
}
