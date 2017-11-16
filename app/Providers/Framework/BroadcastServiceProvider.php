<?php

namespace App\Providers\Framework;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Broadcast::routes();
    }
}
