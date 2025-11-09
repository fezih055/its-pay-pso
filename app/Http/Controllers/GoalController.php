<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    //Yang asli before adding the filtering 
    // public function index()
    // {
    //     $user = Auth::user();
    //     $goals = DB::table('goals')
    //         ->where('user_id', $user->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('goals', compact('user', 'goals'));
    // }

    //The newest code for adding filtering 
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DB::table('goals')->where('user_id', $user->id);

        // jika ada filter tanggal
        if ($request->start && $request->end) {
            $query->whereBetween('deadline', [$request->start, $request->end]);
        }

        // urutkan dari deadline terdekat
        $goals = $query->orderByDesc('deadline')->get();

        return view('goals', compact('user', 'goals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $category = $request->input('category');
        $priority = match ($category) {
            'Need' => 'High',
            'Want' => 'Low',
            'Saving' => 'Medium',
            default => 'Medium'
        };

        $saving = $request->input('saving_amount');
        $goalId = DB::table('goals')->insertGetId([
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'category' => $category,
            'priority' => $priority,
            'target_amount' => $request->input('target_amount'),
            'current_amount' => $saving,
            'deadline' => $request->input('deadline'),  // <--- TAMBAH INI BUAT DEADLINE 
            'created_at' => now()
        ]);

        // Catat saving sebagai transaksi
        if ($saving > 0) {
            DB::table('transactions')->insert([
                'user_id' => $user->id,
                'goal_id' => $goalId,
                'type' => 'income',
                'category' => 'Saving Contribution',
                'amount' => $saving,
                'description' => 'Initial saving for goal: ' . $request->input('title'),
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect('/goals')->with('success', 'Goal and saving recorded.');
    }



    public function edit($id)
    {
        $goal = DB::table('goals')->where('id', $id)->where('user_id', Auth::id())->first();
        return view('edit-goal', compact('goal'));
    }

    public function update(Request $request, $id)
    {
        DB::table('goals')->where('id', $id)->update([
            'title' => $request->title,
            'category' => $request->category,
            'priority' => $request->priority,
            'target_amount' => $request->target_amount,
            'deadline' => $request->deadline,  // <--- TAMBAH INI BUAT DEADLINE 
        ]);

        return redirect('/goals')->with('success', 'Goal updated successfully!');
    }


    public function destroy($id)
    {
        DB::table('goals')->where('id', $id)->where('user_id', Auth::id())->delete();
        return redirect('/goals');
    }
}
