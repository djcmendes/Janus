<?php

declare(strict_types=1);

namespace App\Permissions\Presentation\DTO;

use App\Permissions\Domain\Enum\PermissionAction;

final class CreatePermissionRequest
{
    public function __construct(
        public readonly string  $policyId,
        public readonly string  $action,
        public readonly ?string $collection        = null,
        public readonly ?array  $fields            = null,
        public readonly ?array  $permissionsFilter = null,
        public readonly ?array  $validation        = null,
        public readonly ?array  $presets           = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['policy'])) {
            throw new \InvalidArgumentException('policy (id) is required.');
        }
        if (empty($data['action'])) {
            throw new \InvalidArgumentException('action is required.');
        }

        $allowed = array_column(PermissionAction::cases(), 'value');
        if (!in_array($data['action'], $allowed, true)) {
            throw new \InvalidArgumentException(sprintf('action must be one of: %s.', implode(', ', $allowed)));
        }

        return new self(
            policyId:          $data['policy'],
            action:            $data['action'],
            collection:        $data['collection']   ?? null,
            fields:            $data['fields']       ?? null,
            permissionsFilter: $data['permissions']  ?? null,
            validation:        $data['validation']   ?? null,
            presets:           $data['presets']      ?? null,
        );
    }
}
