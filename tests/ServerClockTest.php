<?php

namespace ServerTimeClock\Tests;

use PHPUnit\Framework\TestCase;
use ServerTimeClock\ServerClock;

class ServerClockTest extends TestCase
{
    /**
    * Data provider, returns an array of configurations
     */
    public static function clientProvider(): array
    {
        $path = __DIR__ . '/config/server_clock_test.json';
        $json = file_get_contents($path);
        $baseConfig = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return [
            'WorldTimeApi' => [array_merge($baseConfig, ['client' => 'WorldTimeApi'])],
            'IpGeoLocation' => [array_merge($baseConfig, ['client' => 'IpGeoLocation'])],
            'TimeApiIo' => [array_merge($baseConfig, ['client' => 'TimeApiIo'])],
        ];
    }

    /**
     * @dataProvider clientProvider
     */
    public function testNowReturnsDateTimeImmutable(array $config): void
    {
        $clock = ServerClock::getInstance($config);
        $now = $clock->now();
        $clientName = $clock->getClientName();

        $this->assertNotEmpty($clientName);
        $this->assertInstanceOf(\DateTimeImmutable::class, $now);
    }

    /**
     * @dataProvider clientProvider
     */
    public function testTimezoneCanBeSpecified(array $config): void
    {
        $clock = ServerClock::getInstance($config);
        $timezone = $clock->getTimezone();

        $this->assertNotEmpty($timezone->getName());
        $this->assertInstanceOf(\DateTimeZone::class, $timezone);
    }

    /**
     * @dataProvider clientProvider
     */
    public function testInvalidClientThrowsException(array $config): void
    {
        $config['client'] = 'InvalidClient';

        $this->expectException(\UnexpectedValueException::class);
        ServerClock::getInstance($config);
    }
}
