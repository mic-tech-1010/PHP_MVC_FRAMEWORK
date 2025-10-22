<?php

namespace Core;

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Database;
use Core\Http\Request;
use Core\Route\Route;
use Core\View\View;

class App
{
    protected static $providers = [];
    protected static $config = [];
    public static Request $request;
    protected static $instance;
    protected static $basePath;


    public static function boot(?string $basePath = null)
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        // Normalize the base path (convert to absolute and remove "..")
        $resolvedBase = realpath($basePath ?? dirname(__DIR__) . '/public');

        if (!$resolvedBase) {
            throw new \Exception("Base path could not be resolved.");
        }

        self::$basePath = rtrim($resolvedBase, '/\\');

        //load configuration
        self::$config['app'] = require __DIR__ . '/../config/app.php';
        self::$config['database'] = require __DIR__ . '/../config/database.php';

        // Initialize services
        /**database */
        Database::configure(self::$config['database']);
        Database::init();

        /**request */
        self::$request = new Request();

        /**route */
        $route = new Route(self::$request);


        /** View */
        View::setBasePath(__DIR__ . '/../resources/views');

        //load Providers list
        $providers = require __DIR__ . '/../bootstrap/providers.php';

        //register and boot each provider
        foreach ($providers as $providerClass) {
            $provider = new $providerClass(new static());

            if (method_exists($provider, 'register')) {
                $provider->register();
            }

            self::$providers[] = $provider;
        }

        //boot all providers (after registration)
        foreach (self::$providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }

        return self::$instance;
    }

    public static function basePath(string $path = '')
    {
        return self::$basePath . ($path ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
    }
}
