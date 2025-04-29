<?php

use App\Models\Backend\Activity;
use Illuminate\Support\Facades\Auth;

function addActivity($category, $action, $note, $action_id)
{
    $activity = new Activity();
    $activity->category = $category;
    $activity->note = $note;
    $activity->action = $action;
    $activity->action_id = $action_id;
    $activity->user_id = getCurrentUserId();
    $activity->save();
    return $activity;
}

function getCurrentUserId()
{
    return Auth::check() ? Auth::user()->id : 1;
}
