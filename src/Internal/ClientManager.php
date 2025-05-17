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

use RuntimeException;
use UnexpectedValueException;
use ServerTimeClock\Client\IpGeolocationApiClient;
use ServerTimeClock\Client\WorldTimeApiClient;
use ServerTimeClock\Client\TimeApiIoClient;

class ClientManager
{
    /**
     * Available clients in order of preference.
     */
    const CLIENTS = [
        'IpGeoLocation',
        'WorldTimeApi',
        'TimeApiIo',
    ];

    /**
     * @var array Configuration array containing client and credentials.
     */
    public function __construct(
        private array $config
    ) {}

    /**
     * Tries clients in configured order until one succeeds.
     */
    public function getAvailableClientData(): array
    {
        $candidates = [];

        $preferred = $this->config['client'] ?? null;
        $credentials = $this->config['credentials'] ?? [];
        if ($preferred) {
            if (!in_array($preferred, self::CLIENTS)) {
                $message = "\nPreferred client '$preferred' is not in the list of available clients.\n" .
                    "Available clients are: " . implode(', ', self::CLIENTS) . ".\n\n";
                throw new UnexpectedValueException($message);
            }
            $candidates[] = $preferred;
        }

        // Add fallbacks
        foreach (self::CLIENTS as $fallback) {
            if (!in_array($fallback, $candidates)) {
                $candidates[] = $fallback;
            }
        }

        foreach ($candidates as $name) {
            try {
                $client = match ($name) {
                    'IpGeoLocation' => new IpGeolocationApiClient($credentials['IpGeoLocation'] ?? ''),
                    'TimeApiIo' => new TimeApiIoClient(),
                    'WorldTimeApi' => new WorldTimeApiClient($credentials['WorldTimeApi'] ?? null),
                    default => throw new RuntimeException("Unknown client: $name"),
                };

                // Test call and return data
                $data = $client->fetch();
                return $data;
            } catch (\RuntimeException $e) {
                // echo "Trying client: $name\n";
                // var_dump($e->getMessage());
                // Log the error or handle it as needed
                // Ignore the error and try the next client
            }
        }

        throw new RuntimeException("No available time client succeeded.");
    }
}
