<?php

declare(strict_types=1);

namespace IPLocate\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use IPLocate\Exceptions\AuthenticationException;
use IPLocate\Exceptions\InvalidIPException;
use IPLocate\Exceptions\NotFoundException;
use IPLocate\Exceptions\RateLimitException;
use IPLocate\IPLocate;
use IPLocate\Models\LookupResponse;
use PHPUnit\Framework\TestCase;

class IPLocateTest extends TestCase
{
    private function createClientWithMockResponse(Response|RequestException $response): IPLocate
    {
        $mock = new MockHandler([$response]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return new IPLocate('test-api-key', httpClient: $client);
    }

    public function testLookupSuccessful(): void
    {
        $responseData = [
            'ip' => '8.8.8.8',
            'country' => 'United States',
            'privacy' => [
                'is_abuser' => false,
                'is_anonymous' => false,
                'is_bogon' => false,
                'is_hosting' => false,
                'is_icloud_relay' => false,
                'is_proxy' => false,
                'is_tor' => false,
                'is_vpn' => false,
            ],
        ];

        $response = new Response(200, [], json_encode($responseData));
        $client = $this->createClientWithMockResponse($response);

        $result = $client->lookup('8.8.8.8');

        $this->assertInstanceOf(LookupResponse::class, $result);
        $this->assertSame('8.8.8.8', $result->ip);
        $this->assertSame('United States', $result->country);
    }

    public function testLookupSelfSuccessful(): void
    {
        $responseData = [
            'ip' => '203.0.113.1',
            'country' => 'Australia',
            'privacy' => [
                'is_abuser' => false,
                'is_anonymous' => false,
                'is_bogon' => false,
                'is_hosting' => false,
                'is_icloud_relay' => false,
                'is_proxy' => false,
                'is_tor' => false,
                'is_vpn' => false,
            ],
        ];

        $response = new Response(200, [], json_encode($responseData));
        $client = $this->createClientWithMockResponse($response);

        $result = $client->lookupSelf();

        $this->assertInstanceOf(LookupResponse::class, $result);
        $this->assertSame('203.0.113.1', $result->ip);
        $this->assertSame('Australia', $result->country);
    }

    public function testLookupWithInvalidIP(): void
    {
        $client = new IPLocate('test-api-key');

        $this->expectException(InvalidIPException::class);
        $this->expectExceptionMessage('Invalid IP address: invalid-ip');

        $client->lookup('invalid-ip');
    }

    public function testLookupHandles400Error(): void
    {
        $errorResponse = ['error' => 'Invalid IP address'];
        $exception = new RequestException(
            'Bad Request',
            new Request('GET', '/lookup/invalid'),
            new Response(400, [], json_encode($errorResponse))
        );

        $client = $this->createClientWithMockResponse($exception);

        $this->expectException(InvalidIPException::class);
        $this->expectExceptionMessage('Invalid IP address');

        $client->lookup('1.2.3.4');
    }

    public function testLookupHandles403Error(): void
    {
        $errorResponse = ['error' => 'Invalid API key'];
        $exception = new RequestException(
            'Forbidden',
            new Request('GET', '/lookup/1.2.3.4'),
            new Response(403, [], json_encode($errorResponse))
        );

        $client = $this->createClientWithMockResponse($exception);

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $client->lookup('1.2.3.4');
    }

    public function testLookupHandles404Error(): void
    {
        $errorResponse = ['error' => 'IP not found'];
        $exception = new RequestException(
            'Not Found',
            new Request('GET', '/lookup/192.0.2.1'),
            new Response(404, [], json_encode($errorResponse))
        );

        $client = $this->createClientWithMockResponse($exception);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('IP not found');

        $client->lookup('192.0.2.1');
    }

    public function testLookupHandles429Error(): void
    {
        $errorResponse = ['error' => 'Rate limit exceeded'];
        $exception = new RequestException(
            'Too Many Requests',
            new Request('GET', '/lookup/1.2.3.4'),
            new Response(429, [], json_encode($errorResponse))
        );

        $client = $this->createClientWithMockResponse($exception);

        $this->expectException(RateLimitException::class);
        $this->expectExceptionMessage('Rate limit exceeded');

        $client->lookup('1.2.3.4');
    }

    public function testClientConfiguration(): void
    {
        $client = new IPLocate(
            apiKey: 'my-api-key',
            baseUrl: 'https://custom.api.com',
            timeout: 60.0
        );

        $this->assertInstanceOf(IPLocate::class, $client);
    }
}
