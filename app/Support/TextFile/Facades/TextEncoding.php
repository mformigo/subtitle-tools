<?php

namespace App\Support\TextFile\Facades;

use Illuminate\Support\Facades\Facade;

class TextEncoding extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\TextFile\TextEncoding::class;
    }
}
