<?php

declare(strict_types=1);

namespace App\Deployments\Application\DTO;

use App\Deployments\Domain\Entity\DeploymentProvider;

final class DeploymentProviderDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $url,
        public readonly ?array  $options,
        public readonly bool    $isActive,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(DeploymentProvider $p): self
    {
        return new self(
            id:        (string) $p->getId(),
            name:      $p->getName(),
            type:      $p->getType()->value,
            url:       $p->getUrl(),
            options:   $p->getOptions(),
            isActive:  $p->isActive(),
            createdAt: $p->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $p->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
