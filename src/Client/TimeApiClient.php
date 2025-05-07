<?php

namespace ServerTimeClock\Client;

interface TimeApiClient
{
    /**
     * Fetch current time and timezone info from external API.
     *
     * @return array Parsed JSON response.
     * @throws \RuntimeException if fetching fails or response is invalid.
     */
    public function fetch(): array;
}
