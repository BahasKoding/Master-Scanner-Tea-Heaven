<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Activity;
use Illuminate\Http\Request;
use App\Models\User;

class ActivityController extends Controller
{
    public function index()
    {
        return view('activity', [
            'activities' => Activity::all()
        ]);
    }

    public function loadMoreActivities(Request $request)
    {
        $activities = Activity::orderBy('created_at', 'desc')
            ->skip($request->skip)
            ->take(5)
            ->with('user') // Ensure to load the related user
            ->get();

        return response()->json($activities);
    }

    /**
     * Get authentication activities (login/logout)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthActivities(Request $request)
    {
        $skip = $request->input('skip', 0);
        $limit = $request->input('limit', 5);

        $activities = Activity::where('category', 'auth')
            ->orderBy('created_at', 'desc')
            ->skip($skip)
            ->take($limit)
            ->with('user')
            ->get();

        return response()->json($activities);
    }

    /**
     * Get activities for a specific user
     *
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserActivities(Request $request, $userId)
    {
        $skip = $request->input('skip', 0);
        $limit = $request->input('limit', 5);
        $category = $request->input('category');

        $query = Activity::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $activities = $query->skip($skip)
            ->take($limit)
            ->get();

        return response()->json($activities);
    }

    /**
     * Show the user activity page
     *
     * @param int $userId
     * @return \Illuminate\View\View
     */
    public function showUserActivities($userId)
    {
        $user = User::findOrFail($userId);

        $activities = Activity::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $authActivities = Activity::where('user_id', $userId)
            ->where('category', 'auth')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('user_activities', [
            'user' => $user,
            'activities' => $activities,
            'authActivities' => $authActivities
        ]);
    }
}
