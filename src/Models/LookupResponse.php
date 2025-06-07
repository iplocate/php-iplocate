<?php

declare(strict_types=1);

namespace IPLocate\Models;

class LookupResponse
{
    public function __construct(
        public string $ip,
        public Privacy $privacy,
        public ?string $country = null,
        public ?string $countryCode = null,
        public bool $isEu = false,
        public ?string $city = null,
        public ?string $continent = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $timeZone = null,
        public ?string $postalCode = null,
        public ?string $subdivision = null,
        public ?string $currencyCode = null,
        public ?string $callingCode = null,
        public ?string $network = null,
        public ?ASN $asn = null,
        public ?Company $company = null,
        public ?Hosting $hosting = null,
        public ?Abuse $abuse = null,
        public mixed $raw = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ip: $data['ip'],
            privacy: Privacy::fromArray($data['privacy']),
            country: $data['country'] ?? null,
            countryCode: $data['country_code'] ?? null,
            isEu: $data['is_eu'] ?? false,
            city: $data['city'] ?? null,
            continent: $data['continent'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            timeZone: $data['time_zone'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            subdivision: $data['subdivision'] ?? null,
            currencyCode: $data['currency_code'] ?? null,
            callingCode: $data['calling_code'] ?? null,
            network: $data['network'] ?? null,
            asn: isset($data['asn']) ? ASN::fromArray($data['asn']) : null,
            company: isset($data['company']) ? Company::fromArray($data['company']) : null,
            hosting: isset($data['hosting']) ? Hosting::fromArray($data['hosting']) : null,
            abuse: isset($data['abuse']) ? Abuse::fromArray($data['abuse']) : null,
            raw: $data,
        );
    }
}
