<?php

declare(strict_types=1);

namespace IPLocate\Models;

class Privacy
{
    public function __construct(
        public bool $isAbuser,
        public bool $isAnonymous,
        public bool $isBogon,
        public bool $isHosting,
        public bool $isIcloudRelay,
        public bool $isProxy,
        public bool $isTor,
        public bool $isVpn,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isAbuser: $data['is_abuser'],
            isAnonymous: $data['is_anonymous'],
            isBogon: $data['is_bogon'],
            isHosting: $data['is_hosting'],
            isIcloudRelay: $data['is_icloud_relay'],
            isProxy: $data['is_proxy'],
            isTor: $data['is_tor'],
            isVpn: $data['is_vpn'],
        );
    }
}
