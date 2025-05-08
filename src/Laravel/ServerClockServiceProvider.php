<?php

namespace ServerTimeClock\Laravel;

use Illuminate\Support\ServiceProvider;
use ServerTimeClock\ServerClock;

class ServerClockServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/server-clock.php', 'server-clock');

        $this->app->singleton(ServerClock::class, function ($app) {
            $config = config('server-clock', []);
            return new ServerClock($config);
        });
    }

    /**
     * Publish config file for customization.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/server-clock.php' => config_path('server-clock.php'),
        ], 'config');
    }
}
