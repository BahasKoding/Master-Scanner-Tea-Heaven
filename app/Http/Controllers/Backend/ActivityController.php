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
     * Display a listing of all activities.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $activities = Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('backend.activity', [
            'activities' => $activities
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
        $user = User::with('roles')->findOrFail($userId);

        $activities = Activity::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('user_activities', [
            'user' => $user,
            'activities' => $activities
        ]);
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
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $activities = $query->take($limit)->get();

        return response()->json($activities);
    }
}
