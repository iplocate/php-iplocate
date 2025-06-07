<?php

declare(strict_types=1);

namespace IPLocate\Tests\Unit\Models;

use IPLocate\Models\ASN;
use IPLocate\Models\LookupResponse;
use IPLocate\Models\Privacy;
use PHPUnit\Framework\TestCase;

class LookupResponseTest extends TestCase
{
    public function testFromArrayCreatesInstance(): void
    {
        $data = [
            'ip' => '8.8.8.8',
            'country' => 'United States',
            'country_code' => 'US',
            'is_eu' => false,
            'city' => 'Mountain View',
            'continent' => 'North America',
            'latitude' => 37.4056,
            'longitude' => -122.0775,
            'time_zone' => 'America/Los_Angeles',
            'postal_code' => '94043',
            'subdivision' => 'California',
            'currency_code' => 'USD',
            'calling_code' => '1',
            'network' => '8.8.8.0/24',
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
            'asn' => [
                'asn' => 'AS15169',
                'route' => '8.8.8.0/24',
                'netname' => 'GOOGLE',
                'name' => 'Google LLC',
                'country_code' => 'US',
                'domain' => 'google.com',
                'type' => 'hosting',
                'rir' => 'ARIN',
            ],
        ];

        $response = LookupResponse::fromArray($data);

        $this->assertSame('8.8.8.8', $response->ip);
        $this->assertSame('United States', $response->country);
        $this->assertSame('US', $response->countryCode);
        $this->assertFalse($response->isEu);
        $this->assertSame('Mountain View', $response->city);
        $this->assertSame('North America', $response->continent);
        $this->assertSame(37.4056, $response->latitude);
        $this->assertSame(-122.0775, $response->longitude);
        $this->assertSame('America/Los_Angeles', $response->timeZone);
        $this->assertSame('94043', $response->postalCode);
        $this->assertSame('California', $response->subdivision);
        $this->assertSame('USD', $response->currencyCode);
        $this->assertSame('1', $response->callingCode);
        $this->assertSame('8.8.8.0/24', $response->network);
        $this->assertInstanceOf(Privacy::class, $response->privacy);
        $this->assertInstanceOf(ASN::class, $response->asn);
        $this->assertSame($data, $response->raw);
    }

    public function testFromArrayWithMinimalData(): void
    {
        $data = [
            'ip' => '8.8.8.8',
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

        $response = LookupResponse::fromArray($data);

        $this->assertSame('8.8.8.8', $response->ip);
        $this->assertNull($response->country);
        $this->assertNull($response->city);
        $this->assertFalse($response->isEu);
        $this->assertInstanceOf(Privacy::class, $response->privacy);
        $this->assertNull($response->asn);
        $this->assertSame($data, $response->raw);
    }
}
