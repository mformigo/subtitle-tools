<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FileName extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileName';
    }
}
