<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Activity;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Activity List', ['only' => ['index']]);
        $this->middleware('permission:Activity View', ['only' => ['showUserActivities', 'getUserActivities']]);
    }

    /**
     * Display a listing of all activities with simple filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getActivitiesData($request);
        }

        // Get simple filter options with role-based filtering
        $categories = Activity::distinct('category')->pluck('category')->filter();

        // Get users list based on current user role
        $currentUser = Auth::user();
        $userRoles = $currentUser->roles->pluck('name');

        if ($userRoles->contains('Super Admin') || $userRoles->contains('Admin')) {
            // Super Admin and Admin can see all users in filter
            $users = User::select('id', 'name')->orderBy('name')->get();
        } else {
            // Other roles can only see themselves in filter
            $users = User::select('id', 'name')->where('id', $currentUser->id)->get();
        }

        return view('activity', compact('categories', 'users'));
    }

    /**
     * Get activities data for DataTables with optimized queries and role-based filtering
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivitiesData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';

        // Simple filters
        $category = $request->get('category');
        $userId = $request->get('user_id');
        $date = $request->get('date');

        // Get current user and their roles
        $currentUser = Auth::user();
        $userRoles = $currentUser->roles->pluck('name');

        // Optimized base query - only select needed columns
        $query = Activity::with(['user:id,name'])
            ->select([
                'id',
                'category',
                'action',
                'note',
                'user_id',
                'created_at'
            ]);

        // Apply role-based filtering
        if (!$userRoles->contains('Super Admin') && !$userRoles->contains('Admin')) {
            // Non-admin roles can only see their own activities
            $query->where('user_id', $currentUser->id);
        }

        // Apply simple filters
        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($userId)) {
            // Only apply user filter if current user has permission to see other users
            if ($userRoles->contains('Super Admin') || $userRoles->contains('Admin')) {
                $query->where('user_id', $userId);
            }
        }

        if (!empty($date)) {
            $query->whereDate('created_at', $date);
        }

        // Simple search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('category', 'LIKE', "%{$search}%")
                    ->orWhere('action', 'LIKE', "%{$search}%")
                    ->orWhere('note', 'LIKE', "%{$search}%");
            });
        }

        // Get total count efficiently with role filtering
        $totalRecordsQuery = Activity::query();
        if (!$userRoles->contains('Super Admin') && !$userRoles->contains('Admin')) {
            $totalRecordsQuery->where('user_id', $currentUser->id);
        }
        $totalRecords = $totalRecordsQuery->count();

        $filteredRecords = $query->count();

        // Apply pagination and ordering
        $activities = $query->orderBy('created_at', 'desc')
            ->offset($start)
            ->limit($length)
            ->get();

        // Format data simply
        $data = [];
        foreach ($activities as $activity) {
            $data[] = [
                'created_at' => $activity->created_at->format('Y-m-d H:i'),
                'category' => $this->getCategoryBadge($activity->category),
                'action' => $activity->action,
                'note' => $activity->note ? Str::limit($activity->note, 50) : '-',
                'user' => $activity->user ? $activity->user->name : 'System'
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Show activities for a specific user (with role-based access control)
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function showUserActivities($userId)
    {
        $currentUser = Auth::user();
        $userRoles = $currentUser->roles->pluck('name');

        // Check if user has permission to view other users' activities
        if (!$userRoles->contains('Super Admin') && !$userRoles->contains('Admin')) {
            // Non-admin users can only view their own activities
            if ($userId != $currentUser->id) {
                abort(403, 'Unauthorized access. You can only view your own activities.');
            }
        }

        $user = User::findOrFail($userId);

        $activities = Activity::where('user_id', $userId)
            ->select(['id', 'category', 'action', 'note', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user_activities', compact('user', 'activities'));
    }

    /**
     * Get activities for a specific user (AJAX) with role-based access control
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserActivities(Request $request, $userId)
    {
        $currentUser = Auth::user();
        $userRoles = $currentUser->roles->pluck('name');

        // Check if user has permission to view other users' activities
        if (!$userRoles->contains('Super Admin') && !$userRoles->contains('Admin')) {
            // Non-admin users can only view their own activities
            if ($userId != $currentUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. You can only view your own activities.'
                ], 403);
            }
        }

        $limit = $request->input('limit', 20);
        $category = $request->input('category');

        $query = Activity::where('user_id', $userId)
            ->select(['id', 'category', 'action', 'note', 'created_at'])
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $activities = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Get simple category badge HTML
     *
     * @param string $category
     * @return string
     */
    private function getCategoryBadge($category)
    {
        $badgeClass = match ($category) {
            'auth' => 'bg-warning',
            'user' => 'bg-success',
            'product' => 'bg-info',
            'sale' => 'bg-primary',
            'system' => 'bg-secondary',
            default => 'bg-dark'
        };

        return "<span class=\"badge {$badgeClass}\">{$category}</span>";
    }
}
