<?php

declare(strict_types=1);

namespace App\Revisions\Application\DTO;

use App\Revisions\Domain\Entity\Revision;

final class RevisionDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $collection,
        public readonly string  $item,
        public readonly array   $data,
        public readonly ?array  $delta,
        public readonly int     $version,
        public readonly ?string $activityId,
        public readonly string  $createdAt,
    ) {}

    public static function fromEntity(Revision $r): self
    {
        return new self(
            id:         (string) $r->getId(),
            collection: $r->getCollection(),
            item:       $r->getItem(),
            data:       $r->getData(),
            delta:      $r->getDelta(),
            version:    $r->getVersion(),
            activityId: $r->getActivityId(),
            createdAt:  $r->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'collection'  => $this->collection,
            'item'        => $this->item,
            'data'        => $this->data,
            'delta'       => $this->delta,
            'version'     => $this->version,
            'activity'    => $this->activityId,
            'created_at'  => $this->createdAt,
        ];
    }
}
