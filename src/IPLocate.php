<?php

declare(strict_types=1);

namespace IPLocate;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use IPLocate\Exceptions\APIException;
use IPLocate\Exceptions\AuthenticationException;
use IPLocate\Exceptions\InvalidIPException;
use IPLocate\Exceptions\NotFoundException;
use IPLocate\Exceptions\RateLimitException;
use IPLocate\Models\LookupResponse;

class IPLocate
{
    private const DEFAULT_BASE_URL = 'https://iplocate.io/api/';
    private const DEFAULT_TIMEOUT = 30.0;

    private ClientInterface $httpClient;

    public function __construct(
        private ?string $apiKey = null,
        private string $baseUrl = self::DEFAULT_BASE_URL,
        private float $timeout = self::DEFAULT_TIMEOUT,
        ?ClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? new Client([
            'timeout' => $this->timeout,
            'base_uri' => $this->baseUrl,
        ]);
    }

    /**
     * Look up an IP address.
     *
     * @param string|null $ip IP address to look up. If null, looks up your own IP.
     * @return LookupResponse The lookup response
     * @throws InvalidIPException If the IP address is invalid
     * @throws AuthenticationException If the API key is invalid
     * @throws NotFoundException If the IP address is not found
     * @throws RateLimitException If the rate limit is exceeded
     * @throws APIException For other API errors
     * @throws GuzzleException For HTTP errors
     */
    public function lookup(?string $ip = null): LookupResponse
    {
        if ($ip !== null) {
            $this->validateIP($ip);
        }

        $url = $this->buildUrl($ip);

        try {
            $response = $this->httpClient->request('GET', $url);
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return LookupResponse::fromArray($data);
        } catch (RequestException $e) {
            $this->handleErrorResponse($e);
        }
    }

    /**
     * Look up your own IP address.
     *
     * @return LookupResponse The lookup response
     * @throws AuthenticationException If the API key is invalid
     * @throws RateLimitException If the rate limit is exceeded
     * @throws APIException For other API errors
     * @throws GuzzleException For HTTP errors
     */
    public function lookupSelf(): LookupResponse
    {
        return $this->lookup();
    }

    private function validateIP(string $ip): void
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidIPException($ip);
        }
    }

    private function buildUrl(?string $ip): string
    {
        $path = $ip !== null ? "lookup/{$ip}" : 'lookup/';

        $query = [];
        if ($this->apiKey !== null) {
            $query['apikey'] = $this->apiKey;
        }

        return $path . (empty($query) ? '' : '?' . http_build_query($query));
    }

    private function handleErrorResponse(RequestException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response?->getStatusCode() ?? 0;
        $responseBody = $response?->getBody()?->getContents();

        // Try to parse error message from JSON
        $errorMessage = 'Unknown error';
        if ($responseBody) {
            try {
                $errorData = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
                $errorMessage = $errorData['error'] ?? $errorMessage;
            } catch (\JsonException) {
                $errorMessage = $responseBody;
            }
        }

        match ($statusCode) {
            400 => throw new InvalidIPException('', $errorMessage, $responseBody),
            403 => throw new AuthenticationException($errorMessage, $responseBody),
            404 => throw new NotFoundException($errorMessage, $responseBody),
            429 => throw new RateLimitException($errorMessage, $responseBody),
            default => throw new APIException($errorMessage, $statusCode, $responseBody),
        };
    }
}
