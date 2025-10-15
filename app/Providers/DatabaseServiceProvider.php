<?php

namespace App\Providers;

use Core\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        // // Initialize services
        // Database::configure(self::$config);
        // Database::init();
    }

    public function boot()
    {
      //  echo "database service provider booted . <br>";
    }
}
