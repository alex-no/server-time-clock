<?php
declare(strict_types = 1);
/**
 * This file is part of the ServerTimeClock package.
 *
 * (c) 2023 ServerTimeClock
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ServerTimeClock\Internal;

use DateTimeImmutable;
use DateTimeZone;
use ServerTimeClock\Internal\ClientManager;

class CacheManager
{
    /**
     * Default cache TTL in seconds.
     */
    private const DEFAULT_TTL = 3600;
    /**
     * Cache key for storing server time data.
     */
    private const CACHE_KEY = 'server_time_clock_cache';

    /**
     * @var array Configuration array containing client and credentials.
     */
    public function __construct(
        private readonly array $config
    ) {}

    /**
     * Returns cached time data or fetches new data if cache is missing or disabled.
     * @return array{
     *     client_name: string,
     *     timezone: string,
     *     datetime: \DateTimeImmutable
     * }
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
     * @return array{
     *     client_name: string,
     *     timezone: string,
     *     datetime: \DateTimeImmutable
     * }
     */
    protected function updateCache(): array
    {
        $manager = new ClientManager($this->config);
        $data = $manager->getAvailableClientData();

        $remote = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s.u',
            sprintf(
                '%d-%02d-%02d %02d:%02d:%02d.%03d',
                $data['year'], $data['month'], $data['day'],
                $data['hour'], $data['minute'], $data['seconds'], $data['milli_seconds']
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
