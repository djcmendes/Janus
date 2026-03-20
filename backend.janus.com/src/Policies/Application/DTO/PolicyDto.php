<?php

declare(strict_types=1);

namespace App\Policies\Application\DTO;

use App\Policies\Domain\Entity\Policy;

final class PolicyDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $description,
        public readonly ?string $icon,
        public readonly bool    $enforceTfa,
        public readonly bool    $adminAccess,
        public readonly bool    $appAccess,
        public readonly ?array  $ipAccess,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Policy $policy): self
    {
        return new self(
            id:          (string) $policy->getId(),
            name:        $policy->getName(),
            description: $policy->getDescription(),
            icon:        $policy->getIcon(),
            enforceTfa:  $policy->isEnforceTfa(),
            adminAccess: $policy->isAdminAccess(),
            appAccess:   $policy->isAppAccess(),
            ipAccess:    $policy->getIpAccess(),
            createdAt:   $policy->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $policy->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
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
            'ip_access'    => $this->ipAccess,
            'created_at'   => $this->createdAt,
            'updated_at'   => $this->updatedAt,
        ];
    }
}
