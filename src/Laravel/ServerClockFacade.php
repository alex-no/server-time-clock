<?php

namespace ServerTimeClock\Laravel;

use Illuminate\Support\Facades\Facade;
use ServerTimeClock\ServerClock;
/**
 * ServerClockFacade is a facade for the ServerClock class.
 * It provides a static interface to access the server clock functionality.
 *
 * @method static \DateTimeImmutable now() Get the current server time as a DateTimeImmutable object.
 * @method static \DateTimeZone getTimezone() Get the timezone information as a DateTimeZone object.
 * @method static string getClientName() Get the name of the client used for fetching time data.
 * @method static array getConfig() Get the configuration array used by the server clock.
 */
class ServerClockFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ServerClock::class;
    }
}
