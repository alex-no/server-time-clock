<?php

namespace ServerTimeClock\Laravel;

use Illuminate\Support\ServiceProvider;
use ServerTimeClock\ServerClock;
/**
 * ServerClockServiceProvider is a Laravel service provider for the ServerClock package.
 * It registers the ServerClock class as a singleton in the Laravel service container.
 * It also publishes the configuration file for customization.
 *
 * @package ServerTimeClock\Laravel
 */
class ServerClockServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
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
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/server-clock.php' => config_path('server-clock.php'),
        ], 'config');
    }
}
