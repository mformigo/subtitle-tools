<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('guest.home');
    }

    public function contact()
    {
        return view('guest.contact');
    }
}
