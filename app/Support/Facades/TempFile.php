<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

class TempFile extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TempFile';
    }
}
