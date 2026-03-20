<?php

declare(strict_types=1);

namespace App\Roles\Application\DTO;

use App\Roles\Domain\Entity\Role;

final class RoleDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly ?string $icon,
        public readonly bool    $enforceTfa,
        public readonly bool    $adminAccess,
        public readonly bool    $appAccess,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Role $role): self
    {
        return new self(
            id:          (string) $role->getId(),
            name:        $role->getName(),
            description: $role->getDescription(),
            icon:        $role->getIcon(),
            enforceTfa:  $role->isEnforceTfa(),
            adminAccess: $role->isAdminAccess(),
            appAccess:   $role->isAppAccess(),
            createdAt:   $role->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $role->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'icon'         => $this->icon,
            'enforce_tfa'  => $this->enforceTfa,
            'admin_access' => $this->adminAccess,
            'app_access'   => $this->appAccess,
            'created_at'   => $this->createdAt,
            'updated_at'   => $this->updatedAt,
        ];
    }
}
