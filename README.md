# IPLocate geolocation client for PHP

[![Latest Version](https://img.shields.io/packagist/v/iplocate/php-iplocate.svg)](https://packagist.org/packages/iplocate/php-iplocate)
[![Total Downloads](https://img.shields.io/packagist/dt/iplocate/php-iplocate.svg)](https://packagist.org/packages/iplocate/php-iplocate)
[![PHP Version](https://img.shields.io/packagist/php-v/iplocate/php-iplocate.svg)](https://packagist.org/packages/iplocate/php-iplocate)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Tests](https://github.com/iplocate/php-iplocate/workflows/CI/badge.svg)](https://github.com/iplocate/php-iplocate/actions)

A PHP client for the [IPLocate.io](https://iplocate.io) geolocation API. Look up detailed geolocation and threat intelligence data for any IP address:

- **IP geolocation**: IP to country, IP to city, IP to region/state, coordinates, timezone, postal code
- **ASN information**: Internet service provider, network details, routing information  
- **Privacy & threat detection**: VPN, proxy, Tor, hosting provider detection
- **Company information**: Business details associated with IP addresses - company name, domain, type (ISP/hosting/education/government/business)
- **Abuse contact**: Network abuse reporting information
- **Hosting detection**: Cloud provider and hosting service detection using our proprietary hosting detection engine

See what information we can provide for [your IP address](https://iplocate.io/what-is-my-ip).

## Getting started

You can make 1,000 free requests per day with a [free account](https://iplocate.io/signup). For higher plans, check out [API pricing](https://www.iplocate.io/pricing).

### Requirements

- PHP 8.0 or higher
- Guzzle HTTP client

### Installation

Install via Composer:

```bash
composer require iplocate/php-iplocate
```

Or add to your `composer.json`:

```json
{
    "require": {
        "iplocate/php-iplocate": "^1.0"
    }
}
```

### Quick start

```php
<?php

use IPLocate\IPLocate;

// Create a client with your API key
// Get your free API key from https://iplocate.io/signup
$client = new IPLocate('your-api-key');

// Look up an IP address
$result = $client->lookup('8.8.8.8');

echo "IP: {$result->ip}\n";
if ($result->country) {
    echo "Country: {$result->country}\n";
}
if ($result->city) {
    echo "City: {$result->city}\n";
}

// Check privacy flags
echo "Is VPN: " . ($result->privacy->isVpn ? 'Yes' : 'No') . "\n";
echo "Is Proxy: " . ($result->privacy->isProxy ? 'Yes' : 'No') . "\n";
```

### Get your own IP address information

```php
// Look up your own IP address (no IP parameter)
$result = $client->lookupSelf();
echo "Your IP: {$result->ip}\n";
```

### Get the country for an IP address

```php
$result = $client->lookup('203.0.113.1');
echo "Country: {$result->country} ({$result->countryCode})\n";
```

### Get the currency code for a country by IP address

```php
$result = $client->lookup('203.0.113.1');
echo "Currency: {$result->currencyCode}\n";
```

### Get the calling code for a country by IP address

```php
$result = $client->lookup('203.0.113.1');
echo "Calling code: +{$result->callingCode}\n";
```

## Authentication

Get your free API key from [IPLocate.io](https://iplocate.io/signup), and pass it when creating the client:

```php
$client = new IPLocate('your-api-key');
```

## Examples

### IP address geolocation lookup

```php
use IPLocate\IPLocate;

$client = new IPLocate('your-api-key');
$result = $client->lookup('203.0.113.1');

echo "Country: {$result->country} ({$result->countryCode})\n";
if ($result->latitude && $result->longitude) {
    echo "Coordinates: {$result->latitude}, {$result->longitude}\n";
}
```

### Check for VPN/Proxy Detection

```php
$result = $client->lookup('192.0.2.1');

if ($result->privacy->isVpn) {
    echo "This IP is using a VPN\n";
}

if ($result->privacy->isProxy) {
    echo "This IP is using a proxy\n";
}

if ($result->privacy->isTor) {
    echo "This IP is using Tor\n";
}
```

### ASN and network information

```php
$result = $client->lookup('8.8.8.8');

if ($result->asn) {
    echo "ASN: {$result->asn->asn}\n";
    echo "ISP: {$result->asn->name}\n";
    echo "Network: {$result->asn->route}\n";
}
```

### Custom configuration

```php
use GuzzleHttp\Client;
use IPLocate\IPLocate;

// Custom timeout and HTTP client
$customHttpClient = new Client([
    'timeout' => 60.0,
    'verify' => true,
]);

$client = new IPLocate(
    apiKey: 'your-api-key',
    timeout: 60.0,
    httpClient: $customHttpClient
);

// Custom base URL (for enterprise customers)
$client = new IPLocate(
    apiKey: 'your-api-key',
    baseUrl: 'https://custom-endpoint.com/api'
);
```

## Response structure

The `LookupResponse` object contains all available data:

```php
class LookupResponse
{
    public string $ip;
    public Privacy $privacy;
    public ?string $country;
    public ?string $countryCode;
    public bool $isEu;
    public ?string $city;
    public ?string $continent;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $timeZone;
    public ?string $postalCode;
    public ?string $subdivision;
    public ?string $currencyCode;
    public ?string $callingCode;
    public ?string $network;
    public ?ASN $asn;
    public ?Company $company;
    public ?Hosting $hosting;
    public ?Abuse $abuse;
}
```

Properties marked as `?` are nullable and may be `null` if data is not available.

## Error handling

```php
use IPLocate\IPLocate;
use IPLocate\Exceptions\AuthenticationException;
use IPLocate\Exceptions\InvalidIPException;
use IPLocate\Exceptions\NotFoundException;
use IPLocate\Exceptions\RateLimitException;
use IPLocate\Exceptions\APIException;

$client = new IPLocate('your-api-key');

try {
    $result = $client->lookup('8.8.8.8');
} catch (InvalidIPException $e) {
    echo "Invalid IP address: {$e->getMessage()}\n";
} catch (AuthenticationException $e) {
    echo "Invalid API key: {$e->getMessage()}\n";
} catch (NotFoundException $e) {
    echo "IP address not found: {$e->getMessage()}\n";
} catch (RateLimitException $e) {
    echo "Rate limit exceeded: {$e->getMessage()}\n";
} catch (APIException $e) {
    echo "API error ({$e->getCode()}): {$e->getMessage()}\n";
}
```

Common API errors:

- `InvalidIPException`: Invalid IP address format (HTTP 400)
- `AuthenticationException`: Invalid API key (HTTP 403)
- `NotFoundException`: IP address not found (HTTP 404)
- `RateLimitException`: Rate limit exceeded (HTTP 429)
- `APIException`: Other API errors (HTTP 500, etc.)

## API reference

For complete API documentation, visit [iplocate.io/docs](https://iplocate.io/docs).

## Development

### Install dependencies

```bash
composer install
```

### Run tests

```bash
composer test
```

### Run tests with coverage

```bash
composer test-coverage
```

### Run static analysis

```bash
composer phpstan
```

### Fix code style

```bash
composer cs-fix
```

### Integration tests

Set the `IPLOCATE_API_KEY` environment variable to run integration tests:

```bash
export IPLOCATE_API_KEY=your-api-key
composer test
```

## Examples

See the [examples/](examples/) directory for more detailed usage examples:

- [Basic usage](examples/basic_usage.php)
- [Advanced usage with all features](examples/advanced_usage.php)
- [Custom HTTP client configuration](examples/custom_http_client.php)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## About IPLocate.io

Since 2017, IPLocate has set out to provide the most reliable and accurate IP address data.

We process 50TB+ of data to produce our comprehensive IP geolocation, IP to company, proxy and VPN detection, hosting detection, ASN, and WHOIS data sets. Our API handles over 15 billion requests a month for thousands of businesses and developers.

- Email: [support@iplocate.io](mailto:support@iplocate.io)
- Website: [iplocate.io](https://iplocate.io)
- Documentation: [iplocate.io/docs](https://iplocate.io/docs)
- Sign up for a free API Key: [iplocate.io/signup](https://iplocate.io/signup)
