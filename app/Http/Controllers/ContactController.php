<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function post(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'captcha' => 'required|numeric|regex:/^6$/',
        ]);

        $message = $request->get('message');
        $email   = $request->get('email') ?? '(none)';

        file_put_contents(
            storage_path('logs/feedback.log'),
            '<strong>'.now()->format('l, \t\h\e jS \\of F \a\t H:i')."</strong><br>".$request->ip().'<br><p>email: '.e($email)."<br><br>".e($message)."</p><br><br>",
            FILE_APPEND
        );

        return view('contact')->with('sentMessage', true);
    }
}
