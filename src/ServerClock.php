<?php

namespace ServerTimeClock;

use DateTimeImmutable;
use DateTimeZone;
use ServerTimeClock\Internal\CacheManager;

class ServerClock
{
    private DateTimeImmutable $datetime;
    private string $timezone;
    private string $clientName;

    public function __construct(array $config)
    {
        $cache = new CacheManager($config);
        $data = $cache->getCachedTimeData();

        $this->datetime = $data['datetime'];
        $this->timezone = $data['timezone'];
        $this->clientName = $data['client_name'];
    }

    /**
     * Returns the current server time as DateTimeImmutable.
     *
     * @return DateTimeImmutable
     */
    public function now(): DateTimeImmutable
    {
        return $this->datetime;
    }

    /**
     * Returns the timezone used by the clock.
     *
     * @return DateTimeZone
     */
    public function getTimezone(): DateTimeZone
    {
        return new DateTimeZone($this->timezone);
    }

    /**
     * Returns the client name used to fetch the time data.
     *
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }
}
