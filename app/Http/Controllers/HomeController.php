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

    public function blogVlcSubtitleBoxes()
    {
        return view('guest.blog.fix-vlc-subtitle-boxes');
    }
}
