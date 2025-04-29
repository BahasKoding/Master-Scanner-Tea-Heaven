<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Activity;
use Illuminate\Http\Request;

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
}
