<?php

namespace App\Support\TextFile\Facades;

use Illuminate\Support\Facades\Facade;

class TextFileReader extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\TextFile\TextFileReader::class;
    }
}
