<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'recentLogs' => $user->authLogs()->latest()->limit(5)->get(),
        ]);
    }
}
