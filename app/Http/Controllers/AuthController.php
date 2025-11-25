<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $studentId = $request->input('student_id');
        $password = $request->input('password');

        $user = User::where('nrp', $studentId)->where('password', $password)->first();

        if ($user) {
            Auth::login($user);
            return redirect('/home');
        }

        return redirect()->back()->with('error', 'Invalid NRP or password.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
