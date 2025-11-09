<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAdvisorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Dummy forecast and insight data, replace with DB::table()->get() if needed
        $forecasts = [
            ['label' => 'Monthly Savings', 'current' => 2500000, 'forecast' => 2650000],
            ['label' => 'Food Expenses', 'current' => 1200000, 'forecast' => 1100000],
            ['label' => 'Transportation', 'current' => 450000, 'forecast' => 520000],
        ];

        return view('ai-advisor', [
            'user' => $user,
            'forecasts' => $forecasts
        ]);
    }
}
