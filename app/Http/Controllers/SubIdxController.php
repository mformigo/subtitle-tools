<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubIdxController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'swap-sub-and-idx',
        //    'convert-text-files-to-utf8:idx',
        ])->only('post');
    }

    public function index()
    {
        return view('sub-idx');
    }

    public function post(Request $request)
    {
        dd($request->files);
    }
}
