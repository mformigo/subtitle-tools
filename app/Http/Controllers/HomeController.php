<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('guest.home');
    }

    public function blogVlcSubtitleBoxes()
    {
        return view('guest.blog.fix-vlc-subtitle-boxes');
    }
}
