<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $transactions = DB::table('transactions')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $totalIncome = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = DB::table('transactions')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->sum('amount');

        $totalTransaction = $transactions->count();

        $averageDaily = DB::table('transactions')
            ->where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->selectRaw('SUM(amount)/DAY(LAST_DAY(NOW())) AS avg')
            ->value('avg');

        return view('transaction', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'totalTransaction',
            'averageDaily',
            'user'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        DB::table('transactions')->insert([
            'user_id' => $user->id,
            'description' => $request->description,
            'type' => $request->type, // income/expense
            'category' => $request->category,
            'amount' => $request->amount,
            'status' => 'completed',
            'created_at' => now(),
        ]);

        return redirect('/transactions');
    }

    public function edit($id)
    {
        $user = session('user');
        $transaction = DB::table('transactions')->where('id', $id)->where('user_id', $user->id)->first();
        return view('edit_transaction', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        DB::table('transactions')->where('id', $id)->update([
            'title' => $request->title,
            'type' => $request->type,
            'category' => $request->category,
            'amount' => $request->amount,
            'updated_at' => now()
        ]);

        return redirect('/transaction');
    }

    public function destroy($id)
    {
        DB::table('transactions')->where('id', $id)->delete();
        return redirect('/transaction');
    }
}
