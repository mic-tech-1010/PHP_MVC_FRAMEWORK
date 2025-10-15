<?php

namespace Core\Support;

abstract class ServiceProvider {

   /**the application instance */
    protected $app;

    public function __construct($app = null) {
        $this->app = $app;
    }

    abstract public function register();

    public function boot() {

    }

}