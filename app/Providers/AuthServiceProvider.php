<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Model::class => ModelPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }

}
