<?php

/**
 * @file VersionDto.php
 *
 * Read-only data transfer object representing a Version for API responses.
 *
 * @package App\Versions\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Application\DTO;

use App\Versions\Domain\Entity\Version;

/**
 * Immutable DTO carrying all scalar fields of a Version for serialisation into JSON responses.
 */
final class VersionDto
{
    /**
     * @param string               $id         UUID of the version record.
     * @param string               $collection Collection the versioned item belongs to.
     * @param string               $item       UUID/ID of the versioned item.
     * @param string               $key        Human-readable version label.
     * @param array<string, mixed> $data       Full item data snapshot.
     * @param array<string, mixed>|null $delta  Diff against the previous version, or null.
     * @param string|null          $userId     UUID of the creating user, or null.
     * @param string               $createdAt  ISO 8601 creation timestamp.
     * @param string|null          $updatedAt  ISO 8601 last-mutation timestamp, or null.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $key,
        public readonly array   $data,
        public readonly ?array  $delta,
        public readonly ?string $userId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /**
     * Constructs a VersionDto from a domain Version entity.
     *
     * @param  Version    $v The domain entity to convert.
     * @return self           A DTO populated with all fields from the given entity.
     */
    public static function fromEntity(Version $v): self
    {
        return new self(
            id:         $v->getId(),
            collection: $v->getCollection(),
            item:       $v->getItem(),
            key:        $v->getKey(),
            data:       $v->getData(),
            delta:      $v->getDelta(),
            userId:     $v->getUserId(),
            createdAt:  $v->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:  $v->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }
}
