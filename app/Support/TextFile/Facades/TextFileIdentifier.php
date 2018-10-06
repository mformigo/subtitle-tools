<?php

namespace App\Support\TextFile\Facades;

use Illuminate\Support\Facades\Facade;

class TextFileIdentifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Support\TextFile\TextFileIdentifier::class;
    }
}
