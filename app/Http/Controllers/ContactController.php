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
        ]);

        $outputFilePath = storage_path('logs/feedback.log');

        $email = $request->get('email') ?? '(none)';

        file_put_contents(
            $outputFilePath,
            '<h4>'.now().' -- '.$request->ip().'</h4><pre>email: '.e($email)."\r\n\r\n".e($request->get('message'))."</pre>\r\n\r\n<hr>",
            FILE_APPEND
        );

        return view('contact')->with('sentMessage', true);
    }
}
