<?php

declare(strict_types=1);

namespace App\Permissions\Presentation\DTO;

use App\Permissions\Application\Command\UpdatePermissionCommand;
use App\Permissions\Domain\Enum\PermissionAction;

final class UpdatePermissionRequest
{
    public function __construct(
        public readonly ?string $action,
        public readonly mixed   $collection,
        public readonly mixed   $fields,
        public readonly mixed   $permissionsFilter,
        public readonly mixed   $validation,
        public readonly mixed   $presets,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (isset($data['action'])) {
            $allowed = array_column(PermissionAction::cases(), 'value');
            if (!in_array($data['action'], $allowed, true)) {
                throw new \InvalidArgumentException(sprintf('action must be one of: %s.', implode(', ', $allowed)));
            }
        }

        return new self(
            action:            $data['action']      ?? null,
            collection:        array_key_exists('collection',  $data) ? $data['collection']  : UpdatePermissionCommand::UNCHANGED,
            fields:            array_key_exists('fields',      $data) ? $data['fields']      : UpdatePermissionCommand::UNCHANGED,
            permissionsFilter: array_key_exists('permissions', $data) ? $data['permissions'] : UpdatePermissionCommand::UNCHANGED,
            validation:        array_key_exists('validation',  $data) ? $data['validation']  : UpdatePermissionCommand::UNCHANGED,
            presets:           array_key_exists('presets',     $data) ? $data['presets']     : UpdatePermissionCommand::UNCHANGED,
        );
    }
}
