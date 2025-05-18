<?php

namespace ServerTimeClock\Tests;

use PHPUnit\Framework\TestCase;
use ServerTimeClock\ServerClock;
use DateTimeImmutable;
use DateTimeZone;
use UnexpectedValueException;

/**
 * Class ServerClockTest
 *
 * @version   1.2.0
 * @author    Alex
 * @since     2025-05-18
 * @modified  2025-05-18 by Alex — added type hints and descriptions
 * @package   ServerTimeClock\Tests
 *
 * @covers \ServerTimeClock\ServerClock
 *
 * @todo Add test to verify caching behavior when cache is enabled and TTL is set
 */
class ServerClockTest extends TestCase
{
    /**
     * Provides different valid client configurations for testing.
     *
     * Each configuration uses a different time API client.
     *
     * @return array<string, array{0: array<string, mixed>}>
     */
    public static function clientProvider(): array
    {
        $path = __DIR__ . '/config/server_clock_test.json';
        $json = file_get_contents($path);
        $baseConfig = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return [
            'WorldTimeApi'    => [array_merge($baseConfig, ['client' => 'WorldTimeApi'])],
            'IpGeoLocation'   => [array_merge($baseConfig, ['client' => 'IpGeoLocation'])],
            'TimeApiIo'       => [array_merge($baseConfig, ['client' => 'TimeApiIo'])],
        ];
    }

    /**
     * Ensures that the `now()` method returns a valid DateTimeImmutable instance
     * and that the client name is not empty.
     *
     * @dataProvider clientProvider
     */
    public function testNowReturnsDateTimeImmutable(array $config): void
    {
        $clock = ServerClock::getInstance($config);
        $now = $clock->now();
        $clientName = $clock->getClientName();

        $this->assertNotEmpty($clientName, 'Client name should not be empty.');
        $this->assertInstanceOf(DateTimeImmutable::class, $now, 'Expected instance of DateTimeImmutable.');
    }

    /**
     * Verifies that the timezone returned by the client is a valid DateTimeZone
     * and has a non-empty name.
     *
     * @dataProvider clientProvider
     */
    public function testTimezoneCanBeSpecified(array $config): void
    {
        $clock = ServerClock::getInstance($config);
        $timezone = $clock->getTimezone();

        $this->assertInstanceOf(DateTimeZone::class, $timezone, 'Expected instance of DateTimeZone.');
        $this->assertNotEmpty($timezone->getName(), 'Timezone name should not be empty.');
    }

    /**
     * Ensures that providing an invalid client name throws an UnexpectedValueException.
     *
     * @dataProvider clientProvider
     */
    public function testInvalidClientThrowsException(array $config): void
    {
        $config['client'] = 'InvalidClient';
        $config['enable_cache'] = false; // Disable cache to ensure fresh lookup

        $this->expectException(UnexpectedValueException::class);
        ServerClock::getInstance($config);
    }
}
