<?php

namespace App\Providers;

use Core\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
       // echo "app service provider registered . <br>";
    }

    public function boot()
    {
        //echo "app service provider booted . <br>";
    }
}
