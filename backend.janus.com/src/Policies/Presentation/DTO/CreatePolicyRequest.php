<?php

declare(strict_types=1);

namespace App\Policies\Presentation\DTO;

final class CreatePolicyRequest
{
    public function __construct(
        public readonly string  $name,
        public readonly ?string $description = null,
        public readonly ?string $icon        = null,
        public readonly bool    $enforceTfa  = false,
        public readonly bool    $adminAccess = false,
        public readonly bool    $appAccess   = true,
        public readonly ?array  $ipAccess    = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('name is required.');
        }

        return new self(
            name:        trim($data['name']),
            description: isset($data['description']) ? trim($data['description']) : null,
            icon:        isset($data['icon'])        ? trim($data['icon'])        : null,
            enforceTfa:  (bool) ($data['enforce_tfa']  ?? false),
            adminAccess: (bool) ($data['admin_access'] ?? false),
            appAccess:   (bool) ($data['app_access']   ?? true),
            ipAccess:    $data['ip_access'] ?? null,
        );
    }
}
