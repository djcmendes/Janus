<?php
declare(strict_types=1);
namespace App\Portals\Presentation\DTO;

final class CreateMagnetRequest
{
    public function __construct(
        public readonly string  $name,
        public readonly string  $sourceType,
        public readonly string  $targetCollectionId,
        public readonly ?string $schedule = null,
    ) {}

    /** @throws \InvalidArgumentException */
    public static function fromArray(array $data): self
    {
        if (empty($data['name']))                 { throw new \InvalidArgumentException('name is required.'); }
        if (empty($data['source_type']))          { throw new \InvalidArgumentException('source_type is required.'); }
        if (empty($data['target_collection_id'])) { throw new \InvalidArgumentException('target_collection_id is required.'); }

        return new self(
            name:               trim($data['name']),
            sourceType:         $data['source_type'],
            targetCollectionId: $data['target_collection_id'],
            schedule:           isset($data['schedule']) ? (string) $data['schedule'] : null,
        );
    }
}
