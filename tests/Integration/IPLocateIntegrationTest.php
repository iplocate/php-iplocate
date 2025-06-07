<?php

declare(strict_types=1);

namespace IPLocate\Tests\Integration;

use IPLocate\Exceptions\InvalidIPException;
use IPLocate\IPLocate;
use IPLocate\Models\LookupResponse;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests - these require an actual API key to run.
 * Set IPLOCATE_API_KEY environment variable to run these tests.
 */
class IPLocateIntegrationTest extends TestCase
{
    private ?IPLocate $client = null;

    protected function setUp(): void
    {
        $apiKey = getenv('IPLOCATE_API_KEY');
        if (empty($apiKey)) {
            $this->markTestSkipped('IPLOCATE_API_KEY environment variable not set');
        }

        $this->client = new IPLocate($apiKey);
    }

    public function testLookupPublicIP(): void
    {
        $result = $this->client->lookup('8.8.8.8');

        $this->assertInstanceOf(LookupResponse::class, $result);
        $this->assertSame('8.8.8.8', $result->ip);
        $this->assertNotNull($result->country);
        $this->assertNotNull($result->privacy);
    }

    public function testLookupSelf(): void
    {
        $result = $this->client->lookupSelf();

        $this->assertInstanceOf(LookupResponse::class, $result);
        $this->assertNotEmpty($result->ip);
        $this->assertNotNull($result->privacy);
    }

    public function testLookupInvalidIPThrowsException(): void
    {
        $this->expectException(InvalidIPException::class);

        $this->client->lookup('invalid-ip-address');
    }

    public function testLookupPrivateIPReturnsLimitedData(): void
    {
        $result = $this->client->lookup('192.168.1.1');

        $this->assertInstanceOf(LookupResponse::class, $result);
        $this->assertSame('192.168.1.1', $result->ip);
        $this->assertNotNull($result->privacy);
        $this->assertTrue($result->privacy->isBogon);
    }
}
