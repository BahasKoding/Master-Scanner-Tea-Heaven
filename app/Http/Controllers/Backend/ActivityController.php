<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Activity;
use Illuminate\Http\Request;
use App\Models\User;

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

        // Get simple filter options
        $categories = Activity::distinct('category')->pluck('category')->filter();
        $users = User::select('id', 'name')->orderBy('name')->get();

        return view('activity', compact('categories', 'users'));
    }

    /**
     * Get activities data for DataTables with optimized queries
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

        // Apply simple filters
        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($userId)) {
            $query->where('user_id', $userId);
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

        // Get total count efficiently
        $totalRecords = Activity::count();
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
                'note' => $activity->note ? \Str::limit($activity->note, 50) : '-',
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
     * Show activities for a specific user
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function showUserActivities($userId)
    {
        $user = User::findOrFail($userId);

        $activities = Activity::where('user_id', $userId)
            ->select(['id', 'category', 'action', 'note', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user_activities', compact('user', 'activities'));
    }

    /**
     * Get activities for a specific user (AJAX)
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserActivities(Request $request, $userId)
    {
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
