<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\LoginHistory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $users = User::all();
        $loginHistories = LoginHistory::with('user')->latest('logged_in_at')->take(20)->get();
        $activityLogs = ActivityLog::latest()->take(20)->get();
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('settings', [
            'users'          => $users,
            'loginHistories' => $loginHistories,
            'activityLogs'   => $activityLogs,
            'settings'       => $settings,
        ]);
    }
}
