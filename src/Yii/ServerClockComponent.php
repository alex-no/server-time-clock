<?php

namespace ServerTimeClock\Yii;

use yii\base\Component;
use ServerTimeClock\ServerClock;

class ServerClockComponent extends Component
{
    public string $client = 'WorldTimeApi';
    public array $credentials = [];
    public bool $enableCache = true;
    public int $cacheTtl = 300;

    private ServerClock $clock;

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

    public function __call($name, $params)
    {
        return $this->clock->$name(...$params);
    }
}
