<?php

namespace App\Http\Controllers;

class NotFoundController extends Controller
{
    public function index()
    {
        return response()->view('errors.404')->setStatusCode(404);
    }
}
