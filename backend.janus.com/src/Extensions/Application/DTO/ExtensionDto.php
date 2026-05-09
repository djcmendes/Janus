<?php

/**
 * @file ExtensionDto.php
 *
 * Read model transferring Extension data across application layer boundaries.
 *
 * @package App\Extensions\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Extensions\Application\DTO;

use App\Extensions\Domain\Entity\Extension;

/**
 * Serialisable read model for a single Extension record.
 *
 * Constructed exclusively via the fromEntity() static factory.
 */
final class ExtensionDto
{
    /**
     * @param string                    $id          UUIDv7 string primary key.
     * @param string                    $name        Package/bundle name.
     * @param string                    $type        ExtensionType enum value string.
     * @param string                    $version     Semantic version string.
     * @param bool                      $enabled     Whether the extension is active.
     * @param string|null               $description Optional human-readable description.
     * @param array<string, mixed>|null $meta        Entry-point configuration, or null.
     * @param string                    $createdAt   ISO 8601 creation timestamp.
     * @param string                    $updatedAt   ISO 8601 last-modification timestamp.
     */
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

    /**
     * Maps an Extension domain entity to a serialisable DTO.
     *
     * @param  Extension     $extension The domain entity to map.
     * @return self                      The populated read model.
     */
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
