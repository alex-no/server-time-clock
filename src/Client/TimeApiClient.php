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

interface TimeApiClient
{
    /**
     * Fetch current time and timezone info from external API.
     *
     * @return array Parsed JSON response.
     * @throws \RuntimeException if fetching fails or response is invalid.
     */
    public function fetchTimeData(): array;

    /**
     * Get the name of the client.
     * 
     * @return string Client name.
     */
    public function getClientName(): string;
}
