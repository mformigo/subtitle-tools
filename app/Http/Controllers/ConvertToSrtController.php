<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConvertToSrtController extends Controller
{
    public function index()
    {
        return view('convert-to-srt-index');
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'subtitle' => 'required|file|textfile',
        ]);

        dd($request);
    }
}
