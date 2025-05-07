# Server Time Clock

[![Latest Stable Version](https://poser.pugx.org/alex-no/server-time-clock/v/stable)](https://packagist.org/packages/alex-no/server-time-clock)
[![Total Downloads](https://poser.pugx.org/alex-no/server-time-clock/downloads)](https://packagist.org/packages/alex-no/server-time-clock)
[![License](https://poser.pugx.org/alex-no/server-time-clock/license)](https://packagist.org/packages/alex-no/server-time-clock)
[![PHPUnit](https://github.com/alex-no/server-time-clock/actions/workflows/phpunit.yml/badge.svg)](https://github.com/alex-no/server-time-clock/actions)

> PSR-20 clock implementation that returns the current server time based on its local timezone or online providers.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
composer require alex-no/server-time-clock
```

## Usage
```php
use ServerTimeClock\ServerClock;

$clock = new ServerClock([
    'client' => 'WorldTimeApi', // or 'IpGeoLocation', 'TimeApiIo'
    'credentials' => [
        'IpGeoLocation' => 'your-api-key', // optional, depending on the provider
    ],
    'enable_cache' => true,
    'cache_ttl' => 300, // seconds
]);

$now = $clock->now(); // instance of DateTimeImmutable
echo $now->format(DATE_ATOM);
```

## Configuration Options

| Key            | Type   | Description                                                     |
| -------------- | ------ | --------------------------------------------------------------- |
| `client`       | string | Preferred time provider (`WorldTimeApi`, `IpGeoLocation`, etc.) |
| `credentials`  | array  | API keys for time providers (optional)                          |
| `enable_cache` | bool   | Enable APCu-based caching (default: `false`)                    |
| `cache_ttl`    | int    | Cache duration in seconds                                       |


## Providers

🌐 WorldTimeApi – no key required

🌐 IpGeoLocation – requires free API key from ipgeolocation.io

🌐 TimeApiIo – no key required

## Testing
Run PHPUnit tests:
```bash
vendor/bin/phpunit
```

## License

MIT License

Made with ❤️ by Oleksandr Nosov