<?php
/**
 * The Nilfraud Project
 * @copyright  Copyright (c) Hybula B.V. (https://www.hybula.com)
 * @author     Nilfraud Development Team <development@hybula.com>
 * @copyright  2017-2024 Hybula B.V.
 * @license    MPL-2.0 License
 * @link       https://github.com/nilfraud/php-library
 */

declare(strict_types=1);

namespace Nilfraud\API;

use Nilfraud\Exceptions\ApiException;
use Nilfraud\Exceptions\ClientException;

class Client
{
    /**
     * @var string URL to API.
     */
    private string $apiUrl;

    /**
     * @var string Identifying key for API.
     */
    private string $apiKey;

    /**
     * @var string Authenticating token for API.
     */
    private string $apiToken;

    /**
     * @var bool Whether to disable TLS validation.
     */
    private bool $validateCertificate = true;

    /**
     * The constructor defines the API server to use, by default the current one is used.
     *
     * @param string $apiUrl
     */
    public function __construct(string $apiUrl = 'https://api.nilfraud.com/v1')
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Checks whether all API related configs are set.
     *
     * @throws ClientException
     */
    private function checkApiConfig(): void
    {
        if (!isset($this->apiUrl) || !isset($this->apiCore) || !isset($this->apiKey) || !isset($this->apiToken)) {
            throw new ClientException('Not all API params are set.');
        }
    }

    /**
     * Sets authentication credentials to use for API calls.
     *
     * @param string $apiKey
     * @param string $apiToken
     */
    public function setAuthCredentials(string $apiKey, string $apiToken): void
    {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
    }

    /**
     * Tell cURL to skip TLS certificate validation, useful when you're hacking around. Do not use in production!
     *
     * @param bool $validateCertificate
     */
    public function validateCertificate(bool $validateCertificate = true): void
    {
        $this->validateCertificate = $validateCertificate;
    }

    /**
     * Check whether a successful API-call is possible, so sends a PING and returns a PONG on success.
     *
     * @return bool
     * @throws ClientException|ApiException
     */
    public function pingPong(): bool
    {
        $apiCall = $this->apiCall('GET', 'ping');
        if (isset($apiCall['results']) && $apiCall['results'] == 'pong') {
            return true;
        }
        throw new ApiException('Did not receive PONG back from API.');
    }

    /**
     * Does authentication and HTTP calls to API.
     *
     * @param string $httpMethod The HTTP method to use.
     * @param string $endpoint The endpoint to call, this may/must include the domain.
     * @param array $payload The body content of the request, also called payload.
     * @return array
     * @throws ClientException|ApiException
     */
    public function apiCall(string $httpMethod, string $endpoint, array $payload = []): array
    {
        $this->checkApiConfig();
        if (!in_array($httpMethod, ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'])) {
            throw new ClientException('Unsupported HTTP method.');
        }
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, [
            CURLOPT_URL => $this->apiUrl . '/' . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode($this->apiKey . ':' . $this->apiToken)
            ],
        ]);
        if (!$this->validateCertificate) {
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($curlHandle);
        if (strpos($response, '"status"') !== false) { // Be sure we receive actual JSON before json_decoding();
            return json_decode($response, true);
        }
        curl_close($curlHandle);
        throw new ApiException('Unknown response from API.');
    }
}
