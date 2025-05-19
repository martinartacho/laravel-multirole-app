<?php

namespace Modules\Example\Providers;

use Illuminate\Support\ServiceProvider;

class ExampleServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../Views', 'example');
        $this->loadMigrationsFrom(__DIR__.'/../../Database/Migrations');
    }
}