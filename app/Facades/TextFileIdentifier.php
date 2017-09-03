<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TextFileIdentifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TextFileIdentifier';
    }
}
