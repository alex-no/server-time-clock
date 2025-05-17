<?php

namespace ServerTimeClock\Tests;

use PHPUnit\Framework\TestCase;
use ServerTimeClock\ServerClock;

class ServerClockTest extends TestCase
{
    private array $config;

    /**
     * @before
     */
    public function clientProvider(): array
    {
        $path = __DIR__ . '/config/server_clock_test.json';
        $json = file_get_contents($path);
        $baseConfig = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return [
            'worldtimeapi' => [array_merge($baseConfig, ['client' => 'worldtimeapi', 'timezone' => 'Europe/Berlin'])],
            'ipgeolocation' => [array_merge($baseConfig, ['client' => 'ipgeolocation', 'timezone' => 'America/New_York'])],
            'timeapiio' => [array_merge($baseConfig, ['client' => 'timeapiio', 'timezone' => 'Asia/Tokyo'])],
        ];
    }

    /**
     * @dataProvider clientProvider
     */
    public function testNowReturnsDateTimeImmutable(): void
    {
        $clock = ServerClock::getInstance($this->config);
        $now = $clock->now();
        $clientName = $clock->getClientName();

        $this->assertNotEmpty($clientName);
        $this->assertInstanceOf(\DateTimeImmutable::class, $now);
    }

    /**
     * @dataProvider clientProvider
     */
    public function testTimezoneCanBeSpecified(): void
    {
        $clock = ServerClock::getInstance($this->config);
        $timezone = $clock->getTimezone();

        $this->assertNotEmpty($timezone->getName());
        $this->assertInstanceOf(\DateTimeZone::class, $timezone);
    }

    /**
     * @dataProvider clientProvider
     */
    public function testInvalidClientThrowsException(): void
    {
        $config = $this->config;
        $config['client'] = 'InvalidClient';

        $this->expectException(\UnexpectedValueException::class);
        ServerClock::getInstance($config);
    }
}
