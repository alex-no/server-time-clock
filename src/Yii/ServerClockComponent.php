<?php

namespace ServerTimeClock\Yii;

use yii\base\Component;
use ServerTimeClock\ServerClock;
/**
 * ServerClockComponent is a Yii component that provides access to the server clock.
 * It allows you to fetch the current server time and timezone information from various APIs.
 *
 * @property string $client The client to use for fetching time data.
 * @property array $credentials The credentials for the client.
 * @property bool $enableCache Whether to enable caching for the time data.
 * @property int $cacheTtl The time-to-live (TTL) for the cache in seconds.
 */
class ServerClockComponent extends Component
{
    /**
     * @var string The client to use for fetching time data.
     * Supported values: 'WorldTimeApi', 'IpGeolocation', 'TimeApiIo'.
     */
    public string $client = 'WorldTimeApi';
    /**
     * @var array The credentials for the client.
     * This is an associative array where the key is the client name and the value is the API key or token.
     */
    public array $credentials = [];
    /**
     * @var bool Whether to enable caching for the time data.
     * If true, the time data will be cached for the specified TTL.
     */
    public bool $enableCache = true;
    /**
     * @var int The time-to-live (TTL) for the cache in seconds.
     * This determines how long the cached time data will be valid before it is refreshed.
     */
    public int $cacheTtl = 300;
    /**
     * @var ServerClock The server clock instance.
     */
    private ServerClock $clock;

    /**
     * Initializes the component.
     * This method is called when the component is being initialized.
     * It sets up the server clock instance with the provided configuration.
     */
    public function init(): void
    {
        parent::init();
        $this->clock = new ServerClock([
            'client' => $this->client,
            'credentials' => $this->credentials,
            'enableCache' => $this->enableCache,
            'cacheTtl' => $this->cacheTtl,
        ]);
    }

    /**
     * Returns the current server time as a DateTimeImmutable object.
     * This method fetches the current time from the server clock instance.
     */
    public function __call(string $name, array $params): mixed
    {
        return $this->clock->$name(...$params);
    }

    /**
     * Returns the server clock instance.
     * This method returns the server clock instance used by the component.
     * @return ServerClock The server clock instance.
     */
    public function getClock(): ServerClock
    {
        return $this->clock;
    }
}
