<?php

namespace App\Http\Controllers;

use App\Models\SavedDashboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'slug' => 'required|alpha_dash|max:50',
            'usernames' => 'required|string'
        ]);

        SavedDashboard::updateOrCreate(
            ['slug' => strtolower($request->slug)],
            ['usernames' => $request->usernames]
        );

        return response()->json([
            'success' => true,
            'url' => url('/' . strtolower($request->slug))
        ]);
    }

    public function view($slug)
    {
        $dashboard = SavedDashboard::where('slug', $slug)->firstOrFail();

        return view('welcome', [
            'savedUsernames' => $dashboard->usernames
        ]);
    }
}
