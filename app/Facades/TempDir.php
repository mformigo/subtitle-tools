<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TempDir extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TempDir';
    }
}
