<?php

declare(strict_types=1);

namespace App\Extensions\Application\DTO;

use App\Extensions\Domain\Entity\Extension;

final class ExtensionDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly string  $type,
        public readonly string  $version,
        public readonly bool    $enabled,
        public readonly ?string $description,
        public readonly ?array  $meta,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public static function fromEntity(Extension $extension): self
    {
        return new self(
            id:          $extension->getId(),
            name:        $extension->getName(),
            type:        $extension->getType()->value,
            version:     $extension->getVersion(),
            enabled:     $extension->isEnabled(),
            description: $extension->getDescription(),
            meta:        $extension->getMeta(),
            createdAt:   $extension->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:   $extension->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
