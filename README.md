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
        'WorldTimeApi' => 'your-api-key2',
    ],
    'enable_cache' => true,
    'cache_ttl' => 300, // seconds
    'useMock' => false, // Mosk is usually used for testing.
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
| `useMock`      | bool   | Enable/Disable Mock for tests (default: `false`)                |


## Providers

🌐 WorldTimeApi – no key required

🌐 IpGeoLocation – requires free API key from ipgeolocation.io

🌐 TimeApiIo – no key required

## Laravel Integration

You can integrate this package into Laravel with minimal setup.

### Configuration (Optional)

To publish the config file:

```bash
php artisan vendor:publish --tag=server-clock-config
```

This will create config/server-clock.php:

```php
return [
    'client' => 'WorldTimeApi',
    'credentials' => [
        // 'IpGeoLocation' => '',
    ],
    'enable_cache' => true,
    'cache_ttl' => 300,
];
```

### Usage in Laravel

```php
use ServerTimeClock\ServerClock;

$clock = app(ServerClock::class);
echo $clock->now()->format('c');
```

## Yii2 Integration

### Configuration
You may use the ServerClock class directly or configure it as a Yii2 component:

```php
'components' => [
    'serverClock' => [
        'class' => \ServerTimeClock\Yii\ServerClockComponent::class,
        'client' => 'WorldTimeApi',
        'credentials' => [
            // 'IpGeoLocation' => '',
        ],
        'enableCache' => true,
        'cacheTtl' => 300,
    ],
],
```

### Usage
Then use it:

```php
Yii::$app->serverClock->now();
```

> **Note**: Yii2 support is optional and requires yiisoft/yii2 to be installed in your project.

## Optional Dependencies

This package is framework-agnostic. Laravel and Yii2 integrations are provided for convenience.

| Framework | Package                    | Required? |
|-----------|----------------------------|-----------|
| Laravel   | `laravel/framework`        | Optional  |
| Yii2      | `yiisoft/yii2`             | Optional  |

## APCu Cache Requirements

If you enable caching (enable_cache => true), make sure the [APCu extension](https://www.php.net/manual/en/book.apcu.php) is installed and enabled in your PHP configuration. This typically means:

 *  The apcu extension must be installed (e.g., via pecl install apcu or appropriate package manager).
 *  For CLI usage (e.g., when running tests), ensure apc.enable_cli=1 is set in your php.ini.

You can verify if APCu is available by running:
```bash
php -i | grep apcu
```

## Testing
Run PHPUnit tests:
```bash
vendor/bin/phpunit
```

## License

MIT License

Made with ❤️ by Oleksandr Nosov