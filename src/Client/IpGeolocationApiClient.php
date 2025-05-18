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

final class IpGeolocationApiClient extends BaseTimeApiClient implements TimeApiClient
{
    /**
     * The API endpoint for the IP Geolocation service.
     * This endpoint is used to fetch the current time and timezone information based on the client's IP address.
     */
    const ENDPOINT = 'https://api.ipgeolocation.io/timezone?apiKey=';

    /**
     * Fetches the current time and timezone information from the TimeAPI service.
     *
     * @return array The normalized data containing timezone and time information.
     * @throws RuntimeException if the API request fails or returns invalid data.
     */
    public function fetch(): array
    {
        $url = self::ENDPOINT . urlencode($this->apiKey);

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
        ];

        $data = $this->fetchAndDecode($curlOptions);
        return $this->useMock ? $data : $this->normalizeData($data);
    }

    /**
     * Normalize the data structure to match your application's needs
     */
    protected function normalizeData(array $sourceData): array
    {
        $time = explode(':', $sourceData['time_24'] ?? '');
        $unixTime = explode('.', strval($sourceData['date_time_unix']) ?? '');
        return [
            'client_name' => 'IpGeoLocation',
            'timezone' => $sourceData['timezone'] ?? null,
            'year' => $sourceData['year'] ?? null,
            'month' => $sourceData['month'] ?? null,
            'day' => !empty($sourceData['date']) ? substr($sourceData['date'], -2) : null,
            'hour' => $time[0] ?? null,
            'minute' => $time[1] ?? null,
            'seconds' => $time[2] ?? null,
            'milli_seconds' => $unixTime[1] ?? null,
        ];
    }
}
