<?php

namespace ServerTimeClock\Tests;

use PHPUnit\Framework\TestCase;
use ServerTimeClock\ServerClock;

class ServerClockTest extends TestCase
{
    public $config = [
        'client' => 'WorldTimeApi', // preferred
        'credentials' => [
            'IpGeoLocation' => '71fba5dbb71e4e87a94cea31783d9f2a',  // Example key
            // 'WorldTimeApi' => 'TOKEN',
        ],
        'enable_cache' => true,
        'cache_ttl' => 300,
    ];

    /**
     * @dataProvider clientProvider
     */
    public function testInvalidClientThrowsException()
    {
        $this->expectException(\UnexpectedValueException::class);

        $config = $this->config;
        $config['client'] = 'InvalidClient';
        ServerClock::getInstance($config);
    }

    /**
     * @dataProvider clientProvider
     */
    public function testNowReturnsDateTimeImmutable()
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
    public function testTimezoneCanBeSpecified()
    {
        $clock = ServerClock::getInstance($this->config);
        $now = $clock->now();

        $timezone = $now->getTimezone();
        $this->assertInstanceOf(\DateTimeZone::class, $timezone);
        $this->assertNotEmpty($timezone->getName());
    }
}
