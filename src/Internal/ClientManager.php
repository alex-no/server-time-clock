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
    private const CLIENTS = [
        'IpGeoLocation',
        'WorldTimeApi',
        'TimeApiIo',
    ];

    /**
     * @var array Configuration array containing client and credentials.
     */
    public function __construct(
        private readonly array $config
    ) {}

    /**
     * Tries clients in configured order until one succeeds.
     * @return array{
     *     client_name: string,
     *     timezone: string,
     *     year: int,
     *     month: int,
     *     day: int,
     *     hour: int,
     *     minute: int,
     *     seconds: int,
     *     milli_seconds: int
     * }
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

        $useMock = $this->config['useMock'] ?? false;
        foreach ($candidates as $name) {
            try {
                $client = match ($name) {
                    'IpGeoLocation' => new IpGeolocationApiClient(
                        $credentials['IpGeoLocation'] ?? throw new RuntimeException('Missing credentials for IpGeoLocation'),
                        $useMock
                    ),
                    'TimeApiIo' => new TimeApiIoClient(null, $useMock),
                    'WorldTimeApi' => new WorldTimeApiClient($credentials['WorldTimeApi'] ?? null, $useMock),
                    default => throw new RuntimeException("Unknown client: $name"),
                };

                // Test call and return data
                $data = $client->fetchTimeData();
                return $data;
            } catch (\RuntimeException $e) {
                // Mayby an error occurred, Log the error or handle it as needed
                // Ignore the error and try the next client
            }
        }

        throw new RuntimeException("No available time client succeeded.");
    }
}
