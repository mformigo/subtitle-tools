<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TextEncoding extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TextEncoding';
    }
}
