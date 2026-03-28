<?php
declare(strict_types=1);
namespace App\Portals\Application\DTO;

use App\Portals\Domain\Entity\Magnet;

final class MagnetDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $portalId,
        public readonly string  $name,
        public readonly string  $sourceType,
        public readonly array   $sourceConfig,
        public readonly string  $targetCollectionId,
        public readonly ?string $schedule,
        public readonly string  $status,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Magnet $magnet): self
    {
        return new self(
            id:                 $magnet->getId(),
            portalId:           $magnet->getPortalId(),
            name:               $magnet->getName(),
            sourceType:         $magnet->getSourceType()->value,
            sourceConfig:       $magnet->getSourceConfig()->toArray(),
            targetCollectionId: $magnet->getTargetCollectionId(),
            schedule:           $magnet->getSchedule(),
            status:             $magnet->getStatus()->value,
            createdAt:          $magnet->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:          $magnet->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                   => $this->id,
            'portal_id'            => $this->portalId,
            'name'                 => $this->name,
            'source_type'          => $this->sourceType,
            'source_config'        => $this->sourceConfig,
            'target_collection_id' => $this->targetCollectionId,
            'schedule'             => $this->schedule,
            'status'               => $this->status,
            'created_at'           => $this->createdAt,
            'updated_at'           => $this->updatedAt,
        ];
    }
}
