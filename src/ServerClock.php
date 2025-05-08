<?php

namespace ServerTimeClock;

use DateTimeImmutable;
use DateTimeZone;
use ServerTimeClock\Internal\CacheManager;

/**
 * Represents a synchronized server clock with timezone and client metadata.
 *
 * This class fetches and caches the current time from an external time provider
 * and exposes access to the current datetime, timezone, and client name.
 *
 * @package ServerTimeClock
 */
class ServerClock
{
    /**
     * CacheManager instance to handle caching of time data.
     *
     * @var CacheManager
     */
    private CacheManager $cache;

    /**
     * The data array contains the current datetime, timezone, and client name.
     *
     * @var array{
     *     datetime: \DateTimeImmutable,
     *     timezone: \DateTimeZone,
     *     clientName: string
     * }
     */
    private array $data;

    /**
     * ServerClock constructor.
     *
     * @param array{
     *     client?: string,  // Preferred client for receiving time data (e.g., 'WorldTimeApi', 'IpGeoLocation')
     *     credentials?: array<string, string>, // Client credentials for authentication (optional)
     *     enableCache?: bool,  // Enable APCu-based caching (default: false)
     *     cacheTtl?: int,      // Cache TTL in seconds (default: 300)
     * } $config Configuration for cache and time source.
     */
    public function __construct(array $config)
    {
        $this->cache = new CacheManager($config);
        $this->refreshData();
    }

    /**
     * Returns the current server time as DateTimeImmutable.
     *
     * @return DateTimeImmutable
     */
    public function now(): DateTimeImmutable
    {
        return $this->data['datetime'];
    }

    /**
     * Returns the timezone used by the clock.
     *
     * @return DateTimeZone
     */
    public function getTimezone(): DateTimeZone
    {
        return $this->data['timezone'];
    }

    /**
     * Returns the client name used to fetch the time data.
     *
     * @return string
     */
    public function getClientName(): string
    {
        return $this->data['clientName'];
    }

    /**
     * Returns the full data array containing datetime, timezone, and client name.
     *
     * @return array{
     *     datetime: \DateTimeImmutable,
     *     timezone: \DateTimeZone,
     *     clientName: string
     * }
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Refreshes the cached data by fetching new data from the configured client (external source).
     *
     * @return array{
     *     datetime: \DateTimeImmutable,
     *     timezone: \DateTimeZone,
     *     clientName: string
     * }
     *
     * @throws \RuntimeException If the time data could not be retrieved.
     * @see CacheManager::getCachedTimeData()
     */
    public function refreshData(): array
    {
        $data = $this->cache->getCachedTimeData();

        $this->data = [
            'datetime' => $data['datetime'],
            'timezone' => new DateTimeZone($data['timezone']),
            'clientName' => $data['client_name'],
        ];
        return $this->data;
    }
}
