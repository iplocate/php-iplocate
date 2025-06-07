<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use IPLocate\Exceptions\AuthenticationException;
use IPLocate\Exceptions\InvalidIPException;
use IPLocate\Exceptions\NotFoundException;
use IPLocate\Exceptions\RateLimitException;
use IPLocate\IPLocate;

// Get your free API key from https://iplocate.io/signup
$apiKey = 'your-api-key-here';

try {
    // Create client with custom configuration
    $client = new IPLocate(
        apiKey: $apiKey,
        timeout: 60.0
    );

    // Look up your own IP
    echo "=== Your IP Information ===\n";
    $selfResult = $client->lookupSelf();
    echo "Your IP: {$selfResult->ip}\n";
    if ($selfResult->country) {
        echo "Country: {$selfResult->country} ({$selfResult->countryCode})\n";
    }
    if ($selfResult->city) {
        echo "City: {$selfResult->city}\n";
    }

    echo "\n=== Google DNS Information ===\n";
    $googleResult = $client->lookup('8.8.8.8');

    // Geographic information
    if ($googleResult->latitude && $googleResult->longitude) {
        echo "Coordinates: {$googleResult->latitude}, {$googleResult->longitude}\n";
    }
    if ($googleResult->timeZone) {
        echo "Timezone: {$googleResult->timeZone}\n";
    }
    if ($googleResult->currencyCode) {
        echo "Currency: {$googleResult->currencyCode}\n";
    }
    if ($googleResult->callingCode) {
        echo "Calling code: +{$googleResult->callingCode}\n";
    }

    // ASN information
    if ($googleResult->asn) {
        echo "\n--- ASN Information ---\n";
        echo "ASN: {$googleResult->asn->asn}\n";
        echo "ISP: {$googleResult->asn->name}\n";
        echo "Network: {$googleResult->asn->route}\n";
        echo "Type: {$googleResult->asn->type}\n";
    }

    // Company information
    if ($googleResult->company) {
        echo "\n--- Company Information ---\n";
        echo "Name: {$googleResult->company->name}\n";
        echo "Domain: {$googleResult->company->domain}\n";
        echo "Type: {$googleResult->company->type}\n";
    }

    // Privacy and threat detection
    echo "\n--- Privacy & Threat Detection ---\n";
    echo 'VPN: ' . ($googleResult->privacy->isVpn ? 'Yes' : 'No') . "\n";
    echo 'Proxy: ' . ($googleResult->privacy->isProxy ? 'Yes' : 'No') . "\n";
    echo 'Tor: ' . ($googleResult->privacy->isTor ? 'Yes' : 'No') . "\n";
    echo 'Hosting: ' . ($googleResult->privacy->isHosting ? 'Yes' : 'No') . "\n";
    echo 'Anonymous: ' . ($googleResult->privacy->isAnonymous ? 'Yes' : 'No') . "\n";

    // Hosting information
    if ($googleResult->hosting) {
        echo "\n--- Hosting Information ---\n";
        if ($googleResult->hosting->provider) {
            echo "Provider: {$googleResult->hosting->provider}\n";
        }
        if ($googleResult->hosting->service) {
            echo "Service: {$googleResult->hosting->service}\n";
        }
    }

    // Abuse contact information
    if ($googleResult->abuse) {
        echo "\n--- Abuse Contact ---\n";
        if ($googleResult->abuse->email) {
            echo "Email: {$googleResult->abuse->email}\n";
        }
        if ($googleResult->abuse->name) {
            echo "Name: {$googleResult->abuse->name}\n";
        }
    }

} catch (InvalidIPException $e) {
    echo "Invalid IP address: {$e->getMessage()}\n";
} catch (AuthenticationException $e) {
    echo "Authentication error: {$e->getMessage()}\n";
} catch (NotFoundException $e) {
    echo "IP not found: {$e->getMessage()}\n";
} catch (RateLimitException $e) {
    echo "Rate limit exceeded: {$e->getMessage()}\n";
} catch (\Exception $e) {
    echo "Unexpected error: {$e->getMessage()}\n";
}
