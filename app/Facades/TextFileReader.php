<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TextFileReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TextFileReader';
    }
}
