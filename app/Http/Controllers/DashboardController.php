<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Active goals
        $activeGoals = DB::table('goals')
            ->where('user_id', $user->id)
            ->get();

        // Total savings (akumulasi dari current_amount semua goals)
        $savingTotal = $activeGoals->sum('current_amount');

        // Goals achieved
        $goalsAchieved = DB::table('goals')
            ->where('user_id', $user->id)
            ->whereColumn('current_amount', '>=', 'target_amount')
            ->count();

        // Monthly income
        $thisMonthIncome = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Total expense per category (ALL TIME)
        $spendingQuery = DB::table('transactions')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->where('user_id', $user->id)
            ->groupBy('category')
            ->get();

        // Convert to associative array: ['F&B' => 1200000, 'Transport' => 350000, ...]
        $spendingData = [];
        foreach ($spendingQuery as $row) {
            $spendingData[$row->category] = $row->total;
        }

        // Static dummy insights
        $insights = [
            [
                'title' => 'Saving Streak',
                'text' => 'Amazing! You\'ve saved consistently for 23 days straight. Keep it up!',
                'class' => 'saving-streak',
            ],
            [
                'title' => 'Investment Opportunity',
                'text' => 'Consider allocating 20% of your coffee budget to crypto investments.',
                'class' => 'investment-opportunity',
            ],
            [
                'title' => 'Goal Reminder',
                'text' => 'Your "Laptop Gaming" goal is 85% complete! Only Rp 2.25M left to achieve it.',
                'class' => 'goal-reminder',
            ],
        ];

        return view('dashboard', [
            'user' => $user,
            'savings' => $savingTotal,
            'goalsAchieved' => $goalsAchieved,
            'monthlySavings' => $thisMonthIncome,
            'spendingData' => $spendingData,
            'insights' => $insights,
        ]);
    }
}
