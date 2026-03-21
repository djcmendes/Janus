<?php

declare(strict_types=1);

namespace App\Policies\Application\DTO;

use App\Policies\Domain\Entity\Access;

final class AccessDto
{
    public function __construct(
        public readonly string  $id,
        public readonly ?string $roleId,
        public readonly string  $policyId,
        public readonly string  $createdAt,
    ) {}

    public static function fromEntity(Access $access): self
    {
        return new self(
            id:        (string) $access->getId(),
            roleId:    $access->getRole() ? (string) $access->getRole()->getId() : null,
            policyId:  (string) $access->getPolicy()->getId(),
            createdAt: $access->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'role'       => $this->roleId,
            'policy'     => $this->policyId,
            'created_at' => $this->createdAt,
        ];
    }
}
