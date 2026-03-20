<?php

declare(strict_types=1);

namespace App\Policies\Presentation\DTO;

use App\Policies\Application\Command\UpdatePolicyCommand;

final class UpdatePolicyRequest
{
    public function __construct(
        public readonly ?string $name,
        public readonly mixed   $description,
        public readonly mixed   $icon,
        public readonly ?bool   $enforceTfa,
        public readonly ?bool   $adminAccess,
        public readonly ?bool   $appAccess,
        public readonly mixed   $ipAccess,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:        isset($data['name'])        ? trim($data['name']) : null,
            description: array_key_exists('description', $data) ? $data['description'] : UpdatePolicyCommand::UNCHANGED,
            icon:        array_key_exists('icon', $data)        ? $data['icon']        : UpdatePolicyCommand::UNCHANGED,
            enforceTfa:  isset($data['enforce_tfa'])  ? (bool) $data['enforce_tfa']  : null,
            adminAccess: isset($data['admin_access']) ? (bool) $data['admin_access'] : null,
            appAccess:   isset($data['app_access'])   ? (bool) $data['app_access']   : null,
            ipAccess:    array_key_exists('ip_access', $data)   ? $data['ip_access']   : UpdatePolicyCommand::UNCHANGED,
        );
    }
}
