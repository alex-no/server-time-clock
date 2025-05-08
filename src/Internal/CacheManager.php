<?php

namespace ServerTimeClock\Internal;

use DateTimeImmutable;
use DateTimeZone;
use ServerTimeClock\Internal\ClientManager;

class CacheManager
{
    private const DEFAULT_TTL = 3600; // seconds
    private const CACHE_KEY = 'server_time_clock_cache';

    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Returns cached time data or fetches new data if cache is missing or disabled.
     */
    public function getCachedTimeData(): array
    {
        if ($this->isCacheEnabled() && $this->isApcuAvailable()) {
            $cache = apcu_fetch(self::CACHE_KEY);
            if (!empty($cache)) {
                $now = microtime(true);
                $serverTime = $cache['time_diff'] + $now;
                $dt = DateTimeImmutable::createFromFormat('U.u', sprintf('%.6f', $serverTime))
                    ->setTimezone(new DateTimeZone($cache['timezone']));

                return [
                    'client_name' => $cache['client_name'],
                    'timezone' => $cache['timezone'],
                    'datetime' => $dt,
                ];
            }
        }

        return $this->updateCache();
    }

    /**
     * Fetches new time data from the configured client and updates the APCu cache if enabled.
     */
    protected function updateCache(): array
    {
        $manager = new ClientManager($this->config);
        $data = $manager->getAvailableClientData();

        $remote = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s.u',
            sprintf(
                '%d-%02d-%02d %02d:%02d:%02d.%06d',
                $data['year'], $data['month'], $data['day'],
                $data['hour'], $data['minute'], $data['seconds'], $data['milli_seconds'] * 1000
            ),
            new DateTimeZone($data['timezone'])
        );

        if ($this->isCacheEnabled() && $this->isApcuAvailable()) {
            $remoteTs = (float) $remote->format('U.u');
            $now = microtime(true);
            $cache = [
                'timezone' => $data['timezone'],
                'client_name' => $data['client_name'],
                'time_diff' => $remoteTs - $now,
            ];

            $ttl = $this->config['cache_ttl'] ?? self::DEFAULT_TTL;
            apcu_store(self::CACHE_KEY, $cache, $ttl);
        }

        return [
            'client_name' => $data['client_name'],
            'timezone' => $data['timezone'],
            'datetime' => $remote,
        ];
    }

    /**
     * Checks if caching is enabled in the configuration.
     */
    private function isCacheEnabled(): bool
    {
        return ($this->config['enable_cache'] ?? true) === true;
    }

    /**
     * Checks if APCu is available and enabled.
     */
    private function isApcuAvailable(): bool
    {
        return function_exists('apcu_fetch') && ini_get('apc.enabled');
    }
}
