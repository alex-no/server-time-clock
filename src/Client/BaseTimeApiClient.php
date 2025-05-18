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
namespace ServerTimeClock\Client;

use RuntimeException;

abstract class BaseTimeApiClient
{
    /**
     * Timeout in seconds for cURL requests
     */
    const TIMEOUT = 5;

    /**
     * Authorization key for cURL requests
     */
    public function __construct(
        protected readonly ?string $apiKey = null,
        protected readonly bool $useMock = false
    ) {}

    /**
     * Executes a cURL request with the given options.
     * Throws an exception if the request fails or if the HTTP status code is 4xx or 5xx.
     */
    protected function executeCurl(array $curlOpt): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, $curlOpt);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false || $httpCode >= 400 || $httpCode === 0) {
            $errorMsg = $error ?: 'Unknown cURL error';
            throw new RuntimeException("Failed to fetch time data: {$errorMsg} (HTTP {$httpCode})");
        }

        return $response;
    }

    /**
     * Fetches data from the API and decodes the JSON response.
     * Throws an exception if the response is not valid JSON.
     */
    protected function fetchAndDecode(array $curlOpt): array
    {
        if ($this->useMock) {
            return $this->getMockData();
        }

        $response = $this->executeCurl($curlOpt);

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Mock data for tests or local development
     * @return array<array-key, mixed>
     * @throws \JsonException
     */
    protected function getMockData(): array
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $microTime = $now->format('U.u');
        $milliSeconds = match (true) {
            str_contains($microTime, '.') => (int) substr(explode('.', $microTime)[1] ?? '000', 0, 3),
            default => 0,
        };

        return [
            'client_name'    => 'MockClient',
            'timezone'       => $now->getTimezone()->getName(),
            'year'           => (int) $now->format('Y'),
            'month'          => (int) $now->format('m'),
            'day'            => (int) $now->format('d'),
            'hour'           => (int) $now->format('H'),
            'minute'         => (int) $now->format('i'),
            'seconds'        => (int) $now->format('s'),
            'milli_seconds'  => $milliSeconds,
        ];
    }
}
