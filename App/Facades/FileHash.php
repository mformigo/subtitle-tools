<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FileHash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'FileHash';
    }
}
