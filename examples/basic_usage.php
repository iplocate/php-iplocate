<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use IPLocate\Exceptions\IPLocateException;
use IPLocate\IPLocate;

// Get your free API key from https://iplocate.io/signup
$apiKey = 'your-api-key-here';

try {
    // Create a new client
    $client = new IPLocate($apiKey);

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
    echo 'Is VPN: ' . ($result->privacy->isVpn ? 'Yes' : 'No') . "\n";
    echo 'Is Proxy: ' . ($result->privacy->isProxy ? 'Yes' : 'No') . "\n";

} catch (IPLocateException $e) {
    echo "Error: {$e->getMessage()}\n";
    echo "HTTP Status: {$e->getCode()}\n";
}
