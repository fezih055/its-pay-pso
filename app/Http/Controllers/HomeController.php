<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeGoals = DB::table('goals')
            ->where('user_id', $user->id)
            ->select('id', 'title', 'category', 'priority', 'target_amount', 'current_amount', 'deadline')
            ->orderBy('deadline')
            ->get();

        $savingTotal = $activeGoals->sum('current_amount');
        $activeCount = $activeGoals->count();

        $totalProgress = 0;
        foreach ($activeGoals as $goal) {
            if ($goal->target_amount > 0) {
                $totalProgress += ($goal->current_amount / $goal->target_amount);
            }
        }

        $achievementPercent = $activeCount > 0 ? round(($totalProgress / $activeCount) * 100) : 0;
        $weeklyBonus = '+15%';

        return view('home', [
            'user' => $user,
            'activeGoals' => $activeGoals,
            'activeCount' => $activeCount,
            'savingTotal' => $savingTotal,
            'achievementPercent' => $achievementPercent,
            'weeklyBonus' => $weeklyBonus,
        ]);
    }
}
