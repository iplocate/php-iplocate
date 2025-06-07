<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use IPLocate\IPLocate;

// Example of using a custom HTTP client with specific configuration
$customHttpClient = new Client([
    'timeout' => 60.0,
    'verify' => true,
    'headers' => [
        'User-Agent' => 'MyApp/1.0',
    ],
    'proxy' => [
        'http'  => 'tcp://proxy.example.com:8080',
        'https' => 'tcp://proxy.example.com:8080',
    ],
]);

// Create IPLocate client with custom HTTP client
$client = new IPLocate(
    apiKey: 'your-api-key-here',
    httpClient: $customHttpClient
);

try {
    $result = $client->lookup('8.8.8.8');
    echo "Successfully looked up IP: {$result->ip}\n";
    echo "Country: {$result->country}\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}
