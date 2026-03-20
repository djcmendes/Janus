<?php

declare(strict_types=1);

namespace App\Versions\Application\DTO;

use App\Versions\Domain\Entity\Version;

final class VersionDto
{
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

    public static function fromEntity(Version $v): self
    {
        return new self(
            id:         (string) $v->getId(),
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
