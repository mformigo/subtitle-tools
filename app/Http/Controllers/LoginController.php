<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('login');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('admin.dashboard.index');
    }

    public function username()
    {
        return 'username';
    }
}
