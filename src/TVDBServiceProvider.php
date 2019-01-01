<?php

namespace musa11971\TVDB;

use Illuminate\Support\ServiceProvider;

class TVDBServiceProvider extends ServiceProvider
{
    public function boot() {
        // Publish config file
        $this->publishes([
            __DIR__ . '/config/tvdb.php' => config_path('tvdb.php')
        ]);
    }

    public function register() {}
}