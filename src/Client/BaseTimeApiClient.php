<?php

namespace ServerTimeClock\Client;

use RuntimeException;

abstract class BaseTimeApiClient
{
    /**
     * Timeout in seconds for cURL requests
     */
    const TIMEOUT = 5;

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

        if ($response === false || $httpCode >= 400) {
            throw new RuntimeException("Failed to fetch time data: {$error} (HTTP {$httpCode})");
        }

        return $response;
    }

    /**
     * Fetches data from the API and decodes the JSON response.
     * Throws an exception if the response is not valid JSON.
     */
    protected function fetchAndDecode(array $curlOpt): array
    {
        $response = $this->executeCurl($curlOpt);

        $data = json_decode($response, true);
        if (!is_array($data)) {
            throw new RuntimeException("Invalid JSON received from time API");
        }

        return $data;
    }
}
