<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubIdxController extends Controller
{
    public function index()
    {
        return view('sub-idx');
    }

    public function post(Request $request)
    {
        dd($request->files);
    }
}
