<?php

namespace App\Http\Controllers\Admin;

class ShowPhpInfo extends Controller
{
    public function __invoke()
    {
        phpinfo();
    }
}
