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

final class WorldTimeApiClient extends BaseTimeApiClient implements TimeApiClient
{
    /**
     * The API endpoint for the WorldTimeAPI service.
     * This endpoint is used to fetch the current time and timezone information based on the client's IP address.
     */
    const ENDPOINT = 'https://worldtimeapi.org/api/ip';

    /**
     * Fetches the current time and timezone information from the TimeAPI service.
     *
     * @return array The normalized data containing timezone and time information.
     * @throws RuntimeException if the API request fails or returns invalid data.
     */
    public function fetchTimeData(): array
    {
        $headers = [];
        if ($this->apiKey !== null) {
            // This header scheme may vary depending on the service
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }

        $curlOptions = [
            CURLOPT_URL => self::ENDPOINT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_HTTPHEADER => $headers,
        ];

        $data = $this->fetchAndDecode($curlOptions);
        return $this->useMock ? $data : $this->normalizeData($data);
    }

    /**
     * Get the name of the client.
     *
     * @return string Client name.
     */
    public function getClientName(): string
    {
        return 'WorldTimeApi';
    }

    /**
     * Normalize the data structure to match your application's needs
     * @param array $sourceData The raw data received from the API.
     * @return array Normalized data structure.
     */
    protected function normalizeData(array $sourceData): array
    {
        return [
            'client_name' => $this->getClientName(),
            'timezone' => $sourceData['timeZone'] ?? null,
            'year' => $sourceData['year'] ?? null,
            'month' => $sourceData['month'] ?? null,
            'day' => $sourceData['day'] ?? null,
            'hour' => $sourceData['hour'] ?? null,
            'minute' => $sourceData['minute'] ?? null,
            'seconds' => $sourceData['seconds'] ?? null,
            'milli_seconds' => $sourceData['milliSeconds'] ?? null,
        ];
    }
}
