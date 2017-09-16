<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('guest.contact');
    }

    public function post(Request $request)
    {
        $fields = $request->validate([
           'message' => 'required',
        ]);

        $outputFilePath = storage_path('logs/feedback.log');

        file_put_contents(
            $outputFilePath,
            "<h4>" . now() . " -- " . $request->ip() . "</h4><pre>" . htmlspecialchars($fields['message']) . "</pre>\r\n\r\n<hr>",
            FILE_APPEND
        );

        return back()->with('sentMessage', true);
    }
}
