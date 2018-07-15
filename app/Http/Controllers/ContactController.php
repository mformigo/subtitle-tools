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
            'mg' => 'required',
        ]);

        // These strange input names are an attempt to prevent spam bots.
        $message = $request->get('mg');
        $email   = $request->get('em') ?? '(none)';

        file_put_contents(
            storage_path('logs/feedback.log'),
            '<h4>'.now().' -- '.$request->ip().'</h4><pre>email: '.e($email)."\r\n\r\n".e($message)."</pre>\r\n\r\n<hr>",
            FILE_APPEND
        );

        return view('contact')->with('sentMessage', true);
    }
}
