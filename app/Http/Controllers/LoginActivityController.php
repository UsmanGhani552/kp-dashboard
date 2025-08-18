<?php

namespace App\Http\Controllers;

use App\Models\LoginActivity;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LoginActivityController extends Controller
{
    public function index() {
        $loginActivities = LoginActivity::with('user')->orderBy('id', 'desc')->get()->map(function($activity){
            $activity->date = $activity->created_at->toDateString();
            $activity->time = $activity->created_at->toTimeString();
            return $activity;
        });
        return ResponseTrait::success('Login activities retrieved successfully', [
            'login_activities' => $loginActivities,
        ]);
    }
}
