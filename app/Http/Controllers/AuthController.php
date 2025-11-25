<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $user = User::where('nrp', $studentId)->first();

        if ($user && Hash::check($password, $user->password)) {
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
