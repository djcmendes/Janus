<?php

declare(strict_types=1);

namespace App\Permissions\Application\DTO;

use App\Permissions\Domain\Entity\Permission;

final class PermissionDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $policyId,
        public readonly ?string $collection,
        public readonly string  $action,
        public readonly ?array  $fields,
        public readonly ?array  $permissionsFilter,
        public readonly ?array  $validation,
        public readonly ?array  $presets,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Permission $permission): self
    {
        return new self(
            id:                (string) $permission->getId(),
            policyId:          (string) $permission->getPolicy()->getId(),
            collection:        $permission->getCollection(),
            action:            $permission->getAction()->value,
            fields:            $permission->getFields(),
            permissionsFilter: $permission->getPermissionsFilter(),
            validation:        $permission->getValidation(),
            presets:           $permission->getPresets(),
            createdAt:         $permission->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:         $permission->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'policy'              => $this->policyId,
            'collection'          => $this->collection,
            'action'              => $this->action,
            'fields'              => $this->fields,
            'permissions'         => $this->permissionsFilter,
            'validation'          => $this->validation,
            'presets'             => $this->presets,
            'created_at'          => $this->createdAt,
            'updated_at'          => $this->updatedAt,
        ];
    }
}
