<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        $user = User::where('student_id', $studentId)->first();

        if ($user && $user->password === $password) {
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
