<?php

declare(strict_types=1);

namespace App\Roles\Presentation\DTO;

use App\Roles\Application\Command\UpdateRoleCommand;

final class UpdateRoleRequest
{
    public function __construct(
        public readonly ?string $name,
        public readonly mixed   $description,
        public readonly mixed   $icon,
        public readonly ?bool   $enforceTfa,
        public readonly ?bool   $adminAccess,
        public readonly ?bool   $appAccess,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:        isset($data['name'])        ? trim($data['name'])        : null,
            description: array_key_exists('description', $data) ? $data['description'] : UpdateRoleCommand::UNCHANGED,
            icon:        array_key_exists('icon', $data)        ? $data['icon']        : UpdateRoleCommand::UNCHANGED,
            enforceTfa:  isset($data['enforce_tfa'])  ? (bool) $data['enforce_tfa']  : null,
            adminAccess: isset($data['admin_access']) ? (bool) $data['admin_access'] : null,
            appAccess:   isset($data['app_access'])   ? (bool) $data['app_access']   : null,
        );
    }
}
