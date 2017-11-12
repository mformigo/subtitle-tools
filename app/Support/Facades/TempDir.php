<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;

class TempDir extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TempDir';
    }
}
